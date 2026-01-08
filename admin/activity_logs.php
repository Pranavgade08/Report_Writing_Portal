<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$logs = get_activity_logs(100);

$title = 'Activity Logs - ' . APP_NAME;
?>

<h2 class="section-title">Activity Logs</h2>

<section class="card">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between">
        <div class="muted">Track all activities performed by admins.</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a class="btn" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Back</a>
        </div>
    </div>
</section>

<section class="card" style="margin-top:14px">
    <table class="table">
        <thead>
            <tr>
                <th>Admin</th>
                <th>Action</th>
                <th>Table</th>
                <th>Record ID</th>
                <th>Description</th>
                <th>IP Address</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$logs): ?>
                <tr><td colspan="7" class="muted">No activity logs found.</td></tr>
            <?php endif; ?>
            
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo h($log['admin_name']); ?></td>
                    <td><span class="badge"><?php echo h($log['action']); ?></span></td>
                    <td><?php echo h($log['table_name']); ?></td>
                    <td><?php echo (int)$log['record_id']; ?></td>
                    <td><?php echo h($log['description']); ?></td>
                    <td><?php echo h($log['ip_address']); ?></td>
                    <td><?php echo h($log['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>