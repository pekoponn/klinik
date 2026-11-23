<?php
/**
 * Simpan di: templates/Penunjang/Radiology/petugas.php
 *
 * Variabel dari controller: $petugas (hasil paginate), $q (kata kunci)
 */
?>
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Penunjang</a></li>
    <li class="breadcrumb-item"><?= $this->Html->link('Radiologi', ['action' => 'index']) ?></li>
    <li class="breadcrumb-item active">Nama Petugas</li>
</ol>
<h1 class="page-header">Daftar Petugas <small>Radiografer</small></h1>

<div class="panel panel-inverse">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-user-md"></i> Daftar Petugas Radiologi</h4>
    </div>

    <div class="panel-body">
        <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
        <div class="form-row align-items-end">
            <div class="col-md-5">
                <label class="mb-1"><i class="fa fa-search"></i> Cari</label>
                <?= $this->Form->text('q', [
                    'value' => $q,
                    'class' => 'form-control',
                    'placeholder' => 'Nama atau kode petugas...',
                ]) ?>
            </div>
            <div class="col-md-7">
                <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i> Cari</button>
                <?= $this->Html->link(
                    '<i class="fa fa-sync"></i> Reset',
                    ['action' => 'petugas'],
                    ['class' => 'btn btn-default', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fa fa-plus"></i> Kelola di Master Tenaga Medis',
                    ['prefix' => false, 'controller' => 'MasterTenagaMedis', 'action' => 'index'],
                    ['class' => 'btn btn-info', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fa fa-arrow-left"></i> Kembali',
                    ['action' => 'index'],
                    ['class' => 'btn btn-default', 'escape' => false]
                ) ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>

    <div class="panel-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:60px;" class="text-center">No</th>
                        <th style="width:160px;">Kode</th>
                        <th>Nama Petugas</th>
                        <th style="width:140px;">Status</th>
                        <th style="width:100px;" class="text-center">Aktif</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($petugas->count() === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fa fa-user-md fa-2x d-block mb-2"></i>
                            Belum ada petugas dengan status Radiografer
                        </td>
                    </tr>
                <?php endif; ?>
                <?php
                $no = ($this->Paginator->param('page') - 1) * $this->Paginator->param('perPage');
                foreach ($petugas as $p):
                    $no++;
                    $aktif = (bool)$p->is_active;
                ?>
                    <tr>
                        <td class="text-center"><?= $no ?></td>
                        <td><code><?= h($p->kode) ?></code></td>
                        <td><b><?= h($p->nama) ?></b></td>
                        <td><span class="badge badge-secondary">RADIOGRAFER</span></td>
                        <td class="text-center">
                            <span class="badge <?= $aktif ? 'badge-success' : 'badge-danger' ?>">
                                <?= $aktif ? 'Aktif' : 'Nonaktif' ?>
                            </span>
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