<?php
/**
 * Simpan di: templates/Penunjang/Radiology/tindakan.php
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RadiologyExamination|null $exam
 * @var array $doctors
 * @var string $officer
 */
use Cake\I18n\FrozenTime;

$isEdit = !empty($exam) && !empty($exam->id);
$v = function (string $f, $default = '') use ($exam) {
    return ($exam && isset($exam->{$f}) && $exam->{$f} !== null) ? $exam->{$f} : $default;
};

$at = ($isEdit && $exam->received_at) ? $exam->received_at : FrozenTime::now();
$birth = $v('birth_date', '');
if (is_object($birth)) {
    $birth = $birth->format('Y-m-d');
}
$origin = $v('patient_origin', 'rs');
$reqType = $v('request_type', 'dikirim_dr');
$sysType = $v('system_type', 'rs');
$gender = $v('gender', '');

$itemsData = [];
$supsData = [];
if ($isEdit) {
    foreach (($exam->radiology_examination_items ?? []) as $it) {
        $itemsData[] = [
            'id' => $it->id, 'code' => $it->code, 'name' => $it->examination_name, 'exam_type' => $it->exam_type,
            'photo_no' => $it->photo_no, 'tariff' => (float)$it->tariff, 'doctor_fee' => (float)$it->doctor_fee,
            'incentive' => (float)$it->incentive, 'insurance_cover' => (float)$it->insurance_cover, 'jpk' => (float)$it->jpk,
            'disc_rs' => (float)$it->disc_rs, 'disc_dr' => (float)$it->disc_dr, 'outside_doctor_fee' => (float)$it->outside_doctor_fee,
        ];
    }
    foreach (($exam->radiology_examination_supplies ?? []) as $s) {
        $supsData[] = [
            'item_id' => $s->item_id, 'code' => $s->code, 'name' => $s->name, 'qty' => (float)$s->qty, 'unit' => $s->unit,
            'price' => (float)$s->price, 'location' => $s->location, 'charged' => (int)$s->charged_to_patient,
        ];
    }
}
$jf = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;
?>
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Penunjang</a></li>
    <li class="breadcrumb-item"><?= $this->Html->link('Radiologi', ['action' => 'index']) ?></li>
    <li class="breadcrumb-item active">Tindakan</li>
</ol>
<h1 class="page-header">Radiologi <small>Tindakan Unit Radiologi</small></h1>

<?= $this->Form->create(null, [
    'url' => ['action' => 'saveTindakan', $isEdit ? $exam->id : null],
    'id' => 'form-tindakan',
    'novalidate' => true,
]) ?>
<input type="hidden" name="registration_id" id="registration_id" value="<?= h($v('registration_id')) ?>">
<input type="hidden" name="patient_id" id="patient_id" value="<?= h($v('patient_id')) ?>">

