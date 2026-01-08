<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;

$departments = db()->query('SELECT id, name FROM departments ORDER BY name')->fetchAll();
$years = db()->query('SELECT id, year_label FROM academic_years WHERE is_active = 1 ORDER BY year_label DESC')->fetchAll();

// Check if the 'featured' column exists
$hasFeaturedColumn = db()->query("SHOW COLUMNS FROM events LIKE 'featured'")->fetch();

$event = [
    'title' => '',
    'event_type' => 'Other',
    'event_date' => date('Y-m-d'),
    'event_time' => '',
    'venue' => '',
    'department_id' => (int)($departments[0]['id'] ?? 0),
    'academic_year_id' => (int)($years[0]['id'] ?? 0),
    'organizer' => '',
    'guest_speaker' => '',
    'participants_count' => 0,
    'objectives' => '',
    'outcomes' => '',
    'description' => '',
    'featured' => 0
];

if ($isEdit) {
    if ($hasFeaturedColumn) {
        $stmt = db()->prepare('SELECT * FROM events WHERE id = ?');
        $stmt->execute([$id]);
    } else {
        $stmt = db()->prepare('SELECT *, 0 as featured FROM events WHERE id = ?');
        $stmt->execute([$id]);
    }
    $dbEvent = $stmt->fetch();
    if (!$dbEvent) {
        echo '<div class="card" style="margin-top:16px">Event not found.</div>';
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }
    $event = array_merge($event, $dbEvent);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event['title'] = trim((string)($_POST['title'] ?? ''));
    $event['event_type'] = trim((string)($_POST['event_type'] ?? 'Other'));
    $event['event_date'] = trim((string)($_POST['event_date'] ?? ''));
    $event['event_time'] = trim((string)($_POST['event_time'] ?? ''));
    $event['venue'] = trim((string)($_POST['venue'] ?? ''));
    $event['department_id'] = (int)($_POST['department_id'] ?? 0);
    $event['academic_year_id'] = (int)($_POST['academic_year_id'] ?? 0);
    $event['organizer'] = trim((string)($_POST['organizer'] ?? ''));
    $event['guest_speaker'] = trim((string)($_POST['guest_speaker'] ?? ''));
    $event['participants_count'] = (int)($_POST['participants_count'] ?? 0);
    $event['objectives'] = trim((string)($_POST['objectives'] ?? ''));
    $event['outcomes'] = trim((string)($_POST['outcomes'] ?? ''));
    $event['description'] = trim((string)($_POST['description'] ?? ''));
    if ($hasFeaturedColumn) {
        $event['featured'] = (int)($_POST['featured'] ?? 0);
    } else {
        $event['featured'] = 0;
    }

    if ($event['title'] === '' || $event['venue'] === '' || $event['organizer'] === '') {
        $error = 'Title, Venue and Organizer are required.';
    } elseif ($event['department_id'] <= 0 || $event['academic_year_id'] <= 0) {
        $error = 'Please select department and academic year.';
    } elseif ($event['event_date'] === '') {
        $error = 'Please select event date.';
    } else {
        if ($isEdit) {
            if ($hasFeaturedColumn) {
                $stmt = db()->prepare(
                    'UPDATE events SET title=?, event_type=?, event_date=?, event_time=?, venue=?, department_id=?, academic_year_id=?, organizer=?, guest_speaker=?, participants_count=?, objectives=?, outcomes=?, description=?, featured=?, updated_at=NOW() WHERE id=?'
                );
                $stmt->execute([
                    $event['title'],
                    $event['event_type'],
                    $event['event_date'],
                    $event['event_time'] !== '' ? $event['event_time'] : null,
                    $event['venue'],
                    $event['department_id'],
                    $event['academic_year_id'],
                    $event['organizer'],
                    $event['guest_speaker'] !== '' ? $event['guest_speaker'] : null,
                    $event['participants_count'],
                    $event['objectives'] !== '' ? $event['objectives'] : null,
                    $event['outcomes'] !== '' ? $event['outcomes'] : null,
                    $event['description'] !== '' ? $event['description'] : null,
                    $event['featured'],
                    $id
                ]);
            } else {
                $stmt = db()->prepare(
                    'UPDATE events SET title=?, event_type=?, event_date=?, event_time=?, venue=?, department_id=?, academic_year_id=?, organizer=?, guest_speaker=?, participants_count=?, objectives=?, outcomes=?, description=?, updated_at=NOW() WHERE id=?'
                );
                $stmt->execute([
                    $event['title'],
                    $event['event_type'],
                    $event['event_date'],
                    $event['event_time'] !== '' ? $event['event_time'] : null,
                    $event['venue'],
                    $event['department_id'],
                    $event['academic_year_id'],
                    $event['organizer'],
                    $event['guest_speaker'] !== '' ? $event['guest_speaker'] : null,
                    $event['participants_count'],
                    $event['objectives'] !== '' ? $event['objectives'] : null,
                    $event['outcomes'] !== '' ? $event['outcomes'] : null,
                    $event['description'] !== '' ? $event['description'] : null,
                    $id
                ]);
            }
            $success = 'Event updated successfully.';
            
            // Log the activity
            log_activity($_SESSION['admin_id'], 'UPDATE', 'events', $id, 'Updated event: ' . $event['title']);
        } else {
            if ($hasFeaturedColumn) {
                $stmt = db()->prepare(
                    'INSERT INTO events (title,event_type,event_date,event_time,venue,department_id,academic_year_id,organizer,guest_speaker,participants_count,objectives,outcomes,description,featured) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([
                    $event['title'],
                    $event['event_type'],
                    $event['event_date'],
                    $event['event_time'] !== '' ? $event['event_time'] : null,
                    $event['venue'],
                    $event['department_id'],
                    $event['academic_year_id'],
                    $event['organizer'],
                    $event['guest_speaker'] !== '' ? $event['guest_speaker'] : null,
                    $event['participants_count'],
                    $event['objectives'] !== '' ? $event['objectives'] : null,
                    $event['outcomes'] !== '' ? $event['outcomes'] : null,
                    $event['description'] !== '' ? $event['description'] : null,
                    $event['featured']
                ]);
            } else {
                $stmt = db()->prepare(
                    'INSERT INTO events (title,event_type,event_date,event_time,venue,department_id,academic_year_id,organizer,guest_speaker,participants_count,objectives,outcomes,description) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([
                    $event['title'],
                    $event['event_type'],
                    $event['event_date'],
                    $event['event_time'] !== '' ? $event['event_time'] : null,
                    $event['venue'],
                    $event['department_id'],
                    $event['academic_year_id'],
                    $event['organizer'],
                    $event['guest_speaker'] !== '' ? $event['guest_speaker'] : null,
                    $event['participants_count'],
                    $event['objectives'] !== '' ? $event['objectives'] : null,
                    $event['outcomes'] !== '' ? $event['outcomes'] : null,
                    $event['description'] !== '' ? $event['description'] : null
                ]);
            }
            $newId = (int)db()->lastInsertId();
            
            // Log the activity
            log_activity($_SESSION['admin_id'], 'INSERT', 'events', $newId, 'Created event: ' . $event['title']);
            
            header('Location: ' . BASE_URL . '/admin/event_form.php?id=' . $newId);
            exit;
        }
    }
}

