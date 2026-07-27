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
    } elseif (isset($_POST['action']) && $_POST['action'] === 'edit' && !empty($_POST['qualification_id'])) {
        $qualificationId = (int)$_POST['qualification_id'];
        $institution = clean_string($_POST['institution'] ?? '');
        $qualification = clean_string($_POST['qualification'] ?? '');
        $year = (int)($_POST['year_awarded'] ?? 0);
        
        if ($institution && $qualification && $year > 1900 && $year <= (int)date('Y')) {
            $updateStmt = $pdo->prepare('UPDATE qualifications SET institution = ?, qualification = ?, year_awarded = ? WHERE id = ? AND staff_id = ?');
            $updateStmt->execute([$institution, $qualification, $year, $qualificationId, $staffId]);
            flash('success', 'Qualification updated successfully.');
        } else {
            flash('error', 'Please provide valid qualification details.');
        }
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
$items = ['Dashboard' => 'staff/dashboard.php', 'Profile' => 'staff/profile.php', 'Qualifications' => 'staff/qualifications.php', 'Courses' => 'staff/courses.php', 'Timetable' => 'staff/timetable.php', 'Documents' => 'staff/documents.php', 'Announcements' => 'staff/announcements.php'];
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
        
        <form class="panel form-grid" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <label>Institution <input name="institution" required placeholder="e.g. University of Benin"></label>
            <label>Qualification <input name="qualification" required placeholder="e.g. MSc Computer Science"></label>
            <label>Year <input type="number" name="year_awarded" required min="1900" max="<?= date('Y') ?>" placeholder="YYYY"></label>
            <button class="cta-button" type="submit" style="align-self: end; justify-self: start;">Add Qualification</button>
        </form>

        <section class="panel table-panel">
            <table>
                <thead>
                    <tr>
                        <th>Institution</th>
                        <th>Qualification</th>
                        <th>Year</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($qualifications as $item): ?>
                    <tr>
                        <td><?= e($item['institution']) ?></td>
                        <td><?= e($item['qualification']) ?></td>
                        <td><?= e((string) $item['year_awarded']) ?></td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" class="button button-light" style="padding: 6px 12px; font-size: 13px;" onclick="editQualification(<?= htmlspecialchars(json_encode($item)) ?>)">Edit</button>
                                <form class="inline-form" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="qualification_id" value="<?= (int)$item['id'] ?>">
                                    <button type="submit" class="button button-light" style="padding: 6px 12px; font-size: 13px; color: var(--danger); border-color: var(--danger);" onclick="return confirm('Are you sure you want to delete this qualification?');">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </section>
</div>

<dialog id="editDialog" class="panel" style="padding: 32px; border: 1px solid var(--line); border-radius: var(--radius); width: 400px; max-width: 90vw; max-height: 90vh; top: 50%; left: 50%; transform: translate(-50%, -50%); margin: 0; background: var(--bg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="margin: 0;">Edit Qualification</h3>
        <button type="button" onclick="document.getElementById('editDialog').close()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--muted);">&times;</button>
    </div>
    <form method="post" style="display: grid; gap: 16px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="qualification_id" id="edit_id">
        <label>Institution <input name="institution" id="edit_institution" required></label>
        <label>Qualification <input name="qualification" id="edit_qualification" required></label>
        <label>Year <input type="number" name="year_awarded" id="edit_year" min="1900" max="<?= date('Y') ?>" required></label>
        <button type="submit" class="cta-button" style="margin-top: 16px;">Save Changes</button>
    </form>
</dialog>

<script>
function editQualification(qualification) {
    document.getElementById('edit_id').value = qualification.id;
    document.getElementById('edit_institution').value = qualification.institution;
    document.getElementById('edit_qualification').value = qualification.qualification;
    document.getElementById('edit_year').value = qualification.year_awarded;
    document.getElementById('editDialog').showModal();
}
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>
