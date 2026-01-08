<?php
require_once __DIR__ . '/includes/header.php';

$eventsCount = (int)db()->query('SELECT COUNT(*) AS c FROM events')->fetch()['c'];
$deptCount = (int)db()->query('SELECT COUNT(*) AS c FROM departments')->fetch()['c'];
$photosCount = (int)db()->query('SELECT COUNT(*) AS c FROM event_photos')->fetch()['c'];

// Check if the 'featured' column exists before querying it
$hasFeaturedColumn = db()->query("SHOW COLUMNS FROM events LIKE 'featured'")->fetch();

if ($hasFeaturedColumn) {
    $featured = db()->query(
        'SELECT e.id, e.title, e.event_type, e.event_date, e.venue, d.name AS department_name,
         (SELECT ep.file_path FROM event_photos ep WHERE ep.event_id = e.id ORDER BY ep.id DESC LIMIT 1) AS image_path
         FROM events e
         JOIN departments d ON d.id = e.department_id
         WHERE e.featured = 1
         ORDER BY e.event_date DESC, e.id DESC
         LIMIT 3'
    )->fetchAll();
} else {
    $featured = [];
}

$latest = db()->query(
    'SELECT e.id, e.title, e.event_type, e.event_date, e.venue, d.name AS department_name,
     (SELECT ep.file_path FROM event_photos ep WHERE ep.event_id = e.id ORDER BY ep.id DESC LIMIT 1) AS image_path
     FROM events e
     JOIN departments d ON d.id = e.department_id
     ORDER BY e.event_date DESC, e.id DESC
     LIMIT 9'
)->fetchAll();

$departments = db()->query(
    "SELECT id, name, short_name
     FROM departments
     WHERE NOT (name = 'NSC' OR short_name = 'NSC')
     ORDER BY name"
)->fetchAll();
?>


<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">

    <h1>Deogiri College Event Report Portal</h1>
    <p>View college event reports department-wise, with details and downloadable reports.</p>

    <div class="stats">

      <div class="stat"><strong><?php echo $eventsCount; ?></strong><span>Events</span></div>
      <div class="stat"><strong><?php echo $deptCount; ?></strong><span>Departments</span></div>
      <div class="stat"><strong><?php echo $photosCount; ?></strong><span>Photos</span></div>
    </div>

    <div class="hero-actions">
      <a class="btn primary" href="<?php echo BASE_URL; ?>/events.php">See All Events</a>
      <?php if (is_admin_logged_in()): ?>
        <a class="btn" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Admin Dashboard</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/admin/logout.php">Logout</a>
      <?php else: ?>
        <a class="btn" href="<?php echo BASE_URL; ?>/admin/login.php">Admin Login</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Department Info Section -->
<h2 class="section-title">Our Departments</h2>
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
  <?php foreach ($departments as $dept): 
    // Set department icon based on department name
    $icon = '';
    if (stripos($dept['name'], 'Computer Science') !== false || stripos($dept['short_name'], 'CS') !== false) {
      $icon = '💻'; // Computer Science icon
    } elseif (stripos($dept['name'], 'Information Technology') !== false || stripos($dept['short_name'], 'IT') !== false) {
      $icon = '📡'; // Information Technology icon
    } elseif (stripos($dept['name'], 'Animation') !== false || stripos($dept['short_name'], 'ANIM') !== false) {
      $icon = '🎨'; // Animation icon
    } elseif (stripos($dept['name'], 'Data Science') !== false || stripos($dept['short_name'], 'DS') !== false) {
      $icon = '📊'; // Data Science icon
    } elseif (stripos($dept['name'], 'Management') !== false || in_array(strtolower($dept['short_name']), ['mba', 'bba'])) {
      $icon = '💼'; // Management icon
    } elseif (stripos($dept['name'], 'Commerce') !== false || stripos($dept['short_name'], 'COM') !== false) {
      $icon = '💰'; // Commerce icon
    } elseif (stripos($dept['name'], 'Computer') !== false || stripos($dept['short_name'], 'BCA') !== false) {
      $icon = '🖥️'; // Computer Applications icon
    } else {
      $icon = '🎓'; // Default education icon
    }
  ?>
    <a href="<?php echo BASE_URL; ?>/events.php?department_id=<?php echo (int)$dept['id']; ?>" style="text-decoration: none; color: inherit;">
    <div class="card department-card" style="cursor: pointer;">
      <h3><span style="margin-right: 10px;"><?php echo $icon; ?></span><?php echo h($dept['name']); ?></h3>
      <p><strong><?php echo h($dept['short_name']); ?></strong></p>
      <p class="muted">Explore department-wise event reports, photos, and downloadable PDFs.</p>
    </div>
    </a>
  <?php endforeach; ?>
