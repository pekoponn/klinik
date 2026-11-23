<?php
/**
 * Simpan di: templates/Penunjang/Radiology/index.php
 */
?>
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Penunjang</a></li>
    <li class="breadcrumb-item active">Radiologi</li>
</ol>
<h1 class="page-header">Radiologi <small>USG dan Elektromedik</small></h1>

<div class="row">

    <!-- ===== MENU KIRI ===== -->
    <div class="col-lg-2 col-md-3">
        <div class="panel panel-inverse">
            <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-bars"></i> Menu</h4></div>
            <div class="panel-body p-2">
                <a href="javascript:;" class="btn btn-default btn-block text-left mb-1"><i class="fa fa-user-md"></i> Nama Petugas</a>
                <a href="javascript:;" class="btn btn-default btn-block text-left mb-1"><i class="fa fa-th-list"></i> Isi Nilai Normal</a>
                <?= $this->Html->link('<i class="fa fa-x-ray"></i> Tindakan', ['action' => 'tindakan'], ['class' => 'btn btn-default btn-block text-left mb-1', 'escape' => false]) ?>
               <a href="javascript:;" class="btn btn-default btn-block text-left" data-toggle="modal" data-target="#modalBiaya"><i class="fa fa-arrow-circle-right"></i> Informasi Biaya Pasien</a>
            </div>
        </div>

        <div class="panel panel-inverse">
            <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-boxes"></i> Persediaan</h4></div>
            <div class="panel-body p-2">
                <?= $this->Html->link('<i class="fa fa-dolly"></i> Persediaan Tidak Beban Pasien', ['prefix' => 'Farmasi', 'controller' => 'BhpUsages', 'action' => 'index', '?' => ['depo' => 'DEPO RADIOLOGI']], ['class' => 'btn btn-default btn-block text-left mb-1', 'escape' => false]) ?>
                <?= $this->Html->link('<i class="fa fa-warehouse"></i> Informasi Stock', ['prefix' => 'Penunjang', 'controller' => 'LabStocks', 'action' => 'index'], ['class' => 'btn btn-default btn-block text-left mb-1', 'escape' => false]) ?>
                <?= $this->Html->link('<i class="fa fa-dolly"></i> Pemakaian Logistik', ['prefix' => 'Penunjang', 'controller' => 'LabUsages', 'action' => 'add'], ['class' => 'btn btn-default btn-block text-left mb-1', 'escape' => false]) ?>
                <?= $this->Html->link('<i class="fa fa-boxes"></i> Daftar Stock Logistik', ['prefix' => 'Farmasi', 'controller' => 'LogisticStocks', 'action' => 'index'], ['class' => 'btn btn-default btn-block text-left mb-1', 'escape' => false]) ?>
            </div>
        </div>

        <a href="javascript:;" class="btn btn-info btn-block mb-1"><i class="fa fa-file-alt"></i> Laporan</a>
        <?= $this->Html->link('<i class="fa fa-sign-out-alt"></i> Keluar', ['prefix' => false, 'controller' => 'Dashboard', 'action' => 'index'], ['class' => 'btn btn-danger btn-block', 'escape' => false]) ?>
    </div>

    <!-- ===== KONTEN KANAN ===== -->
    <div class="col-lg-10 col-md-9">

        <!-- Info petugas -->
        <div class="panel panel-inverse">
            <div class="panel-body py-2">
                <div class="row text-center">
                    <div class="col-md-4"><b>Petugas :</b> <?= h($officer) ?></div>
                    <div class="col-md-4"><b>Shift :</b> 0</div>
                    <div class="col-md-4"><b>Jam :</b> <span id="rad-clock">--:--:--</span></div>
                </div>
            </div>
        </div>

        <!-- Permohonan Pemeriksaan Radiologi (dari E-Order tab Radiologi) -->
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-inbox"></i> Permohonan Pemeriksaan Radiologi</h4>
            </div>
            <div class="panel-body p-0">
                <div class="table-responsive" style="max-height:240px; overflow-y:auto;">
                    <table class="table table-bordered table-hover table-sm mb-0">
                        <thead class="thead-dark" style="position:sticky; top:0;">
                            <tr>
                                <th>No Daftar</th>
                                <th>Rek Medis</th>
                                <th>Tgl Masuk</th>
                                <th>Jam</th>
                                <th>Nama Pasien</th>
                                <th>Dr Pengirim</th>
                                <th>Asal</th>
                                <th>Pesan Dokter</th>
                                <th class="text-center" style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-2x d-block mb-2"></i>
                                    Belum ada permohonan pemeriksaan radiologi
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $rq):
                                $ts = !empty($rq['tgl_order']) ? strtotime((string)$rq['tgl_order']) : null;
                                $asal = ['rawat_jalan' => 'Rawat Jalan', 'rawat_inap' => 'Rawat Inap', 'igd' => 'IGD'][$rq['service_type'] ?? ''] ?? '-';
                            ?>
                            <tr>
                                <td><code><?= h($rq['no_order']) ?></code></td>
                                <td><?= h($rq['rm']) ?></td>
                                <td><?= $ts ? date('d-m-Y', $ts) : '-' ?></td>
                                <td><?= $ts ? date('H:i', $ts) : '-' ?></td>
                                <td><b><?= h($rq['patient_name']) ?></b><br><small class="text-muted"><?= h($rq['rad_names']) ?></small></td>
                                <td><?= h($rq['doctor_name']) ?></td>
                                <td><?= h($asal) ?></td>
                                <td><?= h($rq['pesan']) ?></td>
                                <td class="text-center">
                                    <?= $this->Form->postLink(
                                        '<i class="fa fa-play"></i> Proses',
                                        ['action' => 'proses', $rq['id']],
                                        ['class' => 'btn btn-xs btn-success', 'escape' => false, 'confirm' => 'Proses permohonan radiologi ini?']
                                    ) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pasien Radiologi -->
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-x-ray"></i> Pasien Radiologi</h4>
            </div>
            <div class="panel-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="form-row align-items-end">
                    <div class="col-md-2">
                        <label class="mb-1"><i class="fa fa-search"></i> Cari Pasien</label>
                        <?= $this->Form->select('by', [
                            'patient_name' => 'Nama Pasien',
                            'medical_record_no' => 'No RM',
                            'rad_no' => 'No Transaksi',
                        ], ['value' => $by, 'class' => 'form-control', 'empty' => false]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $this->Form->text('q', ['value' => $q, 'class' => 'form-control', 'placeholder' => 'Ketik kata kunci...']) ?>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1"><i class="fa fa-calendar"></i> Pemeriksaan tanggal &gt;=</label>
                        <?= $this->Form->date('date_from', ['value' => $dateFrom, 'class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i> Cari</button>
                        <?= $this->Html->link('<i class="fa fa-sync"></i> Refresh Pasien', ['action' => 'index'], ['class' => 'btn btn-default', 'escape' => false]) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>

            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No Tr</th>
                                <th>No Reg</th>
                                <th>Tgl Tr</th>
                                <th>Jam</th>
                                <th>Nama Pasien</th>
                                <th>Pemeriksaan</th>
                                <th>Status</th>
                                <th class="text-center" style="width:230px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($examinations->count() === 0): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data pemeriksaan radiologi</td></tr>
                        <?php endif; ?>
                        <?php foreach ($examinations as $e):
                            $badge = ['received' => 'badge-warning', 'processing' => 'badge-info', 'done' => 'badge-success'][$e->status] ?? 'badge-secondary';
                            $label = ['received' => 'Diterima', 'processing' => 'Diproses', 'done' => 'Selesai'][$e->status] ?? $e->status;
                        ?>
                            <tr>
                                <td><code><?= h($e->rad_no) ?></code></td>
                                <td><?= h($e->registration_no ?: '-') ?></td>
                                <td><?= $e->received_at ? $e->received_at->format('d-m-Y') : '-' ?></td>
                                <td><?= $e->received_at ? $e->received_at->format('H:i:s') : '-' ?></td>
                                <td><b><?= h($e->patient_name) ?></b><br><small class="text-muted"><?= h($e->medical_record_no) ?> &middot; <?= h($e->patient_type) ?></small></td>
                                <td><small><?= h($e->no_order) ?></small></td>
                                <td><span class="badge <?= $badge ?>"><?= h($label) ?></span></td>
                                <td class="text-center">
                                    <?= $this->Html->link('<i class="fa fa-x-ray"></i> Tindakan', ['action' => 'tindakan', $e->id], ['class' => 'btn btn-xs btn-primary', 'escape' => false]) ?>
                                    <a href="javascript:;" class="btn btn-xs btn-default" title="Isi hasil radiologi"><i class="fa fa-file-medical"></i> Hasil</a>
                                    <a href="javascript:;" class="btn btn-xs btn-warning btn-biaya" title="Informasi biaya pasien"
                                    data-reg="<?= h($e->registration_no) ?>"
                                    data-nama="<?= h($e->patient_name) ?>"
                                    data-rm="<?= h($e->medical_record_no) ?>"><i class="fa fa-dollar-sign"></i> Biaya</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel-footer d-flex justify-content-between align-items-center">
                <small class="text-muted"><?= $this->Paginator->counter('Halaman {{page}} dari {{pages}}, total {{count}} data') ?></small>
                <ul class="pagination pagination-sm mb-0">
                    <?= $this->Paginator->prev('« Sebelumnya') ?>
                    <?= $this->Paginator->next('Berikutnya »') ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/biaya_modal.php'; ?>

<script>
(function () {
    var el = document.getElementById('rad-clock');
    function tick() { el.textContent = new Date().toLocaleTimeString('id-ID', {hour12: false}); }
    tick(); setInterval(tick, 1000);
})();
</script>