<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterPenjamin[]|\Cake\Collection\CollectionInterface $masterPenjamins
 */

$q = $q ?? $this->request->getQuery('q', '');
$isActive = $isActive ?? $this->request->getQuery('is_active', '');
$csrfToken = $this->request->getAttribute('csrfToken');
?>

<?= $this->Krs->breadcrumb('Master', 'Penjamin', 'Tampilkan') ?>

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
            Halaman <strong>Master Penjamin</strong>, digunakan untuk master data penjamin pasien.
        </div>

        <div class="portlet-body form">

            <div class="table-toolbar">

                <a href="javascript:void(0);" class="btn btn-success"
                   data-toggle="modal" data-target="#modalExportPenjamin"
                   title="Export Excel">
                    <i class="fa fa-file-excel"></i> Excel
                </a>

                <?= $this->Html->link(
                    '<i class="fa fa-plus"></i>',
                    ['action' => 'add'],
                    [
                        'class' => 'btn btn-inverse',
                        'escape' => false,
                        'data-toggle' => 'tooltip',
                        'data-original-title' => 'Tambah'
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
                            'placeholder' => 'Cari kode atau nama penjamin...',
                            'value' => $q
                        ]) ?>

                    </div>

                    <div class="col-md-3 m-b-10">

                        <?= $this->Form->select(
                            'is_active',
                            [
                                '' => 'Semua Status',
                                '1' => 'Aktif',
                                '0' => 'Nonaktif'
                            ],
                            [
                                'class' => 'form-control select2Form',
                                'value' => $isActive
                            ]
                        ) ?>

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
                                'data-original-title' => 'Reset'
                            ]
                        ) ?>

                    </div>

                </div>

            </div>

            <?= $this->Form->end(); ?>

            <div class="table-responsive">

                <table class="table table-striped table-hover table-td-valign-middle tbl-penjamin">

                    <thead class="thead-dark text-center">

                        <tr>

                            <th width="120">
                                <?= $this->Paginator->sort('code', 'Kode') ?>
                            </th>

                            <th>
                                <?= $this->Paginator->sort('name', 'Nama Penjamin') ?>
                            </th>

                            <th width="180">
                                <?= $this->Paginator->sort('type', 'Jenis') ?>
                            </th>

                            <th width="120">
                                <?= $this->Paginator->sort('is_active', 'Status') ?>
                            </th>

                            <th width="170">
                                <?= $this->Paginator->sort('modified', 'Diubah') ?>
                            </th>

                            <th width="180" class="actions">&nbsp;</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if (count($masterPenjamins) > 0): ?>

                        <?php foreach ($masterPenjamins as $rowData): ?>

                        <?php $rowClass = $rowData->is_active ? '' : 'class="table-warning"'; ?>

                        <tr <?= $rowClass ?>>

                            <td align="center">
                                <b><?= h($rowData->code) ?></b>
                            </td>

                            <td>
                                <?= h($rowData->name) ?>
                            </td>

                            <td align="center">
                                <?= h($rowData->type) ?>
                            </td>

                            <td align="center">

                                <button
                                    type="button"
                                    class="btn btn-xs toggle-active-btn <?= $rowData->is_active ? 'btn-lime' : 'btn-danger' ?>"
                                    data-id="<?= $rowData->id ?>"
                                    data-active="<?= $rowData->is_active ?>">

                                    <i class="fa <?= $rowData->is_active ? 'fa-check' : 'fa-times' ?>"></i>

                                    <?= $rowData->is_active ? 'Aktif' : 'Nonaktif' ?>

                                </button>

                            </td>

                            <td align="center">

                                <?= !empty($rowData->modified)
                                    ? $rowData->modified->format('d-m-Y H:i')
                                    : '-' ?>

                            </td>

                            <td class="actions text-center">

                                <?= $this->Krs->btn('preview', null, $rowData->id) ?>

                                <?= $this->Krs->btn(
                                    'edit',
                                    ['action' => 'edit', $rowData->id]
                                ) ?>

                                <?= $this->Krs->btn(
                                    'delete',
                                    ['action' => 'delete', $rowData->id],
                                    $rowData->code
                                ) ?>

                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <?= $this->Krs->dataNull(); ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

                        <?= $this->Krs->paginators(); ?>

        </div>

    </div>