<!-- ===================== DATA PASIEN & PEMERIKSAAN ===================== -->
<div class="panel panel-inverse">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-x-ray"></i> Data Pasien &amp; Pemeriksaan</h4>
    </div>
    <div class="panel-body">
        <div class="mb-3">
            <label class="mr-4"><input type="radio" name="patient_origin" value="rs" <?= $origin === 'rs' ? 'checked' : '' ?>> <b>Pasien RS</b></label>
            <label><input type="radio" name="patient_origin" value="luar" <?= $origin === 'luar' ? 'checked' : '' ?>> <b>Pasien Luar</b></label>
        </div>

        <div class="row">
            <!-- KIRI -->
            <div class="col-lg-6">
                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">No. Periksa</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" readonly value="<?= h($v('rad_no', 'AUTO')) ?>">
                    </div>
                    <label class="col-form-label col-md-1 text-right">Tgl</label>
                    <div class="col-md-2 p-l-0"><input type="date" name="tgl" id="tgl" class="form-control form-control-sm" value="<?= h($at->format('Y-m-d')) ?>"></div>
                    <div class="col-md-2 p-l-0"><input type="time" step="1" name="jam" class="form-control form-control-sm" value="<?= h($at->format('H:i:s')) ?>"></div>
                </div>

                <div class="form-group row m-b-10 rs-only">
                    <label class="col-form-label col-md-3">No Registrasi</label>
                    <div class="col-md-9">
                        <div class="input-group input-group-sm">
                            <input type="text" name="registration_no" id="registration_no" class="form-control" autocomplete="off"
                                   placeholder="Ketik no registrasi / nama / No RM..." value="<?= h($v('registration_no')) ?>">
                            <div class="input-group-append"><button type="button" class="btn btn-default" id="btn-find-reg"><i class="fa fa-search"></i></button></div>
                        </div>
                    </div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Rek. Medis</label>
                    <div class="col-md-9"><input type="text" name="medical_record_no" id="medical_record_no" class="form-control form-control-sm" value="<?= h($v('medical_record_no')) ?>"></div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Nama Pasien</label>
                    <div class="col-md-9"><input type="text" name="patient_name" id="patient_name" class="form-control form-control-sm" value="<?= h($v('patient_name')) ?>"></div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Tgl Lahir</label>
                    <div class="col-md-4"><input type="date" name="birth_date" id="birth_date" class="form-control form-control-sm" value="<?= h($birth) ?>"></div>
                    <label class="col-form-label col-md-1 text-right">Umur</label>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <input type="text" id="age_y" class="form-control text-center" readonly value="0"><div class="input-group-append input-group-prepend"><span class="input-group-text">TH</span></div>
                            <input type="text" id="age_m" class="form-control text-center" readonly value="0"><div class="input-group-append input-group-prepend"><span class="input-group-text">BL</span></div>
                            <input type="text" id="age_d" class="form-control text-center" readonly value="0"><div class="input-group-append"><span class="input-group-text">HR</span></div>
                        </div>
                    </div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">J. Kelamin</label>
                    <div class="col-md-9 pt-1">
                        <label class="mr-3"><input type="radio" name="gender" value="L" <?= $gender === 'L' ? 'checked' : '' ?>> Pria</label>
                        <label><input type="radio" name="gender" value="P" <?= $gender === 'P' ? 'checked' : '' ?>> Wanita</label>
                    </div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Dari</label>
                    <div class="col-md-9"><input type="text" name="patient_type" id="patient_type" class="form-control form-control-sm" value="<?= h($v('patient_type')) ?>"></div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Label Barcode</label>
                    <div class="col-md-6"><input type="text" name="barcode_label" class="form-control form-control-sm" placeholder="Scan barcode di sini" value="<?= h($v('barcode_label')) ?>"></div>
                    <div class="col-md-3"><?= $this->Html->link('<i class="fa fa-inbox"></i> E-Order', ['action' => 'index'], ['class' => 'btn btn-sm btn-default btn-block', 'escape' => false]) ?></div>
                </div>
            </div>

            <!-- KANAN -->
            <div class="col-lg-6">
                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Kelas</label>
                    <div class="col-md-9"><input type="text" name="class_name" class="form-control form-control-sm" value="<?= h($v('class_name')) ?>"></div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Dr. Pembaca</label>
                    <div class="col-md-9">
                        <?= $this->Form->select('reader_doctor_id', $doctors, ['empty' => '-- Pilih Dokter --', 'value' => $v('reader_doctor_id', ''), 'class' => 'form-control form-control-sm']) ?>
                    </div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Pengirim</label>
                    <div class="col-md-5">
                        <?= $this->Form->select('sender_doctor_id', $doctors, ['empty' => '-- Dokter RS --', 'value' => $v('sender_doctor_id', ''), 'class' => 'form-control form-control-sm']) ?>
                    </div>
                    <div class="col-md-4 p-l-0"><input type="text" name="sender_name" class="form-control form-control-sm" placeholder="Dokter luar / ket." value="<?= h($v('sender_name')) ?>"></div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Petugas</label>
                    <div class="col-md-9"><input type="text" name="officer_name" class="form-control form-control-sm" value="<?= h($v('officer_name', $officer)) ?>"></div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">Jenis</label>
                    <div class="col-md-9 pt-1">
                        <label class="mr-3"><input type="radio" name="request_type" value="dikirim_dr" <?= $reqType === 'dikirim_dr' ? 'checked' : '' ?>> Dikirim Dr</label>
                        <label><input type="radio" name="request_type" value="atas_permintaan_sendiri" <?= $reqType === 'atas_permintaan_sendiri' ? 'checked' : '' ?>> Atas Permintaan Sendiri</label>
                    </div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">System</label>
                    <div class="col-md-9 pt-1">
                        <label class="mr-3"><input type="radio" name="system_type" value="rs" <?= $sysType === 'rs' ? 'checked' : '' ?>> Radiologi RS</label>
                        <label><input type="radio" name="system_type" value="dirujuk" <?= $sysType === 'dirujuk' ? 'checked' : '' ?>> Dirujuk</label>
                    </div>
                </div>

                <div class="form-group row m-b-10">
                    <label class="col-form-label col-md-3">E-Order</label>
                    <div class="col-md-4"><input type="text" class="form-control form-control-sm" readonly value="<?= h($v('no_order')) ?>"></div>
                    <div class="col-md-5 p-l-0"><input type="text" name="eorder_note" class="form-control form-control-sm" placeholder="Catatan e-Order" value="<?= h($v('eorder_note')) ?>"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================== TINDAKAN ===================== -->
