<?php
$items = $items ?? [];
$profileName = current_user()['name'] ?? 'User';
?>
<aside class="sidebar app-pane">
    <div class="profile-pill">
        <span><?= e(initials($profileName)) ?></span>
        <strong><?= e($profileName) ?></strong>
    </div>
    <nav class="side-nav">
        <?php foreach ($items as $label => $href): ?>
            <a class="<?= active_link($href) ?>" href="<?= url($href) ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
</aside>
