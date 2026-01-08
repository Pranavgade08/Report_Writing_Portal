<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Check if the 'featured' column exists before querying it
$hasFeaturedColumn = db()->query("SHOW COLUMNS FROM events LIKE 'featured'")->fetch();

if ($hasFeaturedColumn) {
    $events = db()->query(
        'SELECT e.id, e.title, e.event_type, e.event_date, e.featured, d.name AS department_name, y.year_label
         FROM events e
         JOIN departments d ON d.id = e.department_id
         JOIN academic_years y ON y.id = e.academic_year_id
         ORDER BY e.event_date DESC, e.id DESC'
    )->fetchAll();
} else {
    $events = db()->query(
        'SELECT e.id, e.title, e.event_type, e.event_date, 0 as featured, d.name AS department_name, y.year_label
         FROM events e
         JOIN departments d ON d.id = e.department_id
         JOIN academic_years y ON y.id = e.academic_year_id
         ORDER BY e.event_date DESC, e.id DESC'
    )->fetchAll();
}

$title = 'Manage Events - ' . APP_NAME;
?>

<h2 class="section-title">Manage Events</h2>

<section class="card">
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between">
    <div class="muted">Add, edit, delete events and later upload photos.</div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <a class="btn" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Back</a>
      <a class="btn primary" href="<?php echo BASE_URL; ?>/admin/event_form.php">Add New Event</a>
    </div>
  </div>
</section>

<section style="margin-top:14px">
  <table class="table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Type</th>
        <th>Date</th>
        <th>Department</th>
        <th>Year</th>
        <th>Featured</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$events): ?>
        <tr><td colspan="7" class="muted">No events added yet.</td></tr>
      <?php endif; ?>

      <?php foreach ($events as $e): ?>
        <tr>
          <td><?php echo h($e['title']); ?></td>
          <td><span class="badge"><?php echo h($e['event_type']); ?></span></td>
          <td><?php echo h($e['event_date']); ?></td>
          <td><?php echo h($e['department_name']); ?></td>
          <td><?php echo h($e['year_label']); ?></td>
          <td>
            <a class="btn <?php echo $e['featured'] ? 'primary' : 'danger'; ?>" href="<?php echo BASE_URL; ?>/admin/toggle_featured.php?id=<?php echo (int)$e['id']; ?>">
              <?php echo $e['featured'] ? 'Unfeature' : 'Feature'; ?>
            </a>
          </td>
          <td style="display:flex;gap:10px;flex-wrap:wrap">
            <a class="btn" href="<?php echo BASE_URL; ?>/admin/event_form.php?id=<?php echo (int)$e['id']; ?>">Edit</a>
            <a class="btn" href="<?php echo BASE_URL; ?>/admin/photos.php?event_id=<?php echo (int)$e['id']; ?>">Photos</a>
            <a class="btn danger" data-confirm="Delete this event?" href="<?php echo BASE_URL; ?>/admin/event_delete.php?id=<?php echo (int)$e['id']; ?>">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
