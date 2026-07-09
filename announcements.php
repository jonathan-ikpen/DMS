<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/services/DashboardService.php';
$service = new DashboardService($pdo);
$announcements = $service->announcements(20);
$pageTitle = 'Announcements';
include __DIR__ . '/components/header.php';
?>
<section class="page-heading">
    <p class="eyebrow">Announcements</p>
    <h1>Department updates</h1>
</section>
<section class="list-panel">
    <?php foreach ($announcements as $announcement): ?>
        <article class="list-item">
            <div>
                <h3><?= e($announcement['title']) ?></h3>
                <p><?= e($announcement['body']) ?></p>
            </div>
            <time><?= e(date('M d, Y', strtotime($announcement['published_at']))) ?></time>
        </article>
    <?php endforeach; ?>
    <?php if (!$announcements): ?>
        <p>No announcements have been published yet.</p>
    <?php endif; ?>
</section>
<?php include __DIR__ . '/components/footer.php'; ?>
