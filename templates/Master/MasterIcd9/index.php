<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterIcd9[]|\Cake\Collection\CollectionInterface $masterIcd9
 * @var string $q
 * @var string $isActive
 */

$q = $q ?? $this->request->getQuery('q', '');
$isActive = $isActive ?? $this->request->getQuery('is_active', '');
$csrfToken = $this->request->getAttribute('csrfToken');
?>

<?= $this->Krs->breadcrumb('Master', 'ICD-9 CM', 'Tampilkan') ?>

<div class="panel panel-inverse">
    <div class="panel-heading">
        <h4 class="panel-title">Perincian</h4>
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand">
                <i class="fa fa-expand"></i>
            </a>
        </div>
    </div>

    <div class="panel-body">
        <div class="alert alert-primary">
            Halaman <strong>ICD-9 CM</strong>, digunakan untuk master tindakan/prosedur medis.
        </div>

        <div class="portlet-body form">
            <div class="table-toolbar">
                <?= $this->Html->link(
                    '<i class="fa fa-plus"></i>',
                    ['action' => 'add'],
                    [
                        'class' => 'btn btn-inverse',
                        'escape' => false,
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'data-original-title' => 'Tambah',
                    ]
                ) ?>
                <a href="javascript:void(0);" class="btn btn-success"
                   data-toggle="modal" data-target="#modalExportIcd9"
                   title="Export Excel">
                    <i class="fa fa-file-excel"></i> Excel
                </a>
                <?= $this->Html->link(
                    '<i class="fa fa-upload"></i> Import',
                    ['action' => 'import'],
                    [
                        'class' => 'btn btn-primary',
                        'escape' => false,
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'data-original-title' => 'Import CSV/XLSX',
                    ]
                ) ?>
            </div>

            <br>

            <?= $this->Form->create(null, ['type' => 'get']) ?>
            <div class="panel-footer bg-aqua text-white p-t-10">
                <div class="row row-space-10">
                    <div class="col-md-5 m-b-10">
                        <?= $this->Form->text('q', [
                            'class' => 'form-control',
                            'placeholder' => 'Cari kode atau nama ICD-9 CM...',
                            'value' => $q,
                        ]) ?>
                    </div>
                    <div class="col-md-3 m-b-10">
                        <?= $this->Form->select('is_active', [
                            '' => 'Semua Status',
                            '1' => 'Aktif',
                            '0' => 'Nonaktif',
                        ], [
                            'class' => 'form-control select2Form',
                            'value' => $isActive,
                        ]) ?>
                    </div>
                    <div class="col-md-1">
                        <?= $this->Krs->btnSeach(); ?>
                    </div>
                    <div class="col-md-1">
                        <?= $this->Html->link(
                            '<i class="fa fa-undo"></i>',
                            ['action' => 'index'],
                            [
                                'class' => 'btn btn-default',
                                'escape' => false,
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'data-original-title' => 'Reset Filter',
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end() ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-td-valign-middle tbl-icd9">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th width="130"><?= $this->Paginator->sort('kode', 'Kode') ?></th>
                            <th><?= $this->Paginator->sort('nama', 'Nama ICD-9 CM') ?></th>
                            <th width="120"><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                            <th width="160"><?= $this->Paginator->sort('modified', 'Diubah') ?></th>
                            <th width="150" class="actions">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($masterIcd9) > 0): ?>
                            <?php foreach ($masterIcd9 as $rowData): ?>
                                <?php $rowClass = (int)$rowData->is_active === 1 ? '' : 'class="table-warning"'; ?>
                                <tr <?= $rowClass ?>>
                                    <td align="center"><b><?= h($rowData->kode) ?></b></td>
                                    <td><?= h($rowData->nama) ?></td>
                                    <td align="center">
                                        <button
                                            type="button"
                                            class="btn btn-xs toggle-active-btn <?= (int)$rowData->is_active === 1 ? 'btn-lime' : 'btn-danger' ?>"
                                            data-id="<?= h($rowData->id) ?>"
                                            data-active="<?= (int)$rowData->is_active ?>"
                                        >
                                            <i class="fa <?= (int)$rowData->is_active === 1 ? 'fa-check' : 'fa-times' ?>"></i>
                                            <?= (int)$rowData->is_active === 1 ? 'Aktif' : 'Nonaktif' ?>
                                        </button>
                                    </td>
                                    <td align="center">
                                        <?= !empty($rowData->modified) ? h($rowData->modified->format('d-m-Y H:i')) : '-' ?>
                                    </td>
                                    <td class="actions text-center" style="white-space: nowrap;">
                                        <?= $this->Html->link(
                                            '<i class="fa fa-eye"></i>',
                                            ['action' => 'view', $rowData->id],
                                            [
                                                'class' => 'btn btn-yellow btn-sm m-r-5 p-l-15 p-r-15',
                                                'escape' => false,
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'data-original-title' => 'Lihat',
                                            ]
                                        ) ?>
                                        <?= $this->Krs->btn('edit', ['action' => 'edit', $rowData->id]) ?>
                                        <?= $this->Krs->btn('delete', ['action' => 'delete', $rowData->id], $rowData->kode) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?= $this->Krs->dataNull(); ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?= $this->Krs->paginators() ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-active-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            var currentButton = this;
            fetch('<?= $this->Url->build(['action' => 'toggleActive']) ?>/' + currentButton.dataset.id, {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= h($csrfToken) ?>',
                    'Content-Type': 'application/json'
                }
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.success) {
                    return;
                }

                var active = parseInt(data.is_active, 10) === 1;
                currentButton.dataset.active = active ? '1' : '0';
                currentButton.classList.toggle('btn-lime', active);
                currentButton.classList.toggle('btn-danger', !active);
                currentButton.innerHTML = '<i class="fa ' + (active ? 'fa-check' : 'fa-times') + '"></i> ' + (active ? 'Aktif' : 'Nonaktif');
            });
        });
    });
});

