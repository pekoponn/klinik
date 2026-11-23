<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MasterTreatmentPackage[]|\Cake\Collection\CollectionInterface $masterTreatmentPackages
 */

$q = $q ?? $this->request->getQuery('q', '');
$isActive = $isActive ?? $this->request->getQuery('is_active', '');
$csrfToken = $this->request->getAttribute('csrfToken');
?>

<?= $this->Krs->breadcrumb('Master', 'Paket Treatment', 'Tampilkan') ?>

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
            Halaman <strong>Master Paket Treatment</strong>, digunakan untuk master data paket treatment pasien.
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
                        'data-original-title' => 'Tambah'
                    ]
                ) ?>

                <a href="javascript:void(0);" class="btn btn-success"
                   data-toggle="modal" data-target="#modalExportPkt"
                   title="Export Excel">
                    <i class="fa fa-file-excel"></i> Excel
                </a>

            </div>

            <br>

            <?= $this->Form->create(null, ['type' => 'get']) ?>

            <div class="panel-footer bg-aqua text-white p-t-10">

                <div class="row row-space-10">

                    <div class="col-md-5 m-b-10">

                        <?= $this->Form->text('q', [
                            'class' => 'form-control',
                            'placeholder' => 'Cari kode atau nama paket...',
                            'value' => $q
                        ]) ?>

                    </div>

                    <div class="col-md-3 m-b-10">

                        <?= $this->Form->select(
                            'is_active',
                            [
                                '' => 'Semua Status',
                                'Y' => 'Aktif',
                                'N' => 'Nonaktif'
                            ],
                            [
                                'class' => 'form-control',
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

                <table class="table table-striped table-hover table-td-valign-middle tbl-pkt">

                    <thead class="thead-dark text-center">

                        <tr>

                            <th width="120">
                                <?= $this->Paginator->sort('code', 'Kode') ?>
                            </th>

                            <th>
                                <?= $this->Paginator->sort('name', 'Nama Paket') ?>
                            </th>

                            <th width="180">
                                <?= $this->Paginator->sort('price', 'Harga') ?>
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

                    <?php if (count($masterTreatmentPackages) > 0): ?>

                        <?php foreach ($masterTreatmentPackages as $rowData): ?>

                        <?php
                        $active = $rowData->is_active === 'Y';
                        $rowClass = $active ? '' : 'class="table-warning"';
                        ?>

                        <tr <?= $rowClass ?>>

                            <td align="center">
                                <b><?= h($rowData->code) ?></b>
                            </td>

                            <td>
                                <?= h($rowData->name) ?>
                            </td>

                            <td align="right">
                                Rp <?= number_format((float)($rowData->price ?? 0), 0, ',', '.') ?>
                            </td>

                            <td align="center">

                                <button
                                    type="button"
                                    class="btn btn-xs toggle-active-btn <?= $active ? 'btn-lime' : 'btn-danger' ?>"
                                    data-id="<?= $rowData->id ?>"
                                    data-active="<?= $active ? 'Y' : 'N' ?>">

                                    <i class="fa <?= $active ? 'fa-check' : 'fa-times' ?>"></i>

                                    <?= $active ? 'Aktif' : 'Nonaktif' ?>

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

<div class="modal fade" id="modalViewTreatmentPackage">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h4 class="modal-title">
                    Detail Master Paket Treatment
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
                        <th>Nama Paket</th>
                        <td id="view-name"></td>
                    </tr>

                    <tr>
                        <th>Harga</th>
                        <td id="view-price"></td>
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

function formatRupiah(value) {
    var numericValue = parseFloat(value || 0);

    return 'Rp ' + numericValue.toLocaleString('id-ID', {
        maximumFractionDigits: 0
    });
}

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

                var active = data.is_active === 'Y';

                currentButton.dataset.active = active ? 'Y' : 'N';

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

            $('#view-code').text(res.data.code || '-');

            $('#view-name').text(res.data.name || '-');

            $('#view-price').text(formatRupiah(res.data.price));

            $('#view-status').text(
                res.data.is_active === 'Y' ? 'Aktif' : 'Nonaktif'
            );

            $('#view-created').text(res.data.created || '-');

            $('#view-modified').text(res.data.modified || '-');

            $('#modalViewTreatmentPackage').modal('show');

        }

    });

});

</script>

<!-- MODAL EXPORT EXCEL -->
<div class="modal fade" id="modalExportPkt" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Paket Treatment ke Excel</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Status</label>
                            <select id="exportActive" class="form-control">
                                <option value="">Semua</option>
                                <option value="Y"<?= (string)$isActive === 'Y' ? ' selected' : '' ?>>Aktif</option>
                                <option value="N"<?= (string)$isActive === 'N' ? ' selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label>Kata kunci <small class="text-muted">(opsional)</small></label>
                            <input type="text" id="exportKeyword" class="form-control" autocomplete="off"
                                   placeholder="Kode / nama paket" value="<?= h($q) ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSubmitExportPkt">Download</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#btnSubmitExportPkt').on('click', function () {
        var params = {
            is_active: $('#exportActive').val() || '',
            q: $('#exportKeyword').val() || ''
        };

        window.location.href = '<?= $this->Url->build(["action" => "excel"]) ?>?' + $.param(params);
        $('#modalExportPkt').modal('hide');
    });
});
</script>

<style>
/* Header tabel: hilangkan kotak putih pada link sorting */
.tbl-pkt thead th {
    background: #212529 !important;
    color: #fff !important;
    vertical-align: middle;
    border-color: #343a40;
}
.tbl-pkt thead th a,
.tbl-pkt thead th a:hover,
.tbl-pkt thead th a:focus,
.tbl-pkt thead th a.asc,
.tbl-pkt thead th a.desc {
    display: block;
    background: transparent !important;
    color: #fff !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
    text-decoration: none;
}
.tbl-pkt thead th a:hover { color: #f59c1a !important; }
.tbl-pkt thead th a.asc:after  { content: " \25B2"; font-size: 10px; }
.tbl-pkt thead th a.desc:after { content: " \25BC"; font-size: 10px; }
</style>