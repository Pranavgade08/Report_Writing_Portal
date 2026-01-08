  </main>
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <div class="footer-title">Deogiri College</div>
          <div class="footer-text">Event Report Portal for department-wise event documentation and reports.</div>
          <div class="footer-text">&copy; <?php echo date('Y'); ?> <?php echo h(APP_NAME); ?>. All rights reserved.</div>
        </div>

        <div>
          <div class="footer-title">Portal Highlights</div>
          <ul class="footer-list">
            <li>Department-wise event reports</li>
            <li>Photo gallery for each event</li>
            <li>Search and filters (type/year/department)</li>
            <li>PDF report download</li>
          </ul>
        </div>

        <div>
          <div class="footer-title">Quick Links</div>
          <div class="footer-links">
            <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
            <a href="<?php echo BASE_URL; ?>/events.php">Events</a>
            <a href="<?php echo BASE_URL; ?>/about.php">About</a>
            <a href="<?php echo BASE_URL; ?>/contact.php">Contact</a>
            <?php if (is_admin_logged_in()): ?>
              <a href="<?php echo BASE_URL; ?>/admin/dashboard.php">Admin</a>
            <?php else: ?>
              <a href="<?php echo BASE_URL; ?>/admin/login.php">Admin</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <script src="<?php echo BASE_URL; ?>/assets/app.js"></script>
</body>
</html>