$(document).ready(function () {
    $('.select2Form').select2({
        width: '100%',
        minimumResultsForSearch: 0
    });
});
</script>

<!-- MODAL EXPORT EXCEL -->
<div class="modal fade" id="modalExportIcd9" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export ICD-9 CM ke Excel</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Status</label>
                            <select id="exportActive" class="form-control">
                                <option value="">Semua</option>
                                <option value="1"<?= (string)$isActive === '1' ? ' selected' : '' ?>>Aktif</option>
                                <option value="0"<?= (string)$isActive === '0' ? ' selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label>Kata kunci <small class="text-muted">(opsional)</small></label>
                            <input type="text" id="exportKeyword" class="form-control" autocomplete="off"
                                   placeholder="Kode / nama ICD-9 CM" value="<?= h($q) ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSubmitExportIcd9">Download</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#btnSubmitExportIcd9').on('click', function () {
        var params = {
            is_active: $('#exportActive').val() || '',
            q: $('#exportKeyword').val() || ''
        };

        window.location.href = '<?= $this->Url->build(["action" => "excel"]) ?>?' + $.param(params);
        $('#modalExportIcd9').modal('hide');
    });
});
</script>

<style>
/* Header tabel: hilangkan kotak putih pada link sorting */
.tbl-icd9 thead th {
    background: #212529 !important;
    color: #fff !important;
    vertical-align: middle;
    border-color: #343a40;
}
.tbl-icd9 thead th a,
.tbl-icd9 thead th a:hover,
.tbl-icd9 thead th a:focus,
.tbl-icd9 thead th a.asc,
.tbl-icd9 thead th a.desc {
    display: block;
    background: transparent !important;
    color: #fff !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
    text-decoration: none;
}
.tbl-icd9 thead th a:hover { color: #f59c1a !important; }
.tbl-icd9 thead th a.asc:after  { content: " \25B2"; font-size: 10px; }
.tbl-icd9 thead th a.desc:after { content: " \25BC"; font-size: 10px; }
</style>
