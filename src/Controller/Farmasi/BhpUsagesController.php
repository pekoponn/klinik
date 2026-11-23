<?php
declare(strict_types=1);

namespace App\Controller\Farmasi;

use App\Controller\AppController;

/**
 * Pemakaian Non Beban BHP.
 * Stok: stock_balances (per depo + barang). Barang pharmacy_items disambung ke stock_items lewat KODE.
 * Depo disimpan sebagai NAMA depo (stock_warehouses.name).
 */
class BhpUsagesController extends AppController
{
    public function index()
    {
        $from = (string)$this->request->getQuery('date_from', date('Y-m-01'));
        $to = (string)$this->request->getQuery('date_to', date('Y-m-d'));
        $q = trim((string)$this->request->getQuery('q'));
        $depo = trim((string)$this->request->getQuery('depo', ''));

        $query = $this->BhpUsages->find()
            ->where(['is_active' => 'Y', 'usage_date >=' => $from, 'usage_date <=' => $to])
            ->orderBy(['usage_date' => 'DESC', 'id' => 'DESC']);

        if ($depo !== '') {
            $query->where(['depo' => $depo]);
        }

        if ($q !== '') {
            $query->where(['OR' => [
                'usage_no LIKE' => "%$q%",
                'patient_name LIKE' => "%$q%",
                'medical_record_no LIKE' => "%$q%",
            ]]);
        }

        $usages = $this->paginate($query, ['limit' => 25]);
        $this->set(compact('usages', 'from', 'to', 'q', 'depo'));
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $d = $this->request->getData();
            $depo = trim((string)($d['depo'] ?? ''));
            $back = ['action' => 'add', '?' => ['depo' => $depo]];

            if ($depo === '') {
                $this->Flash->error('Pilih depo dulu.');
                return $this->redirect(['action' => 'add']);
            }

            // Hitung ulang di server, angka dari browser tidak dipercaya
            $items = [];
            $subtotal = $discTotal = $taxTotal = 0.0;
            foreach ((array)($d['items'] ?? []) as $r) {
                $name = trim((string)($r['item_name'] ?? ''));
                $qty = (float)($r['qty'] ?? 0);
                if ($name === '' || $qty <= 0) {
                    continue;
                }
                $code = trim((string)($r['item_code'] ?? ''));
                if ($code === '') {
                    $this->Flash->error('Barang "' . $name . '" harus dipilih dari daftar pencarian.');
                    return $this->redirect($back);
                }

                $price = (float)($r['price'] ?? 0);
                $discPct = min(100.0, max(0.0, (float)($r['disc_percent'] ?? 0)));
                $ppnPct = max(0.0, (float)($r['ppn_percent'] ?? 0));

                $gross = $qty * $price;
                $discAmt = $gross * $discPct / 100;
                $ppnAmt = ($gross - $discAmt) * $ppnPct / 100;

                $subtotal += $gross;
                $discTotal += $discAmt;
                $taxTotal += $ppnAmt;

                $items[] = [
                    'item_id' => !empty($r['item_id']) ? $r['item_id'] : null,
                    'item_code' => $code,
                    'item_name' => $name,
                    'qty' => $qty,
                    'unit' => $r['unit'] ?? null,
                    'price' => $price,
                    'disc_percent' => $discPct,
                    'ppn_percent' => $ppnPct,
                    'ppn_amount' => $ppnAmt,
                    'total' => $gross - $discAmt + $ppnAmt,
                    'expired_date' => !empty($r['expired_date']) ? $r['expired_date'] : null,
                    'usage_rule' => $r['usage_rule'] ?? null,
                    'shelf_no' => $r['shelf_no'] ?? null,
                ];
            }

            if (!$items) {
                $this->Flash->error('Isi minimal satu barang.');
                return $this->redirect($back);
            }

            $buyer = ($d['buyer_type'] ?? 'pasien') === 'unit' ? 'unit' : 'pasien';
            $isPatient = $buyer === 'pasien';

            $usage = $this->BhpUsages->newEntity([
                'usage_no' => $this->nextUsageNo(),
                'usage_date' => $d['usage_date'] ?: date('Y-m-d'),
                'usage_time' => $d['usage_time'] ?: date('H:i:s'),
                'depo' => $depo,
                'queue_no' => (int)($d['queue_no'] ?? 0),
                'buyer_type' => $buyer,
                'unit_name' => $isPatient ? null : ($d['unit_name'] ?? null),
                'registration_id' => $isPatient ? ($d['registration_id'] ?: null) : null,
                'registration_no' => $isPatient ? ($d['registration_no'] ?? null) : null,
                'medical_record_no' => $isPatient ? ($d['medical_record_no'] ?? null) : null,
                'patient_name' => $isPatient ? ($d['patient_name'] ?? null) : null,
                'origin' => $d['origin'] ?? null,
                'class_name' => $d['class_name'] ?? null,
                'doctor_name' => $d['doctor_name'] ?? null,
                'phone' => $d['phone'] ?? null,
                'subtotal' => $subtotal,
                'discount_total' => $discTotal,
                'tax_total' => $taxTotal,
                'grand_total' => $subtotal - $discTotal + $taxTotal,
                'bhp_usage_items' => $items,
            ], ['associated' => ['BhpUsageItems']]);

            // Simpan pemakaian + potong stok dalam SATU transaksi: gagal salah satu, semua dibatalkan
            $saved = false;
            try {
                $saved = $this->BhpUsages->getConnection()->transactional(
                    function () use ($usage, $items, $depo) {
                        if (!$this->BhpUsages->save($usage)) {
                            return false;
                        }
                        foreach ($items as $it) {
                            $this->issueStock($depo, (string)$it['item_code'], (string)$it['item_name'], (float)$it['qty']);
                        }
                        return true;
                    }
                );
            } catch (\RuntimeException $e) {
                $this->Flash->error($e->getMessage());
                return $this->redirect($back);
            }

            if ($saved) {
                $this->Flash->success('Pemakaian BHP tersimpan: ' . $usage->usage_no);
                return $this->redirect(['action' => 'index', '?' => ['depo' => $depo]]);
            }
            $this->Flash->error('Gagal menyimpan.');
            return $this->redirect($back);
        }