<div class="panel panel-inverse">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-list"></i> Tindakan Radiologi</h4>
    </div>
    <div class="panel-body">
        <div class="form-row align-items-center mb-2">
            <div class="col-md-5">
                <input type="text" id="master-search" class="form-control form-control-sm" autocomplete="off" placeholder="Ketik kode / nama pemeriksaan radiologi...">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-default" id="btn-add-tx-blank"><i class="fa fa-plus"></i> Baris Manual</button>
            </div>
            <div class="col-md-3 offset-md-2">
                <input type="text" id="photo-search" class="form-control form-control-sm" placeholder="Cari No Photo + Enter">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0" id="tx-table" style="font-size:12px; min-width:1500px;">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:30px;">No</th>
                        <th style="width:100px;">Kode</th>
                        <th style="min-width:230px;">Nama Pemeriksaan</th>
                        <th style="width:110px;">Jenis</th>
                        <th style="width:100px;">No. Foto</th>
                        <th>Tarif R.S</th>
                        <th>Jasa Dokter</th>
                        <th>Insentif</th>
                        <th>Askes/Dicover</th>
                        <th>JPK</th>
                        <th>Disk RS</th>
                        <th>Disk DR</th>
                        <th>Dokter Luar</th>
                        <th>Total</th>
                        <th style="width:32px;"></th>
                    </tr>
                </thead>
                <tbody id="tx-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===================== ALKES / BHP ===================== -->
<div class="panel panel-inverse">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-boxes"></i> Pemakaian Alkes / BHP</h4>
    </div>
    <div class="panel-body">
        <div class="form-row align-items-center mb-2">
            <div class="col-md-5">
                <input type="text" id="item-search" class="form-control form-control-sm" autocomplete="off" placeholder="Ketik kode / nama alkes / obat...">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-default" id="btn-add-sup-blank"><i class="fa fa-plus"></i> Baris Manual</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0" style="font-size:12px; min-width:1000px;">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:100px;">Kode</th>
                        <th style="min-width:220px;">Nama Alkes / Obat</th>
                        <th style="width:90px;">Qty</th>
                        <th style="width:90px;">Satuan</th>
                        <th style="width:120px;">Harga</th>
                        <th style="width:120px;">Total Harga</th>
                        <th style="width:130px;">Lok / Unit</th>
                        <th style="width:110px;" class="text-center">Dibebankan ke Pasien</th>
                        <th style="width:32px;"></th>
                    </tr>
                </thead>
                <tbody id="sup-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===================== TOTAL & AKSI ===================== -->
<div class="panel panel-inverse">
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-5">
                <table class="table table-sm mb-0">
                    <tr><td>Total Tindakan</td><td class="text-right">Rp <span id="sum-tx">0</span></td></tr>
                    <tr><td>Total Alkes/BHP (dibebankan)</td><td class="text-right">Rp <span id="sum-sup">0</span></td></tr>
                    <tr class="font-weight-bold"><td>Total</td><td class="text-right">Rp <span id="sum-all">0</span></td></tr>
                </table>
            </div>
            <div class="col-lg-7 text-right">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                <a href="javascript:;" class="btn btn-default <?= $isEdit ? '' : 'disabled' ?>" title="Form hasil belum dibuat"><i class="fa fa-file-medical"></i> Isi Hasil</a>
                <button type="button" class="btn btn-default btn-print"><i class="fa fa-print"></i> Cetak Slip Detail</button>
                <button type="button" class="btn btn-default btn-print"><i class="fa fa-print"></i> Cetak Slip</button>
                <a href="javascript:;" class="btn btn-default disabled"><i class="fa fa-tag"></i> Etiket</a>
                <a href="javascript:;" class="btn btn-success disabled" title="Terhubung ke Kasir pada tahap berikutnya"><i class="fa fa-check"></i> Bayar</a>
                <?= $this->Html->link('<i class="fa fa-times"></i> Keluar', ['action' => 'index'], ['class' => 'btn btn-danger', 'escape' => false]) ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end() ?>

