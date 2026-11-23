<?php
/**
 * Simpan di: templates/Penunjang/RadiologyNormalValues/index.php
 */
$selectedId = $selected ? (int)$selected->get('id') : 0;
$selfUrl = ['prefix' => 'Penunjang', 'controller' => 'RadiologyNormalValues', 'action' => 'index'];
?>
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Penunjang</a></li>
    <li class="breadcrumb-item"><?= $this->Html->link('Radiologi', ['prefix' => 'Penunjang', 'controller' => 'Radiology', 'action' => 'index']) ?></li>
    <li class="breadcrumb-item active">Isi Nilai Normal</li>
</ol>
<h1 class="page-header">Isi Nilai Normal <small>Nilai normal atau template radiologi</small></h1>

<style>
    /* Daftar tindakan */
    .rnv-list { max-height: 620px; overflow-y: auto; border: 1px solid #e4e7ec; border-radius: 4px; }
    .rnv-list .list-group-item { padding: 8px 12px; border-left: 3px solid transparent; border-radius: 0; line-height: 1.35; font-size: 12.5px; }
    .rnv-list .list-group-item small { display: block; font-size: 11px; font-family: monospace; }
    .rnv-list .list-group-item.active { border-left-color: #f59c1a; }
    .rnv-list .list-group-item.active small { color: rgba(255,255,255,.8) !important; }
    .rnv-hint { font-size: 12px; color: #6c757d; }

    /* Form kanan */
    .rnv-meta { padding: 16px 18px 6px; }
    .rnv-meta label { font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #495057; }
    .rnv-meta .form-control[readonly] { background: #f1f3f6; color: #343a40; }
    .rnv-meta .form-group { margin-bottom: 14px; }
    .rnv-edit { padding: 6px 18px 18px; }
    .rnv-edit label { font-size: 13px; font-weight: 700; margin-bottom: 6px; color: #212529; }
    #normal-value { font-size: 13px; line-height: 1.55; resize: vertical; min-height: 260px; }
    .rnv-divider { border-top: 1px dashed #d5d9e0; margin: 4px 18px 14px; }
    .rnv-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; padding: 12px 18px; border-top: 1px solid #e4e7ec; background: #fafbfc; }
</style>

<div class="row">

    <!-- ===== DAFTAR TINDAKAN ===== -->
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

                <div class="list-group rnv-list">
                <?php foreach ($list as $t):
                    $tid = (int)$t->get('id');
                ?>
                    <?= $this->Html->link(
                        '<small class="text-muted">' . h($t->get($codeField)) . '</small>' . h($t->get($nameField)),
                        $selfUrl + ['?' => ['tindakan_id' => $tid, 'q' => $q]],
                        ['class' => 'list-group-item list-group-item-action' . ($tid === $selectedId ? ' active' : ''), 'escape' => false]
                    ) ?>
                <?php endforeach; ?>
                <?php if (!$list): ?>
                    <div class="text-muted text-center py-4">Tidak ada tindakan radiologi ditemukan.</div>
                <?php endif; ?>
                </div>

                <?php if ($total > $limit): ?>
                    <div class="rnv-hint mt-2 px-1">Menampilkan <?= (int)$limit ?> dari <?= (int)$total ?> tindakan. Gunakan pencarian untuk mempersempit.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== DETAIL ===== -->
    <div class="col-lg-9">
    <?php if (!$selected): ?>
        <div class="alert alert-warning mb-0">
            <i class="fa fa-arrow-left"></i> Belum ada tindakan radiologi. Pastikan tindakan di Master Tindakan sudah berstatus radiologi.
        </div>
    <?php else: ?>

        <?= $this->Form->create(null, ['url' => ['prefix' => 'Penunjang', 'controller' => 'RadiologyNormalValues', 'action' => 'save']]) ?>
        <?= $this->Form->hidden('tindakan_id', ['value' => $selectedId]) ?>
        <?= $this->Form->hidden('q', ['value' => $q]) ?>

        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-x-ray"></i> Nilai Normal / Template Radiologi</h4>
            </div>

            <!-- Data tindakan (hanya tampil) -->
            <div class="rnv-meta">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Kode</label>
                        <input type="text" class="form-control form-control-sm" value="<?= h((string)($selected->get($detailFields['Kode']) ?? '')) ?>" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Kategori</label>
                        <input type="text" class="form-control form-control-sm" value="<?= h((string)($selected->get($detailFields['Kategori']) ?? '')) ?>" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Tindakan</label>
                        <input type="text" class="form-control form-control-sm" value="<?= h((string)($selected->get($detailFields['Tindakan']) ?? '')) ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" class="form-control form-control-sm" value="<?= h((string)($selected->get($detailFields['Keterangan']) ?? '')) ?>" readonly>
                </div>
            </div>

            <div class="rnv-divider"></div>

            <!-- Isian nilai normal -->
            <div class="rnv-edit">
                <label for="normal-value"><i class="fa fa-pen"></i> Nilai normal</label>
                <?= $this->Form->textarea('normal_value', [
                    'id' => 'normal-value',
                    'value' => $normalValue,
                    'rows' => 14,
                    'class' => 'form-control',
                    'placeholder' => 'Tulis template hasil atau nilai normal untuk tindakan ini...',
                ]) ?>
            </div>

            <div class="rnv-toolbar">
                <span class="rnv-hint"><i class="fa fa-info-circle"></i> Teks ini menjadi template saat petugas mengisi hasil radiologi.</span>
                <div>
                    <?= $this->Html->link('<i class="fa fa-undo"></i> Batal', $selfUrl + ['?' => ['tindakan_id' => $selectedId, 'q' => $q]], ['class' => 'btn btn-sm btn-default', 'escape' => false]) ?>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <?= $this->Form->end() ?>

    <?php endif; ?>
    </div>
</div>