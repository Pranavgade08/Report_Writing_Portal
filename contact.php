<?php
require_once __DIR__ . '/includes/header.php';
$title = 'Contact - ' . APP_NAME;
?>

<h2 class="section-title">Contact Us</h2>

<section class="card contact-section">
  <div class="grid">
    <div class="card" style="grid-column: span 6">
      <h3 style="margin-top:0">Address</h3>
      <div class="muted">
        Deogiri College<br />
        Aurangabad Road<br />
        Nanded, Maharashtra - 431602
      </div>
    </div>

    <div class="card" style="grid-column: span 6">
      <h3 style="margin-top:0">Phone & Email</h3>
      <div class="muted">
        Phone: +91-724-244-7777<br />
        Email: info@deogiricollege.edu.in
      </div>
    </div>

    <div class="card" style="grid-column: span 6">
      <h3 style="margin-top:0">Office Hours</h3>
      <div class="muted">Mon-Fri, 9:00 AM - 5:00 PM</div>
    </div>

    <div class="card" style="grid-column: span 6">
      <h3 style="margin-top:0">Campus Image</h3>
      <div class="muted">Upload a campus image in:</div>
      <div class="muted" style="margin-top:6px"><strong>assets/images/campus.jpg</strong></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
