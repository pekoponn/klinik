<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\PharmacyItem> $pharmacyItems
 */

$pages = (empty($this->request->getQuery('page'))) ? '' : $this->request->getQuery('page');
?>

<?= $this->Krs->breadcrumb('Master', 'Pharmacy Item', 'Tampilkan') ?>

<div class="panel panel-inverse">

    <div class="panel-heading">

        <h4 class="panel-title">
            Perincian
        </h4>

        <div class="panel-heading-btn">

            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand">

                <i class="fa fa-expand"></i>

            </a>

        </div>

    </div>

    <div class="panel-body">

        <div class="alert alert-primary">

            Halaman <strong>Pharmacy Item</strong>, digunakan untuk data master pharmacy item.

        </div>

        <div class="portlet-body form">

            <div class="table-toolbar">

                <div class="btn-group">

                    <div class="portlet-body">

                        <a href="javascript:void(0);" class="btn btn-success"
                           data-toggle="modal" data-target="#modalExportPharmacy"
                           title="Export Excel">
                            <i class="fa fa-file-excel"></i> Excel
                        </a>

                        <?= $this->Html->link(
                            '<i class="fa fa-plus"></i>',
                            ['action' => 'add', $pages],
                            [
                                'class' => 'btn btn-inverse',
                                'data-toggle' => 'tooltip',
                                'data-placement' => 'top',
                                'data-original-title' => 'Add',
                                'escape' => false,
                                'fullBase' => true
                            ]
                        ) ?>

                    </div>

                </div>

            </div>

            <br>

            <?= $this->Form->create(null, ['type' => 'get']) ?>

            <div class="panel-footer bg-aqua text-white p-t-10">

                <div class="row row-space-10">

                    <div class="col-md-2 m-b-10">

                        <?= $this->Form->text(
                            'filterCode',
                            [
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => 'Kode...',
                                'value' => $this->request->getQuery('filterCode')
                            ]
                        ); ?>

                    </div>

                    <div class="col-md-4 m-b-10">

                        <?= $this->Form->text(
                            'filterName',
                            [
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => 'Nama Pharmacy Item...',
                                'value' => $this->request->getQuery('filterName')
                            ]
                        ); ?>

                    </div>

                    <div class="col-md-2 m-b-10">

                        <?= $this->Form->text(
                            'filterType',
                            [
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => 'Type...',
                                'value' => $this->request->getQuery('filterType')
                            ]
                        ); ?>

                    </div>

                    <div class="col-md-1">

                        <?= $this->Krs->btnSeach(); ?>

                    </div>

                </div>

            </div>

            <?= $this->Form->end() ?>

            <div class="table-responsive">

                <table class="table table-striped table-hover table-td-valign-middle">

                    <thead class="thead-dark text-center">

                        <tr>

                            <th width="100">
                                Kode
                            </th>

                            <th>
                                Nama
                            </th>

                            <th width="120">
                                Region
                            </th>

                            <th width="120">
                                Type
                            </th>

                            <th width="120">
                                Purchase Unit
                            </th>

                            <th width="120">
                                Sale Unit
                            </th>

                            <th width="100">
                                QJB
                            </th>

                            <th width="150">
                                Therapy Class
                            </th>

                            <th width="150">
                                Producer
                            </th>

                            <th width="120">
                                HPP
                            </th>

                            <th width="120">
                                Sale Price
                            </th>

                            <th width="120" class="actions">
                                &nbsp;
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        if (count($pharmacyItems) > 0) {

                            foreach ($pharmacyItems as $rowData) {

                                $statusHiddenActive = ($rowData->is_active == 'Y') ? "" : ' style="display:none;"';
                                $statusHiddenInactive = ($rowData->is_active == 'N') ? "" : ' style="display:none;"';

                                $btnActive = "<div id='statusSpin" . $rowData->id . "'></div>";

                                $btnActive .= "<div id='statusActive" . $rowData->id . "'" . $statusHiddenActive . ">" .
                                    $this->Html->link(
                                        '<i class="fa fa-check"></i>',
                                        'javascript:;',
                                        [
                                            "escape" => false,
                                            "class" => "btn btn-lime btn-icon btn-circle btn-sm"
                                        ]
                                    ) .
                                    "</div>";

                                $btnActive .= "<div id='statusInactive" . $rowData->id . "'" . $statusHiddenInactive . ">" .
                                    $this->Html->link(
                                        '<i class="fa fa-times"></i>',
                                        'javascript:;',
                                        [
                                            "escape" => false,
                                            "class" => "btn btn-danger btn-icon btn-circle btn-sm"
                                        ]
                                    ) .
                                    "</div>";

                                if ($rowData->is_active == 'Y') {

                                    $classCondition = "";

                                } else {

                                    $classCondition = 'class="table-warning"';

                                }

                                $linkEdit = ['action' => 'edit', $rowData->id];
                                $linkDelete = ['action' => 'delete', $rowData->id];
                                ?>

                                <tr <?= $classCondition ?>>

                                    <td align="center">
                                        <b><?= h($rowData->code) ?></b>
                                    </td>

                                    <td>
                                        <?= h($rowData->name) ?>
                                    </td>

                                    <td align="center">
                                        <?= h($rowData->region) ?>
                                    </td>

                                    <td align="center">
                                        <?= h($rowData->type) ?>
                                    </td>

                                    <td align="center">
                                        <?= h($rowData->purchase_unit) ?>
                                    </td>

                                    <td align="center">
                                        <?= h($rowData->sale_unit) ?>
                                    </td>

                                    <td align="center">
                                        <?= h($rowData->qjb) ?>
                                    </td>

                                    <td>
                                        <?= h($rowData->therapy_class) ?>
                                    </td>

                                    <td>
                                        <?= h($rowData->producer) ?>
                                    </td>

                                    <td align="right">
                                        <?= h($rowData->hpp) ?>
                                    </td>

                                    <td align="right">
                                        <?= h($rowData->sale_price) ?>
                                    </td>

                                    <td class="actions" width="100">

                                        <?= $this->Krs->btn('edit', $linkEdit) ?>

                                        <?= $this->Krs->btn('delete', $linkDelete, $rowData->name) ?>

                                    </td>

                                </tr>

                                <?php
                            }

                        } else {

                            echo $this->Krs->dataNull();

                        }
                        ?>

                    </tbody>

                </table>

            </div>

            <?= $this->Krs->paginators() ?>

        </div>

    </div>

