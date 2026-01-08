<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_dept'])) {
        $name = trim((string)($_POST['name'] ?? ''));
        $short_name = trim((string)($_POST['short_name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));

        if ($name === '') {
            $error = 'Department name is required.';
        } else {
            try {
                $stmt = db()->prepare('INSERT INTO departments (name, short_name, description) VALUES (?, ?, ?)');
                $stmt->execute([$name, $short_name, $description]);
                $newId = (int)db()->lastInsertId();
                $success = 'Department added successfully.';
                
                // Log the activity
                log_activity($_SESSION['admin_id'], 'INSERT', 'departments', $newId, 'Created department: ' . $name);
            } catch (PDOException $e) {
                $error = 'Error adding department: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['update_dept'])) {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $short_name = trim((string)($_POST['short_name'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));

        if ($id <= 0 || $name === '') {
            $error = 'Valid ID and department name are required.';
        } else {
            // Get current department details for logging
            $currentStmt = db()->prepare('SELECT name FROM departments WHERE id=?');
            $currentStmt->execute([$id]);
            $current = $currentStmt->fetch();
            
            $stmt = db()->prepare('UPDATE departments SET name=?, short_name=?, description=? WHERE id=?');
            $stmt->execute([$name, $short_name, $description, $id]);
            $success = 'Department updated successfully.';
            
            // Log the activity
            if ($current) {
                log_activity($_SESSION['admin_id'], 'UPDATE', 'departments', $id, 'Updated department: ' . $current['name'] . ' to ' . $name);
            }
        }
    } elseif (isset($_GET['delete_id'])) {
        $id = (int)($_GET['delete_id'] ?? 0);
        
        if ($id > 0) {
            try {
                // Get department details for logging
                $currentStmt = db()->prepare('SELECT name FROM departments WHERE id=?');
                $currentStmt->execute([$id]);
                $current = $currentStmt->fetch();
                
                $stmt = db()->prepare('DELETE FROM departments WHERE id=?');
                $stmt->execute([$id]);
                $success = 'Department deleted successfully.';
                
                // Log the activity
                if ($current) {
                    log_activity($_SESSION['admin_id'], 'DELETE', 'departments', $id, 'Deleted department: ' . $current['name']);
                }
            } catch (PDOException $e) {
                $error = 'Error deleting department: Cannot delete department with associated events.';
            }
        }
    }
}

$departments = db()->query('SELECT * FROM departments ORDER BY name ASC')->fetchAll();

$title = 'Manage Departments - ' . APP_NAME;
?>

<h2 class="section-title">Manage Departments</h2>

<?php if ($error): ?>
    <div class="alert error" style="margin-bottom:12px"><?php echo h($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert success" style="margin-bottom:12px"><?php echo h($success); ?></div>
<?php endif; ?>

<section class="card">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between">
        <div class="muted">Manage departments for events.</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a class="btn" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Back</a>
        </div>
    </div>
</section>

<section class="card" style="margin-top:14px">
    <h3 style="margin-top:0">Add New Department</h3>
    <form method="post">
        <div class="grid">
            <div class="field" style="grid-column: span 6">
                <label>Department Name</label>
                <input class="input" name="name" placeholder="Eg: Computer Science" required />
            </div>
            <div class="field" style="grid-column: span 6">
                <label>Short Name</label>
                <input class="input" name="short_name" placeholder="Eg: CS" />
            </div>
        </div>
        <div class="field" style="grid-column:1/-1">
            <label>Description</label>
            <textarea name="description" class="input" placeholder="Department description..." style="min-height: 120px;"></textarea>
        </div>
        <div style="margin-top:12px">
            <button class="btn primary" type="submit" name="add_dept">Add Department</button>
        </div>
    </form>
</section>

<section class="card" style="margin-top:14px">
    <h3 style="margin-top:0">Departments</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Short Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$departments): ?>
                <tr><td colspan="3" class="muted">No departments found.</td></tr>
            <?php endif; ?>
            
            <?php foreach ($departments as $dept): ?>
                <tr>
                    <td><?php echo h($dept['name']); ?></td>
                    <td><?php echo h($dept['short_name'] ?? ''); ?></td>
                    <td style="display:flex;gap:10px;flex-wrap:wrap">
                        <a class="btn" href="#edit_<?php echo (int)$dept['id']; ?>" onclick="document.getElementById('edit_form_<?php echo (int)$dept['id']; ?>').style.display='block';return false;">Edit</a>
                        <a class="btn danger" data-confirm="Delete this department?" href="?delete_id=<?php echo (int)$dept['id']; ?>">Delete</a>
                    </td>
                </tr>
                
                <!-- Edit Form (Initially Hidden) -->
                <tr id="edit_form_<?php echo (int)$dept['id']; ?>" style="display:none;">
                    <td colspan="3">
                        <form method="post" style="padding:10px;background:var(--bg);border-radius:10px;">
                            <input type="hidden" name="id" value="<?php echo (int)$dept['id']; ?>" />
                            <div class="grid">
                                <div class="field" style="grid-column: span 4">
                                    <label>Department Name</label>
                                    <input class="input" name="name" value="<?php echo h($dept['name']); ?>" required />
                                </div>
                                <div class="field" style="grid-column: span 4">
                                    <label>Short Name</label>
                                    <input class="input" name="short_name" value="<?php echo h($dept['short_name'] ?? ''); ?>" />
                                </div>
                                <div class="field" style="grid-column: span 4; display:flex;align-items:flex-end;">
                                    <button class="btn primary" type="submit" name="update_dept">Update</button>
                                    <button class="btn" type="button" onclick="document.getElementById('edit_form_<?php echo (int)$dept['id']; ?>').style.display='none';">Cancel</button>
                                </div>
                            </div>
                            <div class="field" style="margin-top:10px">
                                <label>Description</label>
                                <textarea name="description" class="input" placeholder="Department description..." style="min-height: 120px;"><?php echo h($dept['description'] ?? ''); ?></textarea>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>