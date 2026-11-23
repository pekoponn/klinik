<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $users
 * @var array $rules
 */
$dataChild = $this->Html->link('User', ['action' => 'index'], ['full_base' => true]);
$modules = \Cake\Core\Configure::read('KlinikModules');
$rules = $rules ?? [];

// Kelompokkan controller berdasarkan menu; sisanya masuk "Lainnya".
$ctrlGroup = [];
foreach ($modules['menu'] as $g) {
    foreach ($g['items'] as $it) {
        $ctrlGroup[strtolower($it[2])] = $g['label'];
    }
}
$groups = [];
foreach ($modules['controllers'] as $key => $info) {
    $groups[$ctrlGroup[$key] ?? 'Lainnya'][$key] = $info;
}
$order = array_merge(array_column($modules['menu'], 'label'), ['Lainnya']);
?>
<?= $this->Krs->breadcrumb('Pengaturan', $dataChild, 'Akses') ?>
<?= $this->Form->create($users) ?>
<div class="panel panel-inverse">
    <div class="panel-heading"><h4 class="panel-title">Hak Akses: <?= h($users->name) ?></h4></div>
    <div class="panel-body">
        <?php
        echo $this->Form->control('id', ['type' => 'hidden', 'value' => $users->id]);
        echo $this->Form->control('user_id', ['type' => 'hidden', 'value' => $getUser->id]);
        ?>
        <div class="form-group row m-b-15">
            <label class="col-form-label col-md-2">Nama</label>
            <div class="col-md-4">
                <?= $this->Form->control('name', ['label' => false, 'class' => 'form-control', 'default' => $users->name, 'error' => false]) ?>
            </div>
        </div>
        <div class="form-group row m-b-15">
            <label class="col-form-label col-md-2">Telp</label>
            <div class="col-md-4">
                <?= $this->Form->control('telp', ['label' => false, 'class' => 'form-control', 'default' => $users->telp, 'error' => false]) ?>
            </div>
            <label class="col-form-label col-md-1">Email</label>
            <div class="col-md-4">
                <?= $this->Form->control('email', ['label' => false, 'class' => 'form-control', 'default' => $users->email, 'error' => false]) ?>
            </div>
        </div>
        <p>
            <button type="button" class="btn btn-sm btn-default" id="rules-check-all">Centang semua</button>
            <button type="button" class="btn btn-sm btn-default" id="rules-uncheck-all">Hapus semua</button>
        </p>

        <?php foreach ($order as $groupName):
            if (empty($groups[$groupName])) {
                continue;
            } ?>
            <h5 class="m-t-20"><?= h($groupName) ?></h5>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <tbody>
                    <?php foreach ($groups[$groupName] as $key => $info): ?>
                        <tr>
                            <td style="width:220px"><strong><?= h($info['name']) ?></strong></td>
                            <td>
                                <?php foreach ($info['actions'] as $act): ?>
                                    <div class="form-check form-check-inline">
                                        <?= $this->Form->checkbox("Rules.{$key}.{$act}", [
                                            'value' => '1',
                                            'hiddenField' => true,
                                            'checked' => (($rules[$key][$act] ?? 0) == 1),
                                            'class' => 'form-check-input rule-cb',
                                        ]) ?>
                                        <label class="form-check-label"><?= h($act) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <?= $this->Form->button('Simpan', ['class' => 'btn btn-primary', 'type' => 'submit', 'id' => 'btn-add']) ?>
    </div>
</div>
<?= $this->Form->end() ?>
<script>
    $(document).ready(function () {
        var confirmTitle = "Apakah anda ingin menyimpan data tersebut ?";
        var formAction = "<?= $this->Url->build(['controller' => 'users', 'action' => 'access', $users->id], ['fullBase' => true]) ?>";
        handleFormConfirmSubmitValid('UserAddForm', 'btn-add', confirmTitle, formAction);
        $('#rules-check-all').on('click', function () { $('.rule-cb').prop('checked', true); });
        $('#rules-uncheck-all').on('click', function () { $('.rule-cb').prop('checked', false); });
    });
</script>
