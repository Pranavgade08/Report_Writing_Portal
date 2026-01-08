<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_year'])) {
        $year_label = trim((string)($_POST['year_label'] ?? ''));
        $is_active = (int)($_POST['is_active'] ?? 0);

        if ($year_label === '') {
            $error = 'Year label is required.';
        } else {
            try {
                $stmt = db()->prepare('INSERT INTO academic_years (year_label, is_active) VALUES (?, ?)');
                $stmt->execute([$year_label, $is_active]);
                $newId = (int)db()->lastInsertId();
                $success = 'Academic year added successfully.';
                
                // Log the activity
                log_activity($_SESSION['admin_id'], 'INSERT', 'academic_years', $newId, 'Created academic year: ' . $year_label);
            } catch (PDOException $e) {
                $error = 'Error adding academic year: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['update_year'])) {
        $id = (int)($_POST['id'] ?? 0);
        $year_label = trim((string)($_POST['year_label'] ?? ''));
        $is_active = (int)($_POST['is_active'] ?? 0);

        if ($id <= 0 || $year_label === '') {
            $error = 'Valid ID and year label are required.';
        } else {
            // Get current year details for logging
            $currentStmt = db()->prepare('SELECT year_label FROM academic_years WHERE id=?');
            $currentStmt->execute([$id]);
            $current = $currentStmt->fetch();
            
            $stmt = db()->prepare('UPDATE academic_years SET year_label=?, is_active=? WHERE id=?');
            $stmt->execute([$year_label, $is_active, $id]);
            $success = 'Academic year updated successfully.';
            
            // Log the activity
            if ($current) {
                log_activity($_SESSION['admin_id'], 'UPDATE', 'academic_years', $id, 'Updated academic year: ' . $current['year_label'] . ' to ' . $year_label);
            }
        }
    } elseif (isset($_GET['delete_id'])) {
        $id = (int)($_GET['delete_id'] ?? 0);
        
        if ($id > 0) {
            try {
                // Get year details for logging
                $currentStmt = db()->prepare('SELECT year_label FROM academic_years WHERE id=?');
                $currentStmt->execute([$id]);
                $current = $currentStmt->fetch();
                
                $stmt = db()->prepare('DELETE FROM academic_years WHERE id=?');
                $stmt->execute([$id]);
                $success = 'Academic year deleted successfully.';
                
                // Log the activity
                if ($current) {
                    log_activity($_SESSION['admin_id'], 'DELETE', 'academic_years', $id, 'Deleted academic year: ' . $current['year_label']);
                }
            } catch (PDOException $e) {
                $error = 'Error deleting academic year: Cannot delete year with associated events.';
            }
        }
    }
}

$years = db()->query('SELECT * FROM academic_years ORDER BY year_label DESC')->fetchAll();

$title = 'Manage Academic Years - ' . APP_NAME;
?>

<h2 class="section-title">Manage Academic Years</h2>

<?php if ($error): ?>
    <div class="alert error" style="margin-bottom:12px"><?php echo h($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert success" style="margin-bottom:12px"><?php echo h($success); ?></div>
<?php endif; ?>

<section class="card">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between">
        <div class="muted">Manage academic years for events.</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a class="btn" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Back</a>
        </div>
    </div>
</section>

<section class="card" style="margin-top:14px">
    <h3 style="margin-top:0">Add New Academic Year</h3>
    <form method="post">
        <div class="grid">
            <div class="field" style="grid-column: span 6">
                <label>Year Label</label>
                <input class="input" name="year_label" placeholder="Eg: 2024-25" required />
            </div>
            <div class="field" style="grid-column: span 6">
                <label>Active Status</label>
                <select name="is_active" class="input">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div style="margin-top:12px">
            <button class="btn primary" type="submit" name="add_year">Add Year</button>
        </div>
    </form>
</section>

<section class="card" style="margin-top:14px">
    <h3 style="margin-top:0">Academic Years</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Year Label</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$years): ?>
                <tr><td colspan="3" class="muted">No academic years found.</td></tr>
            <?php endif; ?>
            
            <?php foreach ($years as $year): ?>
                <tr>
                    <td><?php echo h($year['year_label']); ?></td>
                    <td>
                        <?php if ($year['is_active']): ?>
                            <span class="badge" style="background:#dcfce7;color:#166534;">Active</span>
                        <?php else: ?>
                            <span class="badge" style="background:#fee2e2;color:#b91c1c;">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td style="display:flex;gap:10px;flex-wrap:wrap">
                        <a class="btn" href="#edit_<?php echo (int)$year['id']; ?>" onclick="document.getElementById('edit_form_<?php echo (int)$year['id']; ?>').style.display='block';return false;">Edit</a>
                        <a class="btn danger" data-confirm="Delete this academic year?" href="?delete_id=<?php echo (int)$year['id']; ?>">Delete</a>
                    </td>
                </tr>
                
                <!-- Edit Form (Initially Hidden) -->
                <tr id="edit_form_<?php echo (int)$year['id']; ?>" style="display:none;">
                    <td colspan="3">
                        <form method="post" style="padding:10px;background:var(--bg);border-radius:10px;">
                            <input type="hidden" name="id" value="<?php echo (int)$year['id']; ?>" />
                            <div class="grid">
                                <div class="field" style="grid-column: span 4">
                                    <label>Year Label</label>
                                    <input class="input" name="year_label" value="<?php echo h($year['year_label']); ?>" required />
                                </div>
                                <div class="field" style="grid-column: span 4">
                                    <label>Active Status</label>
                                    <select name="is_active" class="input">
                                        <option value="1" <?php echo $year['is_active'] ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo !$year['is_active'] ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="field" style="grid-column: span 4; display:flex;align-items:flex-end;">
                                    <button class="btn primary" type="submit" name="update_year">Update</button>
                                    <button class="btn" type="button" onclick="document.getElementById('edit_form_<?php echo (int)$year['id']; ?>').style.display='none';">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>