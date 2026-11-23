<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Penunjang</a></li>
    <li class="breadcrumb-item"><?= $this->Html->link('Laboratorium', ['prefix' => false, 'controller' => 'LabExaminations', 'action' => 'index']) ?></li>
    <li class="breadcrumb-item active">Setup Petugas Laborat</li>
</ol>
<h1 class="page-header">Daftar Petugas <small><?= h($statusLabel) ?></small></h1>

<div class="panel panel-inverse">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="fa fa-user-md"></i> Daftar Petugas Laboratorium</h4>
    </div>

    <div class="panel-body">
        <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
        <label class="mb-1"><i class="fa fa-search"></i> Cari</label>
        <div class="form-row align-items-center">
            <div class="col-lg-6 col-md-12 mb-2">
                <?= $this->Form->text('q', ['value' => $q, 'class' => 'form-control', 'placeholder' => 'Nama atau kode petugas...']) ?>
            </div>
            <div class="col-auto mb-2">
                <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i> Cari</button>
                <?= $this->Html->link('<i class="fa fa-sync"></i> Reset', ['prefix' => 'Penunjang', 'controller' => 'LabOfficers', 'action' => 'index'], ['class' => 'btn btn-default', 'escape' => false]) ?>
                <?= $this->Html->link('<i class="fa fa-plus"></i> Kelola di Master Tenaga Medis', ['prefix' => false, 'controller' => 'MasterTenagaMedis', 'action' => 'index'], ['class' => 'btn btn-info', 'escape' => false]) ?>
                <?= $this->Html->link('<i class="fa fa-arrow-left"></i> Kembali', ['prefix' => false, 'controller' => 'LabExaminations', 'action' => 'index'], ['class' => 'btn btn-default', 'escape' => false]) ?>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center" style="width:60px;">No</th>
                    <th style="width:160px;">Kode</th>
                    <th>Nama Petugas</th>
                    <th style="width:210px;">Status</th>
                    <th class="text-center" style="width:100px;">Aktif</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($officers->count() === 0): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="fa fa-user-md fa-2x d-block mb-2"></i>
                        Belum ada petugas dengan status <?= h($statusLabel) ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php
                $perPage = (int)$this->Paginator->param('perPage');
                $page    = (int)$this->Paginator->param('page');
                $no      = ($page > 1 ? ($page - 1) * $perPage : 0);
                foreach ($officers as $o): $no++;
                $active = (bool)$o->get($activeField);
            ?>
                <tr>
                    <td class="text-center"><?= $no ?></td>
                    <td><code><?= h($o->get($codeField)) ?></code></td>
                    <td><b><?= h($o->get($nameField)) ?></b></td>
                    <td><?= h($o->get($statusField)) ?></td>
                    <td class="text-center">
                        <span class="badge <?= $active ? 'badge-success' : 'badge-secondary' ?>"><?= $active ? 'Aktif' : 'Nonaktif' ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel-footer d-flex justify-content-between align-items-center">
        <small class="text-muted"><?= $this->Paginator->counter('Halaman {{page}} dari {{pages}}, total {{count}} data') ?></small>
        <ul class="pagination pagination-sm mb-0">
            <?= $this->Paginator->prev('« Sebelumnya') ?>
            <?= $this->Paginator->next('Berikutnya »') ?>
        </ul>
    </div>
</div>