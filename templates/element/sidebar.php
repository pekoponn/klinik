<?php
/**
 * Sidebar Klinik. Menu dibangun dari config/klinik_modules.php;
 * item tampil jika user punya hak akses ke action-nya ($checkUrl[controller][action]).
 */
$modules = \Cake\Core\Configure::read('KlinikModules');
$users = $checkUrl['users'] ?? [];
$branch = $checkUrl['branches'] ?? [];
$curC = strtolower((string)$getController);
$curA = (string)$getAction;
$menuDashboard = ($curC === 'dashboard') ? ' class="active"' : '';

if (($users['sidebarAksesibel'] ?? 0) == 1) {
    echo $this->element('sidebar_aksesibel');
    return;
}
?>
<div class="sidebar-bg"></div>
<div id="sidebar" class="sidebar">
    <div data-scrollbar="true" data-height="100%">
        <ul class="nav">
            <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
            <li<?= $menuDashboard ?>>
                <?= $this->Html->link('<i class="fas fa-home"></i><span>Beranda</span>', ['prefix' => false, 'controller' => 'Dashboard', 'action' => 'index'], ['escape' => false]) ?>
            </li>

            <?php foreach ($modules['menu'] as $group):
                $visible = array_filter($group['items'], fn($i) => !empty($checkUrl[strtolower($i[2])][$i[3]]));
                if (empty($visible)) {
                    continue;
                }
                $groupActive = in_array($curC, array_map(fn($i) => strtolower($i[2]), $visible), true) ? 'active' : '';
                ?>
                <li class="has-sub <?= $groupActive ?>">
                    <a href="javascript:;">
                        <b class="caret pull-right"></b>
                        <i class="fa <?= h($group['icon']) ?>"></i>
                        <span><?= h($group['label']) ?></span>
                    </a>
                    <ul class="sub-menu">
                        <?php foreach ($visible as [$label, $prefix, $controller, $action]):
                            $isActive = ($curC === strtolower($controller) && $curA === $action) ? ' class="active"' : '';
                            ?>
                            <li<?= $isActive ?>>
                                <?= $this->Html->link($label, ['prefix' => $prefix, 'controller' => $controller, 'action' => $action], ['fullBase' => true, 'escape' => false]) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>

            <?php if (!empty($users['changePassword']) || !empty($users['index']) || !empty($branch['index'])): ?>
                <li class="has-sub <?= in_array($curC, ['users', 'branches'], true) ? 'active' : '' ?>">
                    <a href="javascript:;">
                        <b class="caret pull-right"></b>
                        <i class="fa fa-cog"></i>
                        <span>Pengaturan</span>
                    </a>
                    <ul class="sub-menu">
                        <li<?= ($curC === 'users' && $curA === 'changePassword') ? ' class="active"' : '' ?>>
                            <?= $this->Html->link('Ganti Password', ['prefix' => false, 'controller' => 'Users', 'action' => 'changePassword', $getUser->id], ['fullBase' => true, 'escape' => false]) ?>
                        </li>
                        <?php if (!empty($users['index'])): ?>
                            <li<?= ($curC === 'users' && in_array($curA, ['index', 'add', 'edit', 'resetPassword', 'access'], true)) ? ' class="active"' : '' ?>>
                                <?= $this->Html->link('User', ['prefix' => false, 'controller' => 'Users', 'action' => 'index'], ['fullBase' => true, 'escape' => false]) ?>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($branch['index'])): ?>
                            <li<?= ($curC === 'branches') ? ' class="active"' : '' ?>>
                                <?= $this->Html->link('Cabang', ['prefix' => 'Master', 'controller' => 'Branches', 'action' => 'index'], ['fullBase' => true, 'escape' => false]) ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
        </ul>
    </div>
</div>
