<?php
require_once __DIR__ . '/config/app.php';
$pageTitle = 'Home';
include __DIR__ . '/components/header.php';
?>

<section class="hero-section">
    <div class="pill-label"><span style="display: flex; align-items: center;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"></path><path d="M20 3v4"></path><path d="M22 5h-4"></path><path d="M4 17v2"></path><path d="M5 18H3"></path></svg></span> The Complete Department Workspace</div>
    <div class="hero-heading">
        <h1>The system that organizes your department in minutes.</h1>
    </div>
    <p class="hero-sub">DMS centralizes student records, staff profiles, Remita payments, timetables, and reports, so your department runs smoothly on autopilot.</p>
    
    <div class="hero-form">
        <input type="email" placeholder="Enter your email address" aria-label="Email address" required>
        <button class="cta-button" type="button" onclick="window.location.href='<?= url('auth/register.php') ?>'">Create Account</button>
    </div>
    <p class="hero-note">Join <span>1,000+</span> departments already using DMS.</p>
</section>

<section class="mockup-container" style="text-align: center;">
    <img src="<?= url('assets/images/dashboard-8-light.png') ?>" alt="Department Management System Dashboard Interface Mockup" class="mockup-light" style="max-width: 100%; height: auto;">
    <img src="<?= url('assets/images/dashboard-8-dark.png') ?>" alt="Department Management System Dashboard Interface Mockup" class="mockup-dark" style="max-width: 100%; height: auto;">
</section>

<section class="features-section" id="features">
    <div class="pill-label"><span style="display: flex; align-items: center;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span> Core Modules</div>
    <h2>Start managing the right records in minutes.</h2>
    
    <div class="features-grid">
        <article class="feature-card">
            <h3 style="color: var(--accent); margin-bottom: 24px;">Student Records</h3>
            <p>Keep personal data, academic level, payment status, uploaded requirements, and timetable access perfectly organized in one place.</p>
        </article>
        <article class="feature-card">
            <h3 style="color: var(--accent); margin-bottom: 24px;">Staff Operations</h3>
            <p>Maintain complete staff profiles, qualifications, CV uploads, assigned courses, and department schedule visibility effortlessly.</p>
        </article>
        <article class="feature-card">
            <h3 style="color: var(--accent); margin-bottom: 24px;">Payments & Reports</h3>
            <p>Track Remita payment references, receipts, and generate printable financial summaries or audit logs with a single click.</p>
        </article>
    </div>
</section>

<section class="faq-section">
    <div class="pill-label">Frequently Asked Questions</div>
    <h2>Your questions, answered.</h2>
    
    <div class="faq-list">
        <div class="faq-item">
            <details>
                <summary>What exactly does this system do?</summary>
                <p>It acts as a centralized workspace for your entire department. It handles student registrations, staff profiles, course assignments, timetable creation, payment tracking (like Remita), and reporting.</p>
            </details>
        </div>
        <div class="faq-item">
            <details>
                <summary>How secure is the data?</summary>
                <p>Extremely secure. We use PDO prepared statements to prevent SQL injection, secure password hashing, CSRF tokens, and robust role-based access control (RBAC) so admins, staff, and students only see what they are authorized to see.</p>
            </details>
        </div>
        <div class="faq-item">
            <details>
                <summary>Can students upload their clearance documents?</summary>
                <p>Yes. Students have a dedicated portal where they can upload required documents (like admission letters or payment receipts). Admins can then review and approve these uploads directly from the dashboard.</p>
            </details>
        </div>
        <div class="faq-item">
            <details>
                <summary>Are reports and timetables printable?</summary>
                <p>Absolutely. The system generates clean, structured reports for financial summaries, student lists, and timetables that are formatted perfectly for printing or PDF export.</p>
            </details>
        </div>
        <div class="faq-item">
            <details>
                <summary>How do I get started, and what does it cost?</summary>
                <p>Getting started takes only a few minutes. You can create an account and immediately start configuring your department. Pricing depends on your deployment needs, but you can begin with a free trial today.</p>
            </details>
        </div>
    </div>
</section>

<section class="footer-cta">
    <h2>Ready to organize your department and save time?</h2>
    <a class="cta-button" href="<?= url('auth/login.php') ?>">Sign In</a>
</section>

<?php include __DIR__ . '/components/footer.php'; ?>
