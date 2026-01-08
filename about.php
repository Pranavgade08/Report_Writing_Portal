<?php
require_once __DIR__ . '/includes/header.php';
$title = 'About - ' . APP_NAME;
?>

<h2 class="section-title">About Our Event Portal</h2>

<section class="card about-section">
  <h3 style="margin-top:0">Why this portal?</h3>
  <p class="muted">
    Deogiri College Event Report Portal is designed to showcase all college events, activities, and reports in one place.
    It helps students and faculty stay updated with events from different departments.
  </p>

  <div class="grid" style="margin-top:16px">
    <div class="card" style="grid-column: span 6">
      <h4 style="margin-top:0;color:var(--primary)">For Students</h4>
      <ul style="margin:0;padding-left:18px" class="muted">
        <li>View all events without login</li>
        <li>Search and filter events easily</li>
        <li>Open event details and photo gallery</li>
        <li>Download event report PDF</li>
      </ul>
    </div>
    <div class="card" style="grid-column: span 6">
      <h4 style="margin-top:0;color:var(--primary)">For Faculty</h4>
      <ul style="margin:0;padding-left:18px" class="muted">
        <li>Secure admin login</li>
        <li>Add, edit and delete events</li>
        <li>Upload multiple photos event-wise</li>
        <li>Maintain event reports for academic records</li>
      </ul>
    </div>
  </div>
</section>

<section class="card" style="margin-top:14px">
  <h3 style="margin-top:0">About Deogiri College</h3>
  <p class="muted">
    Deogiri College is committed to providing a platform where students and faculty can showcase their talents,
    participate in various events, and stay connected with the college community.
  </p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