</div>

<?php if ($featured): ?>
<h2 class="section-title">Featured Events</h2>
<div class="grid">
  <?php foreach ($featured as $e): ?>
    <article class="card event-card featured-card" style="grid-column: span 4">
      <?php if ($e['image_path']): ?>
        <div style="margin-bottom: 12px; text-align: center;">
          <img src="<?php echo h($e['image_path']); ?>" alt="<?php echo h($e['title']); ?>" style="max-width: 100%; width:100%; height: 180px; object-fit: cover; border-radius: 12px;">
        </div>
      <?php endif; ?>
      <div class="badge">Featured</div>
      <h3 style="margin:10px 0 6px"><?php echo h($e['title']); ?></h3>
      <div class="muted" style="display: flex; flex-direction: column; gap: 2px;"><div><?php echo h($e['event_type']); ?></div><div><?php echo h($e['department_name']); ?> | <?php echo h($e['venue']); ?></div></div>
      <div style="margin-top:8px" class="muted"><?php echo h($e['event_date']); ?></div>
      <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn" href="<?php echo BASE_URL; ?>/event.php?id=<?php echo (int)$e['id']; ?>">View Details</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/event.php?id=<?php echo (int)$e['id']; ?>&pdf=1">Download PDF</a>
      </div>
    </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<h2 class="section-title">Latest Events</h2>
<div class="grid">
  <?php if (!$latest): ?>
    <div class="card" style="grid-column:1/-1;text-align:center">
      <div style="font-weight:900;font-size:18px">No events added yet</div>
      <div class="muted" style="margin-top:6px">Faculty can login and add events, then students can view and download PDFs.</div>
      <div style="margin-top:12px"><a class="btn primary" href="<?php echo BASE_URL; ?>/admin/login.php">Admin Login</a></div>
    </div>
  <?php endif; ?>

  <?php foreach ($latest as $e): ?>
    <article class="card event-card" style="grid-column: span 4">
      <?php if ($e['image_path']): ?>
        <div style="margin-bottom: 12px; text-align: center;">
          <img src="<?php echo h($e['image_path']); ?>" alt="<?php echo h($e['title']); ?>" style="max-width: 100%; width:100%; height: 180px; object-fit: cover; border-radius: 12px;">
        </div>
      <?php endif; ?>
      <div class="badge"><?php echo h($e['event_type']); ?></div>
      <h3 style="margin:10px 0 6px"><?php echo h($e['title']); ?></h3>
      <div class="muted" style="display: flex; flex-direction: column; gap: 2px;"><div><?php echo h($e['department_name']); ?></div><div><?php echo h($e['venue']); ?></div></div>
      <div style="margin-top:8px" class="muted"><?php echo h($e['event_date']); ?></div>
      <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap">
        <a class="btn" href="<?php echo BASE_URL; ?>/event.php?id=<?php echo (int)$e['id']; ?>">View Details</a>
        <a class="btn" href="<?php echo BASE_URL; ?>/event.php?id=<?php echo (int)$e['id']; ?>&pdf=1">Download PDF</a>
      </div>
    </article>
  <?php endforeach; ?>