</div>
<!-- MODAL EXPORT EXCEL -->
<div class="modal fade" id="modalExportPharmacy" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Pharmacy Item ke Excel</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Region <small class="text-muted">(kosongkan = semua)</small></label>
                            <?= $this->Form->select('export_region', $exportRegions ?? [], [
                                'id' => 'exportRegion',
                                'multiple' => true,
                                'class' => 'form-control',
                                'style' => 'width:100%',
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type <small class="text-muted">(kosongkan = semua)</small></label>
                            <?= $this->Form->select('export_type', $exportTypes ?? [], [
                                'id' => 'exportType',
                                'multiple' => true,
                                'class' => 'form-control',
                                'style' => 'width:100%',
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Therapy Class <small class="text-muted">(kosongkan = semua)</small></label>
                    <?= $this->Form->select('export_therapy', $exportTherapies ?? [], [
                        'id' => 'exportTherapy',
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
                                <option value="Y">Aktif</option>
                                <option value="N">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label>Kata kunci <small class="text-muted">(opsional)</small></label>
                            <input type="text" id="exportKeyword" class="form-control" autocomplete="off"
                                   placeholder="Kode / nama / producer"
                                   value="<?= h((string)($this->request->getQuery('filterName') ?: $this->request->getQuery('filterCode'))) ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnSubmitExportPharmacy">Download</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Select2 di popup dipasang saat modal sudah tampil supaya dropdown benar
    $('#modalExportPharmacy').on('shown.bs.modal', function () {
        var $modal = $('#modalExportPharmacy');
        var cfg = {
            '#exportRegion': 'Semua region',
            '#exportType': 'Semua type',
            '#exportTherapy': 'Semua therapy class'
        };
        $.each(cfg, function (sel, ph) {
            if (!$(sel).hasClass('select2-hidden-accessible')) {
                $(sel).select2({
                    placeholder: ph,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $modal
                });
            }
        });
    });

    $('#btnSubmitExportPharmacy').on('click', function () {
        var params = {
            region: $('#exportRegion').val() || [],
            type: $('#exportType').val() || [],
            therapy: $('#exportTherapy').val() || [],
            is_active: $('#exportActive').val() || '',
            q: $('#exportKeyword').val() || ''
        };

        window.location.href = '<?= $this->Url->build(["action" => "excel"]) ?>?' + $.param(params);
        $('#modalExportPharmacy').modal('hide');
    });
});
</script>

<style>
.select2-container--open { z-index: 99999; }
</style>