        $defaultDepo = trim((string)$this->request->getQuery('depo', ''));
        $nextNo = $this->nextUsageNo();

        // Daftar depo dari master gudang stok
        $depos = $this->stockTable('StockWarehouses', 'stock_warehouses')->find()
            ->select(['name'])
            ->where(['is_active' => 1])
            ->orderBy(['name' => 'ASC'])
            ->all()->extract('name')->toList();

        $units = ['LABORATORIUM', 'INSTALASI GAWAT DARURAT', 'RAWAT INAP', 'RAWAT JALAN'];
        $this->set(compact('nextNo', 'depos', 'units', 'defaultDepo'));
    }

    public function view($id = null)
    {
        $usage = $this->BhpUsages->get((int)$id, contain: ['BhpUsageItems']);
        $this->set(compact('usage'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post']);
        $u = $this->BhpUsages->get((int)$id, contain: ['BhpUsageItems']);

        if ($u->is_active !== 'Y') {
            $this->Flash->error('Data sudah dihapus.');
            return $this->redirect(['action' => 'index']);
        }

        $ok = $this->BhpUsages->getConnection()->transactional(function () use ($u) {
            // Kembalikan stok
            foreach ($u->bhp_usage_items as $it) {
                if (!empty($it->item_code)) {
                    $this->returnStock((string)$u->depo, (string)$it->item_code, (float)$it->qty);
                }
            }
            $u->is_active = 'N';
            $u->is_delete = 1;
            return (bool)$this->BhpUsages->save($u, ['associated' => false]);
        });

        $ok ? $this->Flash->success('Data dihapus, stok dikembalikan.') : $this->Flash->error('Gagal menghapus.');
        return $this->redirect(['action' => 'index', '?' => ['depo' => $u->depo]]);
    }

    /**
     * Autocomplete barang: hanya barang yang PUNYA STOK di depo terpilih.
     * GET: q, depo
     */
    public function searchItems()
    {
        $this->request->allowMethod(['get']);
        $q = trim((string)$this->request->getQuery('q'));
        $depo = trim((string)$this->request->getQuery('depo'));
        $rows = [];

        if (mb_strlen($q) >= 2 && $depo !== '') {
            $tbl = $this->stockTable('StockBalances', 'stock_balances');
            $query = $tbl->find();
            $query->select([
                    'id' => 'p.id',
                    'code' => 'p.code',
                    'name' => 'p.name',
                    'unit' => 'p.sale_unit',
                    'price' => 'p.sale_price', // ganti ke 'p.hpp' bila pemakaian internal dihitung harga pokok
                    'stock' => $query->newExpr(
                        'StockBalances.so_qty + StockBalances.adjustment_qty + StockBalances.received_qty - StockBalances.issued_qty'
                    ),
                ])
                ->innerJoin(['si' => 'stock_items'], 'si.id = StockBalances.item_id')
                ->innerJoin(['w' => 'stock_warehouses'], 'w.id = StockBalances.warehouse_id')
                ->innerJoin(
                    ['p' => 'pharmacy_items'],
                    'p.code COLLATE utf8mb4_general_ci = si.code COLLATE utf8mb4_general_ci'
                )
                ->where([
                    'w.name' => $depo,
                    'p.is_active' => 'Y',
                    'p.is_delete' => 0,
                    'OR' => ['p.name LIKE' => "%$q%", 'p.code LIKE' => "%$q%"],
                ])
                ->orderBy(['p.name' => 'ASC'])
                ->limit(20);

            foreach ($query->disableHydration()->all() as $r) {
                $r['stock'] = (float)$r['stock'];
                $rows[] = $r;
            }
        }

        return $this->response->withType('application/json')
            ->withStringBody(json_encode($rows, JSON_UNESCAPED_UNICODE));
    }

    // Autocomplete registrasi pasien
    public function searchRegistration()
    {
        $this->request->allowMethod(['get']);
        $q = trim((string)$this->request->getQuery('q'));
        $rows = [];
        if (mb_strlen($q) >= 2) {
            $rows = $this->fetchTable('Registrations')->find()
                ->select([
                    'id' => 'Registrations.id',
                    'registration_no' => 'Registrations.registration_no',
                    'service_type' => 'Registrations.service_type',
                    'class_name' => 'Registrations.class_name',
                    'rm' => 'p.code',
                    'patient_name' => 'p.full_name',
                    'doctor_name' => 'd.doctor_name',
                ])
                ->join([
                    'p' => ['table' => 'patients', 'type' => 'LEFT', 'conditions' => 'p.id = Registrations.patient_id'],
                    'd' => ['table' => 'doctors', 'type' => 'LEFT', 'conditions' => 'd.id = Registrations.doctor_id'],
                ])
                ->where(['OR' => [
                    'Registrations.registration_no LIKE' => "%$q%",
                    'p.full_name LIKE' => "%$q%",
                    'p.code_hash' => \App\Utility\DataCipher::hash($q),
                ]])
                ->orderBy(['Registrations.id' => 'DESC'])
                ->limit(15)->disableHydration()->all()->toList();
            foreach ($rows as &$row) {
                $row['rm'] = \App\Utility\DataCipher::decrypt($row['rm'] ?? null);
            }
            unset($row);
        }
        return $this->response->withType('application/json')->withStringBody(json_encode($rows));
    }

    // =====================================================================
    // STOK
    // =====================================================================

    /** Tabel stok tanpa file Table terpisah */
    private function stockTable(string $alias, string $table)
    {
        return $this->fetchTable($alias, [
            'className' => \Cake\ORM\Table::class,
            'table' => $table,
        ]);
    }

    /** Baris stok_balances untuk (depo, kode barang). $lock = kunci baris selama transaksi. */
    private function findBalance(string $depo, string $itemCode, bool $lock = false)
    {
        $query = $this->stockTable('StockBalances', 'stock_balances')->find()
            ->innerJoin(['si' => 'stock_items'], 'si.id = StockBalances.item_id')
            ->innerJoin(['w' => 'stock_warehouses'], 'w.id = StockBalances.warehouse_id')
            ->where(['si.code' => $itemCode, 'w.name' => $depo]);

        if ($lock) {
            $query->epilog('FOR UPDATE');
        }

        return $query->first();
    }

    /** Potong stok: issued_qty += qty. Ditolak bila stok tidak cukup. */
    private function issueStock(string $depo, string $itemCode, string $itemName, float $qty): void
    {
        $bal = $this->findBalance($depo, $itemCode, true);
        if (!$bal) {
            throw new \RuntimeException("Barang $itemName ($itemCode) tidak punya stok di $depo.");
        }

        // Saldo = SO + Adjustment + Terima - Keluar
        $available = (float)$bal->so_qty + (float)$bal->adjustment_qty + (float)$bal->received_qty - (float)$bal->issued_qty;
        if ($available + 0.00001 < $qty) {
            throw new \RuntimeException(
                "Stok $itemName di $depo tidak cukup (tersedia " . $this->fmtQty($available) . ', diminta ' . $this->fmtQty($qty) . ').'
            );
        }

        $bal->issued_qty = (float)$bal->issued_qty + $qty;
        if (!$this->stockTable('StockBalances', 'stock_balances')->save($bal)) {
            throw new \RuntimeException("Gagal memperbarui stok $itemName.");
        }
    }

    /** Kembalikan stok saat transaksi dihapus: issued_qty -= qty (tidak di bawah 0). */
    private function returnStock(string $depo, string $itemCode, float $qty): void
    {
        $bal = $this->findBalance($depo, $itemCode, true);
        if (!$bal) {
            return; // baris stok sudah tidak ada, tidak ada yang dikembalikan
        }

        $bal->issued_qty = max(0.0, (float)$bal->issued_qty - $qty);
        $this->stockTable('StockBalances', 'stock_balances')->save($bal);
    }

    private function fmtQty(float $n): string
    {
        return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
    }

    /** Nomor transaksi berikutnya: BHP2026-00000001 */
    private function nextUsageNo(): string
    {
        $last = (int)($this->BhpUsages->find()
            ->select(['id'])
            ->orderBy(['id' => 'DESC'])
            ->first()?->id ?? 0);

        return 'BHP' . date('Y') . '-' . str_pad((string)($last + 1), 8, '0', STR_PAD_LEFT);
    }
}