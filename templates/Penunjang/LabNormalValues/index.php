<?php
$num = fn($v) => $v === null ? '' : rtrim(rtrim(number_format((float)$v, 3, '.', ''), '0'), '.');
?>
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Penunjang</a></li>
    <li class="breadcrumb-item"><?= $this->Html->link('Laboratorium', ['controller' => 'LabExaminations', 'action' => 'index']) ?></li>
    <li class="breadcrumb-item active">Isi Nilai Normal</li>
</ol>
<h1 class="page-header">Isi Nilai Normal <small>Setting hasil laboratorium</small></h1>

<style>
    /* ---------- Daftar tindakan (kiri) ---------- */
    .lnv-list { max-height: 600px; overflow-y: auto; }
    .lnv-list .list-group-item { padding: 8px 12px; border-left: 3px solid transparent; line-height: 1.3; }
    .lnv-list .list-group-item small { display: block; font-size: 11px; }
    .lnv-list .list-group-item.active { border-left-color: #f59c1a; }
    .lnv-list .list-group-item.active small { color: rgba(255,255,255,.75) !important; }

    /* ---------- Identitas pemeriksaan ---------- */
    .lnv-meta label { font-size: 12px; font-weight: 600; margin-bottom: 3px; color: #495057; }
    .lnv-meta .form-control[readonly] { background: #f1f3f6; }

    /* ---------- Tabel parameter ---------- */
    .lnv-table { font-size: 12px; table-layout: fixed; min-width: 1140px; margin: 0; }
    .lnv-table thead th { vertical-align: middle; text-align: center; font-weight: 600; white-space: nowrap; padding: 7px 4px; }
    .lnv-table tbody td { padding: 4px; vertical-align: middle; background: #fff; }
    .lnv-table .form-control { padding: 3px 6px; height: 28px; font-size: 12px; }
    .lnv-table input.num { text-align: center; }
    .lnv-table tbody tr:hover td { background: #f5f8ff; }
    .lnv-table tbody td.col-pria   { background: #faf7ff; }
    .lnv-table tbody td.col-wanita { background: #fff7fa; }
    .lnv-table tbody tr:hover td.col-pria   { background: #f1eaff; }
    .lnv-table tbody tr:hover td.col-wanita { background: #ffeaf1; }
    .lnv-table th.grp-pria   { background: #6f55b5; color: #fff; }
    .lnv-table th.grp-wanita { background: #c0507a; color: #fff; }
    .lnv-table th.sub-row    { font-weight: 500; font-size: 11px; background: #343a40; }
    .lnv-empty { padding: 36px 12px; text-align: center; color: #8a93a0; }
    .lnv-empty i { font-size: 28px; display: block; margin-bottom: 8px; opacity: .6; }

    /* ---------- Toolbar bawah tabel ---------- */
    .lnv-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 10px 12px; border-top: 1px solid #e4e7ec; background: #fafbfc; }
    .lnv-toolbar .btn-group-right { display: flex; gap: 6px; }
    .lnv-hint { font-size: 12px; color: #6c757d; }
</style>

<div class="row">

    <!-- ===== DAFTAR PEMERIKSAAN ===== -->
    <div class="col-lg-3">
        <div class="panel panel-inverse">
            <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-list"></i> Tindakan</h4></div>
            <div class="panel-body p-2">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="input-group mb-2">
                    <?= $this->Form->text('q', ['value' => $q, 'class' => 'form-control', 'placeholder' => 'Cari kode / nama...']) ?>
                    <div class="input-group-append"><button class="btn btn-warning" title="Cari"><i class="fa fa-search"></i></button></div>
                </div>
                <?= $this->Form->end() ?>

                <div class="list-group lnv-list">
                <?php foreach ($labs as $l): ?>
                    <?= $this->Html->link(
                        '<small class="text-muted">' . h($l['code']) . '</small>' . h($l['name']),
                        ['action' => 'index', '?' => ['lab_id' => $l['id'], 'q' => $q]],
                        ['class' => 'list-group-item list-group-item-action' . ((int)$l['id'] === $selectedId ? ' active' : ''), 'escape' => false]
                    ) ?>
                <?php endforeach; ?>
                <?php if (!$labs): ?>
                    <div class="text-muted text-center py-4">Tidak ada pemeriksaan ditemukan.</div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== DETAIL ===== -->
    <div class="col-lg-9">
    <?php if (!$selectedLab): ?>
        <div class="alert alert-warning mb-0">
            <i class="fa fa-arrow-left"></i> Pilih pemeriksaan di daftar kiri untuk mengisi nilai normalnya.
        </div>
    <?php else: ?>

        <?= $this->Form->create(null, ['url' => ['action' => 'save'], 'id' => 'form-nilai']) ?>
        <?= $this->Form->hidden('master_laboratory_id', ['value' => $selectedId]) ?>

        <!-- Panel 1: identitas + parameter -->
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-flask"></i> Parameter &amp; Nilai Normal</h4>
            </div>

            <div class="panel-body lnv-meta">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Kode</label>
                        <input type="text" class="form-control form-control-sm" value="<?= h($selectedLab['code']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Golongan</label>
                        <input type="text" name="lab_group" class="form-control form-control-sm" value="<?= h($selectedLab['lab_group'] ?? '') ?>">
                    </div>
                    <div class="form-group col-md-5">
                        <label>Sub golongan</label>
                        <input type="text" name="lab_subgroup" class="form-control form-control-sm" value="<?= h($selectedLab['lab_subgroup'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label>Tindakan</label>
                    <input type="text" class="form-control form-control-sm" value="<?= h($selectedLab['name']) ?>" readonly>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm lnv-table">
                    <colgroup>
                        <col style="width:34px;">
                        <col style="width:90px;">
                        <col style="width:180px;">
                        <col style="width:70px;">
                        <col span="8" style="width:64px;">
                        <col span="2" style="width:100px;">
                        <col style="width:58px;">
                    </colgroup>
                    <thead class="thead-dark">
                        <tr>
                            <th rowspan="2"><input type="checkbox" id="chk-all" title="Pilih semua"></th>
                            <th rowspan="2">Kode Mesin</th>
                            <th rowspan="2">Keterangan Hasil</th>
                            <th rowspan="2">Satuan</th>
                            <th colspan="2">Umum</th>
                            <th colspan="2" class="grp-pria"><i class="fa fa-mars"></i> Pria</th>
                            <th colspan="2" class="grp-wanita"><i class="fa fa-venus"></i> Wanita</th>
                            <th colspan="2">Anak</th>
                            <th colspan="2">Uraian</th>
                            <th rowspan="2">Desimal</th>
                        </tr>
                        <tr>
                            <th class="sub-row">Dari</th><th class="sub-row">Sampai</th>
                            <th class="sub-row">Dari</th><th class="sub-row">Sampai</th>
                            <th class="sub-row">Dari</th><th class="sub-row">Sampai</th>
                            <th class="sub-row">Dari</th><th class="sub-row">Sampai</th>
                            <th class="sub-row">Pria</th><th class="sub-row">Wanita</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($values) === 0): ?>
                        <tr>
                            <td colspan="15" class="p-0">
                                <div class="lnv-empty">
                                    <i class="fa fa-vial"></i>
                                    Pemeriksaan ini belum punya parameter.<br>
                                    Klik <b>Tambah</b> untuk membuat parameter pertama, atau <b>Proses Copy</b> untuk menyalin dari pemeriksaan lain.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($values as $v):
                        $f = fn($field, $val = null, $cls = '') =>
                            '<input type="text" name="rows[' . $v->id . '][' . $field . ']" value="' . h($val ?? $v->$field) . '" class="form-control ' . $cls . '">';
                    ?>
                        <tr>
                            <td class="text-center"><input type="checkbox" name="check_ids[]" value="<?= $v->id ?>" class="chk-row"></td>
                            <td><?= $f('machine_code') ?></td>
                            <td><?= $f('parameter_name') ?></td>
                            <td><?= $f('unit') ?></td>
                            <td><?= $f('general_min', $num($v->general_min), 'num') ?></td>
                            <td><?= $f('general_max', $num($v->general_max), 'num') ?></td>
                            <td class="col-pria"><?= $f('male_min', $num($v->male_min), 'num') ?></td>
                            <td class="col-pria"><?= $f('male_max', $num($v->male_max), 'num') ?></td>
                            <td class="col-wanita"><?= $f('female_min', $num($v->female_min), 'num') ?></td>
                            <td class="col-wanita"><?= $f('female_max', $num($v->female_max), 'num') ?></td>
                            <td><?= $f('child_min', $num($v->child_min), 'num') ?></td>
                            <td><?= $f('child_max', $num($v->child_max), 'num') ?></td>
                            <td><?= $f('text_male') ?></td>
                            <td><?= $f('text_female') ?></td>
                            <td><?= $f('decimals', null, 'num') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="lnv-toolbar">
                <div class="d-flex align-items-center" style="gap:12px;">
                    <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-copy">
                        <i class="fa fa-copy"></i> Proses Copy
                    </button>
                    <span class="lnv-hint d-none d-xl-inline">Hasil di luar rentang akan ditandai H (tinggi) atau L (rendah).</span>
                </div>
                <div class="btn-group-right">
                    <button type="submit" class="btn btn-sm btn-default" formaction="<?= $this->Url->build(['action' => 'addValue']) ?>">
                        <i class="fa fa-plus"></i> Tambah
                    </button>
                    <button type="submit" class="btn btn-sm btn-default text-danger" formaction="<?= $this->Url->build(['action' => 'deleteChecked']) ?>"
                            onclick="return lnvConfirm('.chk-row', 'Hapus parameter yang dicentang?');">
                        <i class="fa fa-trash"></i> Hapus
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <?= $this->Form->end() ?>

        <!-- Panel 2: Master Lembar -->
        <div class="panel panel-inverse">
            <div class="panel-heading"><h4 class="panel-title"><i class="fa fa-file-alt"></i> Master Lembar</h4></div>

            <?= $this->Form->create(null, ['url' => ['action' => 'addTemplate'], 'id' => 'form-lembar']) ?>
            <?= $this->Form->hidden('master_laboratory_id', ['value' => $selectedId]) ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" style="font-size:12px;">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:34px;" class="text-center"><input type="checkbox" id="chk-all-tpl" title="Pilih semua"></th>
                            <th style="width:45%;">Nama Tes</th>
                            <th>Hasil Tes Normal</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($templates as $t): ?>
                        <tr>
                            <td class="text-center"><input type="checkbox" name="tpl_ids[]" value="<?= $t->id ?>" class="chk-tpl"></td>
                            <td><?= h($t->test_name) ?></td>
                            <td><?= h($t->normal_result) ?></td>
                        </tr>
                    <?php endforeach; ?>
                        <tr class="bg-light">
                            <td class="text-center text-muted"><i class="fa fa-plus"></i></td>
                            <td><?= $this->Form->text('test_name', ['class' => 'form-control form-control-sm', 'placeholder' => 'mis. Warna']) ?></td>
                            <td><?= $this->Form->text('normal_result', ['class' => 'form-control form-control-sm', 'placeholder' => 'mis. Kuning jernih']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="lnv-toolbar">
                <div>
                    <button type="button" class="btn btn-sm btn-default" disabled title="Fitur lanjutan">Master Result</button>
                </div>
                <div class="btn-group-right">
                    <button type="submit" class="btn btn-sm btn-default"><i class="fa fa-plus"></i> Tambah</button>
                    <button type="submit" class="btn btn-sm btn-default text-danger" formaction="<?= $this->Url->build(['action' => 'deleteTemplates']) ?>"
                            onclick="return lnvConfirm('.chk-tpl', 'Hapus data yang dicentang?');">
                        <i class="fa fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>

        <!-- Modal Proses Copy -->
        <div class="modal fade" id="modal-copy" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <?= $this->Form->create(null, ['url' => ['action' => 'copyFrom']]) ?>
                    <?= $this->Form->hidden('master_laboratory_id', ['value' => $selectedId]) ?>
                    <div class="modal-header">
                        <h5 class="modal-title">Proses Copy</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label>Salin parameter dari pemeriksaan:</label>
                        <select name="source_laboratory_id" class="form-control">
                            <?php foreach ($labs as $l): if ((int)$l['id'] === $selectedId) continue; ?>
                                <option value="<?= $l['id'] ?>"><?= h($l['code'] . ' - ' . $l['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-copy"></i> Salin</button>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <script>
        $('#chk-all').on('change', function(){ $('.chk-row').prop('checked', this.checked); });
        $('#chk-all-tpl').on('change', function(){ $('.chk-tpl').prop('checked', this.checked); });

        // Konfirmasi hapus hanya jika ada yang dicentang
        function lnvConfirm(selector, message) {
            if ($(selector + ':checked').length === 0) {
                alert('Centang data yang ingin dihapus terlebih dahulu.');
                return false;
            }
            return confirm(message);
        }
        </script>
    <?php endif; ?>
    </div>
</div>