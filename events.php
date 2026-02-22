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
    'SELECT e.id, e.title, e.event_type, e.event_date, e.venue, e.featured, d.name AS department_name, y.year_label
     FROM events e
     JOIN departments d ON d.id = e.department_id
     JOIN academic_years y ON y.id = e.academic_year_id';

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY e.featured DESC, e.event_date DESC, e.id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

$title = 'Events - ' . APP_NAME;
?>

<?php
// Separate featured and regular events
$featuredEvents = array_filter($events, function($e) { return isset($e['featured']) && $e['featured'] == 1; });
$regularEvents = array_filter($events, function($e) { return !isset($e['featured']) || $e['featured'] != 1; });

// Debug: Count featured events
$featuredCount = count($featuredEvents);
$regularCount = count($regularEvents);
$totalCount = count($events);
?>

<h2 class="section-title">Browse Events</h2>

<!-- Debug Info (remove in production) -->
<div style="background: #f0f9ff; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 12px;">
  <strong>Debug:</strong> Total Events: <?php echo $totalCount; ?> | Featured: <?php echo $featuredCount; ?> | Regular: <?php echo $regularCount; ?>
</div>

<?php if (!empty($featuredEvents)): ?>
<section class="featured-events-section" style="margin-bottom:30px;">
  <h3 style="margin:0 0 20px 0; display:flex;align-items:center;gap:10px;">
    <span style="background:linear-gradient(135deg, #fbbf24, #f59e0b); color:white; padding:8px 16px; border-radius:20px; font-weight:800; font-size:14px;">FEATURED</span>
    Featured Events
  </h3>
  
  <div class="featured-events-grid">
    <?php foreach ($featuredEvents as $e): ?>
      <div class="featured-event-card">
        <div class="featured-badge">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          Featured
        </div>
        <h4 style="margin:12px 0 8px; font-size:18px; line-height:1.3;"><?php echo h($e['title']); ?></h4>
        <div class="muted" style="font-size:14px; margin-bottom:12px;">
          <div style="margin-bottom:4px;">📍 <?php echo h($e['venue']); ?></div>
          <div style="margin-bottom:4px;">📅 <?php echo h($e['event_date']); ?></div>
          <div>🏛️ <?php echo h($e['department_name']); ?></div>
        </div>
        <div style="display:flex;gap:8px; align-items:center; justify-content:space-between;">
          <span class="badge" style="background:linear-gradient(135deg, #fbbf24, #f59e0b); color:white; border:none;"><?php echo h($e['event_type']); ?></span>
          <a class="btn primary" href="<?php echo BASE_URL; ?>/event.php?id=<?php echo (int)$e['id']; ?>" style="padding:8px 16px; font-size:14px;">View Event</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<style>
.featured-events-section {
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
  border-radius: 20px;
  padding: 25px;
  border: 2px solid #f59e0b;
  box-shadow: 0 10px 30px rgba(245, 158, 11, 0.15);
}

.featured-events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 20px;
}

.featured-event-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  border: 1px solid #f59e0b;
  position: relative;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.featured-event-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
}

.featured-badge {
  position: absolute;
  top: -10px;
  right: 15px;
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  color: white;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 4px;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}
</style>
<?php endif; ?>

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
      <?php if (empty($regularEvents)): ?>
        <tr>
          <td colspan="6" class="muted">No events found.</td>
        </tr>
      <?php endif; ?>

      <?php foreach ($regularEvents as $e): ?>
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
