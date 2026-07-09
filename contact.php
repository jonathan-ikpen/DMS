<?php
require_once __DIR__ . '/config/app.php';
$pageTitle = 'Contact';
include __DIR__ . '/components/header.php';
?>
<section class="page-heading">
    <p class="eyebrow">Contact</p>
    <h1>Reach the department office.</h1>
    <p>Email the department administrator or visit the Computer Science office for support with registration, payments, and document requirements.</p>
</section>
<section class="contact-grid">
    <article><h3>Email</h3><p>department@example.edu.ng</p></article>
    <article><h3>Office</h3><p>Computer Science Department, Petroleum Training Institute, Effurun.</p></article>
    <article><h3>Hours</h3><p>Monday to Friday, 9:00 AM - 4:00 PM</p></article>
</section>
<?php include __DIR__ . '/components/footer.php'; ?>