</div>

<div style="margin-top:16px;display:flex;justify-content:center">
  <a class="btn primary" href="<?php echo BASE_URL; ?>/events.php">See All Events</a>
</div>

<!-- Department Events Section -->
<h2 class="section-title">Department Events</h2>
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
  <?php foreach ($departments as $dept): ?>
    <div class="card">
      <h3 style="margin-top:0;"><?php echo h($dept['name']); ?> Events</h3>
      <?php 
      $deptEvents = db()->prepare(
          'SELECT e.id, e.title, e.event_date, e.venue, e.event_type
           FROM events e
           WHERE e.department_id = ?
           ORDER BY e.event_date DESC
           LIMIT 3'
      );
      $deptEvents->execute([(int)$dept['id']]);
      $deptEventsList = $deptEvents->fetchAll();
      ?>
      <?php if ($deptEventsList): ?>
        <div style="margin-top: 10px;">
          <?php foreach ($deptEventsList as $event): ?>
            <div style="margin-bottom: 8px; padding: 8px; border-left: 3px solid var(--primary); background: rgba(var(--primary-rgb), 0.06); border-radius: 4px;">
              <div style="font-weight: 600; font-size: 0.95rem;"><?php echo h($event['title']); ?></div>
              <div class="muted" style="font-size: 0.85rem; margin-top: 2px;"><?php echo h($event['event_date']); ?> | <?php echo h($event['event_type']); ?></div>
              <div class="muted" style="font-size: 0.85rem;"><?php echo h($event['venue']); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
        <div style="margin-top: 10px; text-align: center;">
          <a class="btn" href="<?php echo BASE_URL; ?>/events.php?department_id=<?php echo (int)$dept['id']; ?>">View All <?php echo h($dept['short_name']); ?> Events</a>
        </div>
      <?php else: ?>
        <div class="muted" style="text-align: center; padding: 15px;">No events available for this department yet.</div>
        <div style="margin-top: 10px; text-align: center;">
          <a class="btn" href="<?php echo BASE_URL; ?>/events.php?department_id=<?php echo (int)$dept['id']; ?>">View <?php echo h($dept['short_name']); ?> Events</a>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<!-- About Our Event Portal Section -->
<section class="about-portal">
  <h2 class="section-title">About Our Event Portal</h2>
  <div class="card">
    <p>Deogiri College Event Portal is designed to showcase all college events, activities, and reports in one centralized location. This portal helps students, faculty, and staff stay updated with the latest events happening in different departments of our college.</p>
    <p>Our portal provides comprehensive event management features including event registration, photo galleries, detailed reports, and downloadable resources. Faculty members can create and manage events, while students can browse, search, and access event details and reports.</p>
    <div class="features-grid">
      <div class="feature">
        <h4>For Students</h4>
        <ul>
          <li>Browse upcoming events</li>
          <li>Access event reports</li>
          <li>View photo galleries</li>
          <li>Download resources</li>
        </ul>
      </div>
      <div class="feature">
        <h4>For Faculty</h4>
        <ul>
          <li>Create and manage events</li>
          <li>Upload event photos</li>
          <li>Generate event reports</li>
          <li>Track event participation</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Contact Us Section -->
<section class="contact-us">
  <h2 class="section-title">Contact Us</h2>
  <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
    <div class="card contact-card">
      <h3>📍 Address</h3>
      <p>Deogiri College<br>Aurangabad Road<br>Nanded, Maharashtra - 431602</p>
    </div>
    <div class="card contact-card">
      <h3>📞 Phone & Email</h3>
      <p>Phone: +91-724-244-7777<br>Email: info@deogiricollege.edu.in</p>
    </div>
    <div class="card contact-card">
      <h3>🏢 Find Us</h3>
      <p>Visit our campus<br>Office Hours: Mon-Fri, 9:00 AM - 5:00 PM</p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>