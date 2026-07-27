<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['staff']);

// Fetch staff record to get staff_id
$staffStmt = $pdo->prepare('SELECT id FROM staff WHERE user_id = ?');
$staffStmt->execute([$user['id']]);
$staffRecord = $staffStmt->fetch();
$staffId = $staffRecord ? (int)$staffRecord['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && !empty($_POST['qualification_id'])) {
        $deleteStmt = $pdo->prepare('DELETE FROM qualifications WHERE id = ? AND staff_id = ?');
        $deleteStmt->execute([$_POST['qualification_id'], $staffId]);
        flash('success', 'Qualification removed successfully.');
    } elseif (isset($_POST['action']) && $_POST['action'] === 'add') {
        $institution = clean_string($_POST['institution'] ?? '');
        $qualification = clean_string($_POST['qualification'] ?? '');
        $year = (int)($_POST['year_awarded'] ?? 0);
        
        if ($institution && $qualification && $year > 1900 && $year <= (int)date('Y')) {
            $insertStmt = $pdo->prepare('INSERT INTO qualifications (staff_id, institution, qualification, year_awarded) VALUES (?, ?, ?, ?)');
            $insertStmt->execute([$staffId, $institution, $qualification, $year]);
            flash('success', 'Qualification added successfully.');
        } else {
            flash('error', 'Please provide valid qualification details.');
        }
    }
    redirect('staff/qualifications.php');
}

$statement = $pdo->prepare('SELECT qualifications.* FROM qualifications INNER JOIN staff ON staff.id = qualifications.staff_id WHERE staff.user_id = ? ORDER BY year_awarded DESC');
$statement->execute([$user['id']]);
$qualifications = $statement->fetchAll();
$pageTitle = 'Qualifications';
$items = ['Dashboard' => 'staff/dashboard.php', 'Profile' => 'staff/profile.php', 'Qualifications' => 'staff/qualifications.php', 'Courses' => 'staff/courses.php', 'Timetable' => 'staff/timetable.php', 'Announcements' => 'staff/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Staff</p>
                <h1>Qualifications</h1>
            </div>
        </div>
        
        <section class="panel">
            <h2>Add Qualification</h2>
            <form method="post" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; margin-top: 1rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                
                <label style="flex: 1; min-width: 200px;">
                    Institution
                    <input type="text" name="institution" required placeholder="e.g. University of Benin" style="width: 100%; margin-top: 0.5rem; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                </label>
                
                <label style="flex: 1; min-width: 200px;">
                    Qualification
                    <input type="text" name="qualification" required placeholder="e.g. MSc Computer Science" style="width: 100%; margin-top: 0.5rem; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                </label>
                
                <label style="width: 120px;">
                    Year
                    <input type="number" name="year_awarded" required min="1900" max="<?= date('Y') ?>" placeholder="YYYY" style="width: 100%; margin-top: 0.5rem; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
                </label>
                
                <button type="submit" class="button button-primary" style="height: fit-content; padding: 0.6rem 1rem;">Add Qualification</button>
            </form>
        </section>

        <section class="panel" style="margin-top: 2rem;">
            <h2>Existing Qualifications</h2>
            <?php if (empty($qualifications)): ?>
                <p style="margin-top: 1rem; color: var(--text-muted);">No qualifications added yet.</p>
            <?php else: ?>
                <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($qualifications as $item): ?>
                        <div class="compact-row" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border: 1px solid var(--border); border-radius: 6px;">
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;"><?= e($item['institution']) ?></span>
                                <strong><?= e($item['qualification']) ?>, <?= e((string) $item['year_awarded']) ?></strong>
                            </div>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this qualification?');" style="margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="qualification_id" value="<?= (int)$item['id'] ?>">
                                <button type="submit" class="button" style="color: #dc2626; border: 1px solid #fca5a5; background: transparent; padding: 0.4rem 0.8rem; font-size: 0.875rem;">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
