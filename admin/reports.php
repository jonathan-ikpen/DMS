<?php
require_once __DIR__ . '/../middleware/auth.php';
require_auth(['admin']);
$reports = [
    'Academic & Personnel' => [
        'student_list' => ['title' => 'Student Roster', 'desc' => 'A complete printable list of all registered students, their matriculation numbers, and current levels.'],
        'staff_list' => ['title' => 'Staff Directory', 'desc' => 'A directory of all departmental staff members and their assigned roles.'],
        'timetable' => ['title' => 'Timetable', 'desc' => 'A printable weekly schedule of all classes, optionally filterable by level.', 'modal' => 'timetableModal'],
    ],
    'Financials' => [
        'financial_ledger' => ['title' => 'Financial Ledger', 'desc' => 'A comprehensive ledger detailing total revenue from payments versus departmental expenses.'],
    ]
];
$pageTitle = 'Reports';
$items = ['Dashboard' => 'admin/dashboard.php', 'Students' => 'admin/users.php?role=student', 'Staff' => 'admin/users.php?role=staff', 'Courses' => 'admin/courses.php', 'Timetable' => 'admin/timetable.php', 'Payments' => 'admin/payments.php', 'Payment Items' => 'admin/payment_items.php', 'Expenses' => 'admin/expenses.php', 'Reports' => 'admin/reports.php', 'Document Reviews' => 'admin/document_reviews.php', 'Manage Announcements' => 'admin/announcements.php', 'Settings' => 'admin/settings.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout"><?php include __DIR__ . '/../components/sidebar.php'; ?><section class="workspace">
    <div class="workspace-heading"><div><p class="eyebrow">Admin</p><h1>Printable reports</h1></div></div>
    
    <?php foreach ($reports as $category => $list): ?>
        <h2 style="margin-top: 32px; margin-bottom: 16px; font-size: 18px; border-bottom: 1px solid var(--line); padding-bottom: 8px;"><?= e($category) ?></h2>
        <section class="feature-grid">
            <?php foreach ($list as $type => $data): ?>
            <article>
                <h3><?= e($data['title']) ?></h3>
                <p><?= e($data['desc']) ?></p>
                <?php if (isset($data['modal'])): ?>
                    <button type="button" class="cta-button" style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; padding: 8px 16px;" onclick="document.getElementById('<?= $data['modal'] ?>').showModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Download PDF
                    </button>
                <?php else: ?>
                    <a class="cta-button" style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; padding: 8px 16px;" href="<?= url('admin/export.php?type=' . $type) ?>" target="_blank">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Download PDF
                    </a>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</section></div>

<dialog id="timetableModal" class="panel" style="padding: 24px; border: 1px solid var(--line); border-radius: var(--radius); width: 400px; max-width: 90vw; max-height: 90vh; margin: auto; background: var(--bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 18px;">Export Timetable</h3>
        <button type="button" onclick="this.closest('dialog').close()" style="background:none; border:none; font-size: 20px; cursor: pointer; color: var(--muted);">&times;</button>
    </div>
    <form action="<?= url('admin/export.php') ?>" method="get" target="_blank" style="display: flex; flex-direction: column; gap: 16px;">
        <input type="hidden" name="type" value="timetable">
        <label>Select Level
            <select name="level">
                <option value="Full">Full Timetable (All Levels)</option>
                <option value="ND1">ND1</option>
                <option value="ND2">ND2</option>
                <option value="HND1">HND1</option>
                <option value="HND2">HND2</option>
            </select>
        </label>
        <button type="submit" class="cta-button" onclick="this.closest('dialog').close()">Generate PDF</button>
    </form>
</dialog>

<?php include __DIR__ . '/../components/footer.php'; ?>