</div>

<div class="modal fade" id="modalViewPenjamin">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h4 class="modal-title">
                    Detail Master Penjamin
                </h4>

                <button class="close" data-dismiss="modal">
                    &times;
                </button>

            </div>

            <div class="modal-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="220">Kode</th>
                        <td id="view-code"></td>
                    </tr>

                    <tr>
                        <th>Nama Penjamin</th>
                        <td id="view-name"></td>
                    </tr>

                    <tr>
                        <th>Jenis</th>
                        <td id="view-type"></td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td id="view-status"></td>
                    </tr>

                    <tr>
                        <th>Dibuat</th>
                        <td id="view-created"></td>
                    </tr>

                    <tr>
                        <th>Terakhir Diubah</th>
                        <td id="view-modified"></td>
                    </tr>

                </table>

            </div>

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
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {

                if (!data.success) {
                    return;
                }

                var active = parseInt(data.is_active, 10) === 1;

                currentButton.dataset.active = active ? '1' : '0';

                currentButton.classList.toggle('btn-lime', active);
                currentButton.classList.toggle('btn-danger', !active);

                currentButton.innerHTML =
                    '<i class="fa ' +
                    (active ? 'fa-check' : 'fa-times') +
                    '"></i> ' +
                    (active ? 'Aktif' : 'Nonaktif');

            });

        });

    });

});

$(document).on('click', '.btn-preview', function () {

    var id = $(this).data('id');

    $.ajax({

        url: '<?= $this->Url->build(['action' => 'view']) ?>/' + id,

        type: 'GET',

        dataType: 'json',

        success: function (res) {

            $('#view-code').text(res.data.code);

            $('#view-name').text(res.data.name);

            $('#view-type').text(res.data.type);

            $('#view-status').text(
                res.data.is_active ? 'Aktif' : 'Nonaktif'
            );

            $('#view-created').text(res.data.created);

            $('#view-modified').text(res.data.modified);

            $('#modalViewPenjamin').modal('show');

        }

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
<div class="modal fade" id="modalExportPenjamin" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Master Penjamin ke Excel</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Jenis <small class="text-muted">(kosongkan = semua)</small></label>
                    <?= $this->Form->select('export_type', $exportTypes ?? [], [
                        'id' => 'exportType',
                        'multiple' => true,
                        'class' => 'form-control',
                        'style' => 'width:100%',
                    ]) ?>
                </div>

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
                                   placeholder="Kode / nama / jenis" value="<?= h($q) ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSubmitExportPenjamin">Download</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Select2 di popup dipasang saat modal sudah tampil supaya dropdown benar
    $('#modalExportPenjamin').on('shown.bs.modal', function () {
        var $modal = $('#modalExportPenjamin');
        if (!$('#exportType').hasClass('select2-hidden-accessible')) {
            $('#exportType').select2({
                placeholder: 'Semua jenis',
                allowClear: true,
                width: '100%',
                dropdownParent: $modal
            });
        }
    });

    $('#btnSubmitExportPenjamin').on('click', function () {
        var params = {
            type: $('#exportType').val() || [],
            is_active: $('#exportActive').val() || '',
            q: $('#exportKeyword').val() || ''
        };

        window.location.href = '<?= $this->Url->build(["action" => "excel"]) ?>?' + $.param(params);
        $('#modalExportPenjamin').modal('hide');
    });
});
</script>

<style>
.select2-container--open { z-index: 99999; }

.tbl-penjamin thead th {
    background: #212529 !important;
    color: #fff !important;
    vertical-align: middle;
    border-color: #343a40;
}
.tbl-penjamin thead th a,
.tbl-penjamin thead th a:hover,
.tbl-penjamin thead th a:focus,
.tbl-penjamin thead th a.asc,
.tbl-penjamin thead th a.desc {
    display: block;
    background: transparent !important;
    color: #fff !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
    text-decoration: none;
}
.tbl-penjamin thead th a:hover { color: #f59c1a !important; }
.tbl-penjamin thead th a.asc:after  { content: " \25B2"; font-size: 10px; }
.tbl-penjamin thead th a.desc:after { content: " \25BC"; font-size: 10px; }
</style>