<div id="tx-dd"></div>

<style>
#tx-dd { display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #ced4da; border-radius:4px;
         box-shadow:0 4px 16px rgba(0,0,0,.15); max-height:300px; overflow-y:auto; font-size:12px; }
#tx-dd .dd-item { padding:7px 10px; cursor:pointer; border-bottom:1px solid #f0f0f0; line-height:1.4; }
#tx-dd .dd-item:hover { background:#e8f0fe; }
#tx-dd .dd-main { font-weight:600; color:#1e3a5f; }
#tx-dd .dd-meta { font-size:10px; color:#888; }
.num-in { text-align:right; min-width:95px; }
.tx-hit td { background:#fff3cd !important; }
@media print { .page-header, .breadcrumb, .panel-heading, #sidebar, #header, .btn, .form-row { display:none !important; } }
</style>

<script>
$(function () {
    var URL_MASTER = '<?= $this->Url->build(['action' => 'searchMaster']) ?>';
    var URL_ITEMS  = '<?= $this->Url->build(['action' => 'searchItems']) ?>';
    var URL_REG    = '<?= $this->Url->build(['action' => 'searchRegistration']) ?>';
    var INIT_ITEMS = <?= json_encode($itemsData, $jf) ?>;
    var INIT_SUPS  = <?= json_encode($supsData, $jf) ?>;

    var txIdx = 0, supIdx = 0, timers = {};
    var $dd = $('#tx-dd');

    function esc(s) { return $('<div>').text(String(s == null ? '' : s)).html(); }
    function num(v) { var n = parseFloat(v); return isNaN(n) ? 0 : n; }
    function fmt(n) { return num(n).toLocaleString('id-ID', {maximumFractionDigits: 2}); }

    // ---------- dropdown pencarian generik ----------
    function lookup(key, url, q, $anchor, render, pick, minLen) {
        clearTimeout(timers[key]);
        if (q.length < (minLen || 2)) { $dd.hide(); return; }
        timers[key] = setTimeout(function () {
            var r = $anchor[0].getBoundingClientRect();
            $dd.css({top: r.bottom + 2, left: r.left, width: Math.max(r.width, 440)})
               .html('<div class="p-2 text-muted"><i class="fa fa-spinner fa-spin"></i> Mencari...</div>').show();
            $.getJSON(url, {q: q}, function (rows) {
                $dd.empty();
                if (!rows || !rows.length) { $dd.html('<div class="p-2 text-muted">Tidak ditemukan</div>'); return; }
                $.each(rows, function (_, d) {
                    $('<div class="dd-item"></div>').html(render(d)).on('mousedown', function (e) {
                        e.preventDefault(); pick(d); $dd.hide();
                    }).appendTo($dd);
                });
            }).fail(function () { $dd.html('<div class="p-2 text-danger">Gagal memuat data.</div>'); });
        }, 250);
    }
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#tx-dd, #master-search, #item-search, #registration_no, #btn-find-reg').length) { $dd.hide(); }
    });

    // ---------- umur ----------
    function calcAge() {
        var b = $('#birth_date').val();
        if (!b) { $('#age_y,#age_m,#age_d').val(0); return; }
        var t = new Date($('#tgl').val() || new Date()), bd = new Date(b);
        var y = t.getFullYear() - bd.getFullYear(), m = t.getMonth() - bd.getMonth(), d = t.getDate() - bd.getDate();
        if (d < 0) { m--; d += new Date(t.getFullYear(), t.getMonth(), 0).getDate(); }
        if (m < 0) { y--; m += 12; }
        $('#age_y').val(Math.max(y, 0)); $('#age_m').val(Math.max(m, 0)); $('#age_d').val(Math.max(d, 0));
    }
    $('#birth_date, #tgl').on('change', calcAge);

    // ---------- Pasien RS / Luar ----------
    function applyOrigin(clear) {
        var luar = $('input[name=patient_origin]:checked').val() === 'luar';
        $('.rs-only').toggle(!luar);
        $('#patient_name, #medical_record_no, #birth_date').prop('readonly', !luar);
        if (luar && clear) { $('#registration_id, #patient_id, #registration_no').val(''); }
    }
    $('input[name=patient_origin]').on('change', function () { applyOrigin(true); });

    // ---------- cari registrasi ----------
    function searchReg() {
        lookup('reg', URL_REG, $.trim($('#registration_no').val()), $('#registration_no'),
            function (d) {
                return '<div class="dd-main">' + esc(d.name) + ' <small>(' + esc(d.rm) + ')</small></div>'
                     + '<div class="dd-meta">' + esc(d.registration_no) + ' &middot; ' + esc(d.service_label) + '</div>';
            },
            function (d) {
                $('#registration_id').val(d.id); $('#registration_no').val(d.registration_no);
                $('#patient_id').val(d.patient_id); $('#medical_record_no').val(d.rm);
                $('#patient_name').val(d.name); $('#birth_date').val(d.birth_date);
                $('#patient_type').val(d.service_label);
                if (d.gender) { $('input[name=gender][value=' + d.gender + ']').prop('checked', true); }
                calcAge();
            }, 3);
    }
    $('#registration_no').on('input', searchReg);
    $('#btn-find-reg').on('click', function () { $('#registration_no').focus(); searchReg(); });

    // ---------- baris tindakan ----------
    function numCell(i, f, val) {
        return '<td><input type="number" step="any" min="0" name="items[' + i + '][' + f + ']" data-f="' + f
             + '" class="form-control form-control-sm num-in tx-num" value="' + num(val) + '"></td>';
    }
    function addTx(d) {
        d = d || {}; var i = txIdx++;
        var h = '<tr class="tx-row"><td class="rn text-center"></td>'
          + '<td><input type="hidden" name="items[' + i + '][id]" value="' + esc(d.id || '') + '">'
          + '<input type="text" name="items[' + i + '][code]" class="form-control form-control-sm" value="' + esc(d.code || '') + '"></td>'
          + '<td><input type="text" name="items[' + i + '][examination_name]" class="form-control form-control-sm" value="' + esc(d.name || '') + '"></td>'
          + '<td><input type="text" name="items[' + i + '][exam_type]" class="form-control form-control-sm" value="' + esc(d.exam_type || '') + '"></td>'
          + '<td><input type="text" name="items[' + i + '][photo_no]" class="form-control form-control-sm photo-in" value="' + esc(d.photo_no || '') + '"></td>'
          + numCell(i, 'tariff', d.tariff) + numCell(i, 'doctor_fee', d.doctor_fee) + numCell(i, 'incentive', d.incentive)
          + numCell(i, 'insurance_cover', d.insurance_cover) + numCell(i, 'jpk', d.jpk)
          + numCell(i, 'disc_rs', d.disc_rs) + numCell(i, 'disc_dr', d.disc_dr) + numCell(i, 'outside_doctor_fee', d.outside_doctor_fee)
          + '<td><input type="text" class="form-control form-control-sm row-total num-in" readonly style="background:#f5f5f5;font-weight:600"></td>'
          + '<td class="text-center"><button type="button" class="btn btn-xs btn-danger btn-del-tx">&times;</button></td></tr>';
        $('#tx-tbody').append(h); recalc();
    }

    // ---------- baris alkes / BHP ----------
    function addSup(d) {
        d = d || {}; var i = supIdx++;
        var charged = (d.charged === undefined || d.charged) ? 'checked' : '';
        var h = '<tr class="sup-row">'
          + '<td><input type="hidden" name="supplies[' + i + '][item_id]" value="' + esc(d.item_id || '') + '">'
          + '<input type="text" name="supplies[' + i + '][code]" class="form-control form-control-sm" value="' + esc(d.code || '') + '"></td>'
          + '<td><input type="text" name="supplies[' + i + '][name]" class="form-control form-control-sm" value="' + esc(d.name || '') + '"></td>'
          + '<td><input type="number" step="any" min="0" name="supplies[' + i + '][qty]" class="form-control form-control-sm num-in sup-qty" value="' + (d.qty === undefined ? 1 : num(d.qty)) + '"></td>'
          + '<td><input type="text" name="supplies[' + i + '][unit]" class="form-control form-control-sm" value="' + esc(d.unit || '') + '"></td>'
          + '<td><input type="number" step="any" min="0" name="supplies[' + i + '][price]" class="form-control form-control-sm num-in sup-price" value="' + num(d.price) + '"></td>'
          + '<td><input type="text" class="form-control form-control-sm num-in sup-total" readonly style="background:#f5f5f5"></td>'
          + '<td><input type="text" name="supplies[' + i + '][location]" class="form-control form-control-sm" value="' + esc(d.location || 'RADIOLOGI') + '"></td>'
          + '<td class="text-center"><input type="checkbox" name="supplies[' + i + '][charged_to_patient]" value="1" class="sup-charge" ' + charged + '></td>'
          + '<td class="text-center"><button type="button" class="btn btn-xs btn-danger btn-del-sup">&times;</button></td></tr>';
        $('#sup-tbody').append(h); recalc();
    }

    // ---------- hitung total ----------
    function recalc() {
        var tx = 0, sp = 0;
        $('#tx-tbody tr').each(function (idx) {
            var $r = $(this); $r.find('.rn').text(idx + 1);
            var g = function (f) { return num($r.find('[data-f="' + f + '"]').val()); };
            var t = g('tariff') + g('doctor_fee') - g('disc_rs') - g('disc_dr');
            $r.find('.row-total').val(fmt(t)); tx += t;
        });
        $('#sup-tbody tr').each(function () {
            var $r = $(this), t = num($r.find('.sup-qty').val()) * num($r.find('.sup-price').val());
            $r.find('.sup-total').val(fmt(t));
            if ($r.find('.sup-charge').is(':checked')) { sp += t; }
        });
        $('#sum-tx').text(fmt(tx)); $('#sum-sup').text(fmt(sp)); $('#sum-all').text(fmt(tx + sp));
    }
    $(document).on('input change', '.tx-num, .sup-qty, .sup-price, .sup-charge', recalc);
    $(document).on('click', '.btn-del-tx', function () { $(this).closest('tr').remove(); recalc(); });
    $(document).on('click', '.btn-del-sup', function () { $(this).closest('tr').remove(); recalc(); });

    // ---------- pencarian master tindakan & alkes ----------
    $('#master-search').on('input', function () {
        lookup('master', URL_MASTER, $.trim($(this).val()), $(this),
            function (d) {
                return '<div class="dd-main">' + esc(d.nama) + (d.kode ? ' <small>[' + esc(d.kode) + ']</small>' : '') + '</div>'
                     + '<div class="dd-meta">' + (d.kategori ? esc(d.kategori) + ' &middot; ' : '') + 'Rp ' + fmt(d.tarif) + '</div>';
            },
            function (d) { addTx({code: d.kode, name: d.nama, exam_type: d.kategori, tariff: d.tarif}); $('#master-search').val(''); }, 2);
    });
    $('#item-search').on('input', function () {
        lookup('items', URL_ITEMS, $.trim($(this).val()), $(this),
            function (d) {
                return '<div class="dd-main">' + esc(d.nama) + (d.kode ? ' <small>[' + esc(d.kode) + ']</small>' : '') + '</div>'
                     + '<div class="dd-meta">' + esc(d.unit) + ' &middot; Rp ' + fmt(d.harga) + '</div>';
            },
            function (d) { addSup({item_id: d.id, code: d.kode, name: d.nama, unit: d.unit, price: d.harga}); $('#item-search').val(''); }, 2);
    });
    $('#btn-add-tx-blank').on('click', function () { addTx(); });
    $('#btn-add-sup-blank').on('click', function () { addSup(); });

    // ---------- cari no foto ----------
    $('#photo-search').on('keydown', function (e) {
        if (e.which !== 13) { return; }
        e.preventDefault();
        var q = $.trim($(this).val()); $('.tx-row').removeClass('tx-hit');
        if (!q) { return; }
        var $hit = $('.photo-in').filter(function () { return $.trim($(this).val()) === q; }).first();
        if ($hit.length) { $hit.closest('tr').addClass('tx-hit'); $hit.focus(); } else { alert('No foto tidak ditemukan.'); }
    });

    $('.btn-print').on('click', function () { window.print(); });

    // ---------- validasi submit ----------
    $('#form-tindakan').on('submit', function (e) {
        if (!$.trim($('#patient_name').val())) { alert('Nama pasien wajib diisi.'); e.preventDefault(); return; }
        if (!$('#tx-tbody tr').length) { alert('Tambahkan minimal satu tindakan.'); e.preventDefault(); }
    });
    $('#form-tindakan').on('keydown', function (e) { if (e.which === 13 && $(e.target).is('input')) { e.preventDefault(); } });

    // ---------- init ----------
    $.each(INIT_ITEMS, function (_, d) { addTx(d); });
    $.each(INIT_SUPS, function (_, d) { addSup(d); });
    applyOrigin(false); calcAge(); recalc();
});
</script>