$title = ($isEdit ? 'Edit Event' : 'Add New Event') . ' - ' . APP_NAME;
?>

<h2 class="section-title"><?php echo $isEdit ? 'Edit Event' : 'Add New Event'; ?></h2>

<section class="card">
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between">
    <div class="muted">Fill the event report details. Students can view these details publicly.</div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <a class="btn" href="<?php echo BASE_URL; ?>/admin/events.php">Back</a>
      <?php if ($isEdit): ?>
        <a class="btn" href="<?php echo BASE_URL; ?>/event.php?id=<?php echo $id; ?>">View Public Page</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="card" style="margin-top:14px">
  <?php if ($error): ?>
    <div class="alert error" style="margin-bottom:12px"><?php echo h($error); ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert success" style="margin-bottom:12px"><?php echo h($success); ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="grid">
      <div class="field" style="grid-column: span 6">
        <label>Event Title</label>
        <input class="input" name="title" value="<?php echo h((string)$event['title']); ?>" placeholder="Eg: BSc IT Career Guidance" required />
      </div>

      <div class="field" style="grid-column: span 3">
        <label>Event Type</label>
        <select name="event_type">
          <?php foreach (['Seminar','Workshop','Sports','Cultural','Other'] as $t): ?>
            <option value="<?php echo h($t); ?>" <?php echo ((string)$event['event_type']) === $t ? 'selected' : ''; ?>><?php echo h($t); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field" style="grid-column: span 3">
        <label>Participants</label>
        <input class="input" type="number" min="0" name="participants_count" value="<?php echo (int)$event['participants_count']; ?>" />
      </div>

      <div class="field" style="grid-column: span 3">
        <label>Date</label>
        <input class="input" type="date" name="event_date" value="<?php echo h((string)$event['event_date']); ?>" required />
      </div>

      <div class="field" style="grid-column: span 3">
        <label>Time</label>
        <input class="input" type="time" name="event_time" value="<?php echo h((string)($event['event_time'] ?? '')); ?>" />
      </div>

      <div class="field" style="grid-column: span 6">
        <label>Venue</label>
        <input class="input" name="venue" value="<?php echo h((string)$event['venue']); ?>" placeholder="Eg: Seminar Hall" required style="min-height: 40px;" />
      </div>

      <div class="field" style="grid-column: span 6">
        <label>Organizer / Department Name</label>
        <input class="input" name="organizer" value="<?php echo h((string)$event['organizer']); ?>" placeholder="Eg: Department of Information Technology" required style="min-height: 40px;" />
      </div>

      <div class="field" style="grid-column: span 6">
        <label>Department (for filtering)</label>
        <select name="department_id" required>
          <?php foreach ($departments as $d): ?>
            <option value="<?php echo (int)$d['id']; ?>" <?php echo (int)$event['department_id'] === (int)$d['id'] ? 'selected' : ''; ?>><?php echo h($d['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field" style="grid-column: span 6">
        <label>Academic Year (for filtering)</label>
        <select name="academic_year_id" required>
          <?php foreach ($years as $y): ?>
            <option value="<?php echo (int)$y['id']; ?>" <?php echo (int)$event['academic_year_id'] === (int)$y['id'] ? 'selected' : ''; ?>><?php echo h($y['year_label']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field" style="grid-column: span 6">
        <label>Guest / Speaker Details</label>
        <input class="input" name="guest_speaker" value="<?php echo h((string)($event['guest_speaker'] ?? '')); ?>" placeholder="Eg: Mr. ABC (Industry Expert)" />
      </div>

      <div class="field" style="grid-column: span 6">
        <label>Objectives</label>
        <textarea name="objectives" placeholder="Write objectives in simple points..." style="min-height: 120px;" ><?php echo h((string)($event['objectives'] ?? '')); ?></textarea>
      </div>

      <div class="field" style="grid-column: span 6">
        <label>Outcomes</label>
        <textarea name="outcomes" placeholder="Write outcomes in simple points..." style="min-height: 120px;" ><?php echo h((string)($event['outcomes'] ?? '')); ?></textarea>
      </div>

      <div class="field" style="grid-column:1/-1">
        <label>Complete Event Description</label>
        <textarea name="description" placeholder="Write full event report description..." style="min-height: 150px;" ><?php echo h((string)($event['description'] ?? '')); ?></textarea>
      </div>
      
<?php if ($hasFeaturedColumn): ?>
      <div class="field" style="grid-column:1/-1">
        <label>
          <input type="checkbox" name="featured" value="1" <?php echo $event['featured'] ? 'checked' : ''; ?>>
          Mark as Featured Event
        </label>
      </div>
<?php endif; ?>
    </div>

    <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap">
      <button class="btn primary" type="submit"><?php echo $isEdit ? 'Update Event' : 'Save Event'; ?></button>
      <a class="btn" href="<?php echo BASE_URL; ?>/admin/events.php">Cancel</a>
    </div>
  </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
