<?php
require_once __DIR__ . '/includes/header.php';

$q = trim((string)($_GET['q'] ?? ''));
$dept = (int)($_GET['department_id'] ?? 0);
$year = (int)($_GET['academic_year_id'] ?? 0);
$type = trim((string)($_GET['event_type'] ?? ''));

$departments = db()->query(
    "SELECT id, name, short_name
     FROM departments
     WHERE NOT (name = 'NSC' OR short_name = 'NSC')
     ORDER BY name"
)->fetchAll();
$years = db()->query('SELECT id, year_label FROM academic_years WHERE is_active = 1 ORDER BY year_label DESC')->fetchAll();

$where = [];
$params = [];

$where[] = "NOT (d.name = 'NSC' OR d.short_name = 'NSC')";

if ($q !== '') {
    $where[] = 'e.title LIKE ?';
    $params[] = '%' . $q . '%';
}
if ($dept > 0) {
    $where[] = 'e.department_id = ?';
    $params[] = $dept;
}
if ($year > 0) {
    $where[] = 'e.academic_year_id = ?';
    $params[] = $year;
}
if ($type !== '') {
    $where[] = 'e.event_type = ?';
    $params[] = $type;
}

$sql =
    'SELECT e.id, e.title, e.event_type, e.event_date, e.venue, d.name AS department_name, y.year_label
     FROM events e
     JOIN departments d ON d.id = e.department_id
     JOIN academic_years y ON y.id = e.academic_year_id';

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY e.event_date DESC, e.id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

$title = 'Events - ' . APP_NAME;
?>

<h2 class="section-title">Browse Events</h2>

<section class="card">
  <form method="get" class="filters">
    <div class="field" style="min-width:240px">
      <label>Search by title</label>
      <input class="input" name="q" value="<?php echo h($q); ?>" placeholder="Eg: Workshop" />
    </div>

    <div class="field">
      <label>Department</label>
      <select name="department_id">
        <option value="0">All</option>
        <?php foreach ($departments as $d): ?>
          <option value="<?php echo (int)$d['id']; ?>" <?php echo $dept === (int)$d['id'] ? 'selected' : ''; ?>><?php echo h($d['name']); ?> (<?php echo h($d['short_name']); ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="field">
      <label>Academic year</label>
      <select name="academic_year_id">
        <option value="0">All</option>
        <?php foreach ($years as $y): ?>
          <option value="<?php echo (int)$y['id']; ?>" <?php echo $year === (int)$y['id'] ? 'selected' : ''; ?>><?php echo h($y['year_label']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="field">
      <label>Event type</label>
      <select name="event_type">
        <option value="">All</option>
        <?php foreach (['Seminar','Workshop','Sports','Cultural','Other'] as $t): ?>
          <option value="<?php echo h($t); ?>" <?php echo $type === $t ? 'selected' : ''; ?>><?php echo h($t); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="field">
      <label>&nbsp;</label>
      <button class="btn primary" type="submit">Apply</button>
    </div>

    <div class="field">
      <label>&nbsp;</label>
      <a class="btn" href="<?php echo BASE_URL; ?>/events.php">Reset</a>
    </div>
  </form>
</section>

<section style="margin-top:14px">
  <table class="table">
    <thead>
      <tr>
        <th>Event</th>
        <th>Type</th>
        <th>Date</th>
        <th>Department</th>
        <th>Year</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$events): ?>
        <tr>
          <td colspan="6" class="muted">No events found.</td>
        </tr>
      <?php endif; ?>

      <?php foreach ($events as $e): ?>
        <tr>
          <td><?php echo h($e['title']); ?><div class="muted"><?php echo h($e['venue']); ?></div></td>
          <td><span class="badge"><?php echo h($e['event_type']); ?></span></td>
          <td><?php echo h($e['event_date']); ?></td>
          <td><?php echo h($e['department_name']); ?></td>
          <td><?php echo h($e['year_label']); ?></td>
          <td>
            <a class="btn" href="<?php echo BASE_URL; ?>/event.php?id=<?php echo (int)$e['id']; ?>">View</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
