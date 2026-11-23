<?php
/**
 * Sidebar ramah screen reader.
 * Dipakai hanya jika hak akses users -> sidebarAksesibel = 1.
 * Menu dibangun dari config/klinik_modules.php.
 */
$modules = \Cake\Core\Configure::read('KlinikModules');
$p = fn(string $c, string $a): bool => !empty($checkUrl[$c][$a]);

$h = fn(string $label, array $url, bool $show, string $ctrl, ?string $act = null): array
    => ['label' => $label, 'url' => $url, 'show' => $show, 'ctrl' => $ctrl, 'act' => $act];

$menu = [];
$menu[] = $h('Beranda', ['prefix' => false, 'controller' => 'Dashboard', 'action' => 'index'], true, 'dashboard');

foreach ($modules['menu'] as $group) {
    $children = [];
    foreach ($group['items'] as [$label, $prefix, $controller, $action]) {
        $children[] = $h(
            $label,
            ['prefix' => $prefix, 'controller' => $controller, 'action' => $action],
            $p(strtolower($controller), $action),
            strtolower($controller),
            strtolower($action)
        );
    }
    $menu[] = ['label' => $group['label'], 'children' => $children];
}

$adaPengaturan = $p('users', 'changePassword') || $p('users', 'index') || $p('users', 'access') || $p('branches', 'index');
$menu[] = ['label' => 'Pengaturan', 'children' => [
    $h('Ganti Password', ['prefix' => false, 'controller' => 'Users', 'action' => 'changePassword', $getUser->id], $adaPengaturan, 'users', 'changepassword'),
    $h('User', ['prefix' => false, 'controller' => 'Users', 'action' => 'index'], $p('users', 'index'), 'users', 'index'),
    $h('Cabang', ['prefix' => 'Master', 'controller' => 'Branches', 'action' => 'index'], $p('branches', 'index'), 'branches'),
]];

// ---------- Fungsi bantu ----------
$curC = strtolower((string)$getController);
$curA = strtolower((string)$getAction);

$visible = function (array $it) use (&$visible): bool {
    if (isset($it['children'])) {
        foreach ($it['children'] as $c) {
            if ($visible($c)) {
                return true;
            }
        }
        return false;
    }
    return !empty($it['show']);
};
$active = function (array $it) use (&$active, $curC, $curA): bool {
    if (isset($it['children'])) {
        foreach ($it['children'] as $c) {
            if ($active($c)) {
                return true;
            }
        }
        return false;
    }
    if (empty($it['show']) || $it['ctrl'] !== $curC) {
        return false;
    }
    return $it['act'] === null || $it['act'] === $curA;
};

$uid = 0;
$render = function (array $items) use (&$render, &$uid, $visible, $active) {
    echo '<ul class="sba-list">';
    foreach ($items as $it) {
        if (!$visible($it)) {
            continue;
        }
        if (isset($it['children'])) {
            $open = $active($it);
            echo '<li class="sba-group">';
            echo '<details' . ($open ? ' open' : '') . '>';
            echo '<summary class="sba-toggle">' . h($it['label']) . '</summary>';
            $render($it['children']);
            echo '</details></li>';
        } else {
            $isActive = $active($it);
            $attrs = ['fullBase' => true, 'class' => 'sba-link' . ($isActive ? ' sba-active' : '')];
            if ($isActive) {
                $attrs['aria-current'] = 'page';
            }
            echo '<li>' . $this->Html->link($it['label'], $it['url'], $attrs) . '</li>';
        }
    }
    echo '</ul>';
};
?>
<div class="sidebar-bg"></div>
<div id="sidebar" class="sidebar">
    <nav class="sba-nav" aria-label="Menu utama">
        <a href="#content" class="sba-skip" id="sba-skip">Lompat ke konten utama</a>
        <?php $render($menu); ?>
    </nav>
</div>

<style>
.sba-nav { height: 100%; overflow-y: auto; padding: 10px 0 60px; }
.sba-nav ul { list-style: none; margin: 0; padding: 0; }
.sba-nav ul ul { padding-left: 14px; }
.sba-toggle, .sba-link {
    display: flex; align-items: center; justify-content: space-between; width: 100%;
    padding: 10px 16px; border: 0; background: transparent; color: #fff;
    text-align: left; font-size: 14px; text-decoration: none; cursor: pointer;
}
.sba-toggle:hover, .sba-link:hover { background: rgba(255,255,255,.12); color: #fff; }
.sba-toggle:focus, .sba-link:focus, .sba-skip:focus {
    outline: 3px solid #ffc107; outline-offset: -3px; background: rgba(255,255,255,.18);
}
.sba-active { background: rgba(255,255,255,.2); font-weight: 700; }
summary.sba-toggle { list-style: none; }
summary.sba-toggle::-webkit-details-marker { display: none; }
summary.sba-toggle::after { content: "\25B8"; }
details[open] > summary.sba-toggle::after { content: "\25BE"; }
.sba-nav details ul { display: block !important; visibility: visible !important; }
.sba-skip { position: absolute; left: -9999px; }
.sba-skip:focus { position: static; display: block; padding: 10px 16px; color: #fff; }
</style>

<script>
(function () {
    var skip = document.getElementById('sba-skip');
    if (skip) {
        skip.addEventListener('click', function (e) {
            var c = document.getElementById('content') || document.querySelector('h1');
            if (c) { e.preventDefault(); c.setAttribute('tabindex', '-1'); c.focus(); }
        });
    }
})();
</script>