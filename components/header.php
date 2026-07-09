<?php
$pageTitle = $pageTitle ?? APP_NAME;
$bodyClass = $bodyClass ?? '';
$user = function_exists('current_user') ? current_user() : null;
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="<?= url('assets/js/app.js') ?>"></script>
    <script>
        const theme = localStorage.getItem('dms-theme');
        if (theme) document.documentElement.dataset.theme = theme;
    </script>
</head>
<body class="<?= e($bodyClass) ?>">
<header class="topbar">
        <a class="brand" href="<?= url('index.php') ?>">
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: var(--accent); border-radius: 9999px; color: #fff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                    <path d="M2 17l10 5 10-5"></path>
                    <path d="M2 12l10 5 10-5"></path>
                </svg>
            </span>
            <span style="font-family: 'Anton', sans-serif; font-size: 20px; letter-spacing: 0.05em; color: var(--ink);">DMS</span>
        </a>
    <div style="display: flex; align-items: center; gap: 16px;">
        <button type="button" class="menu-toggle" onclick="document.querySelector('.nav').classList.toggle('nav-open')" aria-label="Toggle menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="9" x2="20" y2="9"></line>
                <line x1="4" y1="15" x2="14" y2="15"></line>
            </svg>
        </button>
        <nav class="nav">
            <button type="button" class="nav-close" aria-label="Close menu" onclick="this.parentElement.classList.remove('nav-open')">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <?php if (current_user() && isset($items) && is_array($items)): ?>
                <div class="mobile-dashboard-nav">
                    <span class="eyebrow" style="display: block; margin-bottom: 8px;">Workspace</span>
                    <?php foreach ($items as $label => $href): ?>
                        <a href="<?= url($href) ?>"><?= e($label) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <a href="<?= url('index.php#features') ?>">Features</a>
            <a href="<?= url('announcements.php') ?>">Announcements</a>
            <a href="<?= url('contact.php') ?>">Contact</a>
            <?php if (current_user()): ?>
                <a href="<?= url(current_user()['role'] . '/dashboard.php') ?>">Dashboard</a>
                <a href="<?= url('auth/logout.php') ?>">Logout</a>
            <?php else: ?>
                <a href="<?= url('auth/login.php') ?>">Login</a>
                <a class="cta-button" style="padding: 8px 16px; margin-left: 12px; font-size: 14px;" href="<?= url('auth/register.php') ?>">Register</a>
            <?php endif; ?>
        </nav>
        <?php if (current_user()): ?>
        <div class="notifications-wrapper" style="position: relative;">
            <button type="button" class="icon-button" id="notifToggle" aria-label="Notifications">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span id="notifBadge" style="display: none; position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; background: var(--danger); border-radius: 50%;"></span>
            </button>
            <div id="notifDropdown" class="panel notif-dropdown" style="display: none; padding: 0;">
                <div style="padding: 16px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                    <h4 style="margin: 0; font-size: 14px;">Notifications</h4>
                    <button type="button" id="markAllRead" style="background: none; border: none; color: var(--accent); font-size: 12px; cursor: pointer; display: none;">Mark all read</button>
                </div>
                <div id="notifList" style="padding: 8px 0;">
                    <div style="padding: 16px; text-align: center; color: var(--muted); font-size: 14px;">Loading...</div>
                </div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('notifToggle');
            const dropdown = document.getElementById('notifDropdown');
            const badge = document.getElementById('notifBadge');
            const list = document.getElementById('notifList');
            const markAllBtn = document.getElementById('markAllRead');
            let unreadIds = [];

            if (!toggle) return;

            // Fetch on load
            fetchNotifications();
            setInterval(fetchNotifications, 60000);

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.notifications-wrapper')) {
                    dropdown.style.display = 'none';
                }
            });

            markAllBtn.addEventListener('click', async () => {
                if (unreadIds.length === 0) return;
                try {
                    await fetch('<?= url("api/notifications.php") ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: unreadIds })
                    });
                    unreadIds = [];
                    badge.style.display = 'none';
                    markAllBtn.style.display = 'none';
                    list.innerHTML = '<div style="padding: 16px; text-align: center; color: var(--muted); font-size: 14px;">No new notifications</div>';
                } catch (e) {
                    console.error('Failed to mark read', e);
                }
            });

            async function fetchNotifications() {
                try {
                    const res = await fetch('<?= url("api/notifications.php") ?>');
                    const data = await res.json();
                    
                    if (data.notifications && data.notifications.length > 0) {
                        unreadIds = data.notifications.map(n => n.id);
                        badge.style.display = 'block';
                        markAllBtn.style.display = 'block';
                        
                        list.innerHTML = data.notifications.map(n => `
                            <div style="padding: 12px 16px; border-bottom: 1px solid var(--line);">
                                <strong style="display: block; font-size: 13px; margin-bottom: 4px;">${escapeHtml(n.title)}</strong>
                                <p style="margin: 0; font-size: 13px; color: var(--muted);">${escapeHtml(n.body)}</p>
                                <small style="display: block; margin-top: 6px; color: var(--muted); font-size: 11px;">${new Date(n.created_at).toLocaleString()}</small>
                            </div>
                        `).join('');
                    } else {
                        badge.style.display = 'none';
                        markAllBtn.style.display = 'none';
                        list.innerHTML = '<div style="padding: 16px; text-align: center; color: var(--muted); font-size: 14px;">No new notifications</div>';
                    }
                } catch (e) {
                    console.error('Failed to fetch notifications', e);
                }
            }

            function escapeHtml(unsafe) {
                return (unsafe || '').replace(/[&<"']/g, function(m) {
                    switch (m) {
                        case '&': return '&amp;';
                        case '<': return '&lt;';
                        case '"': return '&quot;';
                        default: return '&#039;';
                    }
                });
            }
        });
        </script>
        <?php endif; ?>
        <button type="button" class="icon-button" data-theme-toggle aria-label="Toggle theme">
            <span class="theme-icon"></span>
        </button>
    </div>
</header>
<main>
<?php foreach (flash_messages() as $type => $messages): ?>
    <?php foreach ($messages as $message): ?>
        <div class="flash flash-<?= e($type) ?>">
            <span><?= e($message) ?></span>
            <button type="button" class="flash-close" aria-label="Close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>
