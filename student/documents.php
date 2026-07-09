<?php
require_once __DIR__ . '/../middleware/auth.php';
$user = require_auth(['student']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $requirementId = (int) ($_POST['requirement_id'] ?? 0);
    
    $req = $pdo->prepare("SELECT * FROM document_requirements WHERE id = ? AND audience IN ('student','all') AND is_active = 1");
    $req->execute([$requirementId]);
    $requirement = $req->fetch();
    
    if (!$requirement) {
        flash('error', 'Invalid document requirement.');
        redirect('student/documents.php');
    }
    
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['document'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = array_map('trim', explode(',', $requirement['allowed_extensions']));
        
        if (!in_array($ext, $allowed)) {
            flash('error', "Invalid file type. Allowed: " . $requirement['allowed_extensions']);
            redirect('student/documents.php');
        }
        
        if ($file['size'] > ($requirement['max_size_mb'] * 1024 * 1024)) {
            flash('error', "File exceeds maximum size of {$requirement['max_size_mb']}MB.");
            redirect('student/documents.php');
        }
        
        $filename = uniqid('doc_') . '_' . time() . '.' . $ext;
        $destination = __DIR__ . '/../storage/uploads/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Check if replacing a rejected document
            $existing = $pdo->prepare('SELECT id FROM document_uploads WHERE user_id = ? AND requirement_id = ?');
            $existing->execute([$user['id'], $requirementId]);
            if ($existing->fetch()) {
                $pdo->prepare('UPDATE document_uploads SET file_path = ?, status = "pending", created_at = CURRENT_TIMESTAMP WHERE user_id = ? AND requirement_id = ?')
                    ->execute([$filename, $user['id'], $requirementId]);
            } else {
                $pdo->prepare('INSERT INTO document_uploads (requirement_id, user_id, file_path) VALUES (?, ?, ?)')
                    ->execute([$requirementId, $user['id'], $filename]);
            }
            flash('success', 'Document uploaded successfully and is pending review.');
        } else {
            flash('error', 'Failed to save document. Please try again.');
        }
    } else {
        flash('error', 'Please select a valid file to upload.');
    }
    
    redirect('student/documents.php');
}

$requirements = $pdo->query("SELECT * FROM document_requirements WHERE audience IN ('student','all') AND is_active = 1 ORDER BY name")->fetchAll();

$uploads = $pdo->prepare('SELECT requirement_id, status, file_path FROM document_uploads WHERE user_id = ?');
$uploads->execute([$user['id']]);
$userUploads = [];
foreach ($uploads->fetchAll() as $up) {
    $userUploads[$up['requirement_id']] = $up;
}

$pageTitle = 'Documents';
$items = ['Dashboard' => 'student/dashboard.php', 'Profile' => 'student/profile.php', 'Payments' => 'student/payments.php', 'Documents' => 'student/documents.php', 'Timetable' => 'student/timetable.php', 'Announcements' => 'student/announcements.php'];
include __DIR__ . '/../components/header.php';
?>
<div class="app-layout">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <section class="workspace">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Student</p>
                <h1>Document uploads</h1>
            </div>
        </div>
        
        <section class="feature-grid">
            <?php foreach ($requirements as $requirement): ?>
                <?php $upload = $userUploads[$requirement['id']] ?? null; ?>
                <article style="display: flex; flex-direction: column;">
                    <h3 style="margin-top:0;"><?= e($requirement['name']) ?></h3>
                    <p style="flex-grow: 1;"><?= e($requirement['description']) ?></p>
                    
                    <?php if ($upload): ?>
                        <div style="padding: 16px; background: var(--soft); border-radius: var(--radius); border: 1px solid var(--line); display: grid; gap: 12px; margin-top: 16px;">
                            <p style="margin:0; font-size: 14px; font-weight: 500;">
                                Status: <span class="status"><?= ucfirst(e($upload['status'])) ?></span>
                            </p>
                            <a href="<?= url('download.php?file=' . e($upload['file_path'])) ?>" target="_blank" class="button button-dark" style="width: 100%; display: inline-flex; justify-content: center; border-radius: var(--pill-radius); font-weight: 500;">View Document</a>
                        </div>
                        <?php if ($upload['status'] === 'rejected'): ?>
                            <p style="margin-top: 16px; margin-bottom: 8px; font-size: 13px; color: var(--danger); font-weight: 500;">Please upload a valid replacement.</p>
                            <form method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="requirement_id" value="<?= e((string) $requirement['id']) ?>">
                                <input type="file" name="document" required style="margin-bottom: 12px;">
                                <button class="cta-button" type="submit" style="width: 100%;">Re-upload</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <form method="post" enctype="multipart/form-data" style="margin-top: 16px;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="requirement_id" value="<?= e((string) $requirement['id']) ?>">
                            <input type="file" name="document" required style="margin-bottom: 12px;">
                            <button class="cta-button" type="submit" style="width: 100%;">Upload</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </section>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
