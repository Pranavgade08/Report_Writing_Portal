<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$eventsCount = (int)db()->query('SELECT COUNT(*) AS c FROM events')->fetch()['c'];
$photosCount = (int)db()->query('SELECT COUNT(*) AS c FROM event_photos')->fetch()['c'];

$deptWise = db()->query(
    'SELECT d.name, COUNT(e.id) AS total
     FROM departments d
     LEFT JOIN events e ON e.department_id = d.id
     GROUP BY d.id
     ORDER BY total DESC, d.name ASC'
)->fetchAll();

$recent = db()->query(
    'SELECT e.id, e.title, e.event_date, d.name AS department_name
     FROM events e
     JOIN departments d ON d.id = e.department_id
     ORDER BY e.created_at DESC, e.id DESC
     LIMIT 6'
)->fetchAll();

$title = 'Admin Dashboard - ' . APP_NAME;
?>

<h2 class="section-title">Admin Dashboard</h2>

<section class="card admin-dashboard">
  <div class="admin-stats">
    <div class="admin-stat">
      <div class="muted">Total Events</div>
      <div><?php echo $eventsCount; ?></div>
    </div>
    <div class="admin-stat">
      <div class="muted">Total Photos</div>
      <div><?php echo $photosCount; ?></div>
    </div>
    <div class="admin-stat">
      <div class="muted">Logged in as</div>
      <div><?php echo h((string)($_SESSION['admin_name'] ?? 'Admin')); ?></div>
    </div>
  </div>

  <div class="admin-quick-links">
    <a class="btn primary" href="<?php echo BASE_URL; ?>/admin/events.php">Manage Events</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/admin/event_form.php">Add New Event</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/admin/academic_years.php">Manage Academic Years</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/admin/departments.php">Manage Departments</a>
    <a class="btn" href="<?php echo BASE_URL; ?>/admin/activity_logs.php">View Activity Logs</a>
  </div>
</section>

<div class="grid" style="margin-top:14px">
  <section class="card" style="grid-column: span 6">
    <h3 style="margin-top:0">Events Department-wise</h3>
    <table class="table">
      <thead>
        <tr><th>Department</th><th>Total</th></tr>
      </thead>
      <tbody>
        <?php foreach ($deptWise as $row): ?>
          <tr>
            <td><?php echo h($row['name']); ?></td>
            <td><?php echo (int)$row['total']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="card" style="grid-column: span 6">
    <h3 style="margin-top:0">Recently Added</h3>
    <table class="table">
      <thead>
        <tr><th>Event</th><th>Date</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php if (!$recent): ?>
          <tr><td colspan="3" class="muted">No events yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($recent as $e): ?>
          <tr>
            <td><?php echo h($e['title']); ?><div class="muted"><?php echo h($e['department_name']); ?></div></td>
            <td><?php echo h($e['event_date']); ?></td>
            <td><a class="btn" href="<?php echo BASE_URL; ?>/admin/event_form.php?id=<?php echo (int)$e['id']; ?>">Edit</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
