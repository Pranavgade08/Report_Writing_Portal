  </main>
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>Deogiri College</h3>
          <p>Empowering education through technology. Our Event Portal connects students, faculty, and staff in a unified platform.</p>
          <div class="contact-info">
            <p>📍 Chhatrapati Sambhajinagar, Maharashtra</p>
            <p>📧 info@deogiricollege.edu</p>
            <p>📞 +91 12345 67890</p>
          </div>
        </div>

        <div class="footer-section">
          <h3>Portal Features</h3>
          <ul>
            <li>📋 Department reports</li>
            <li>🖼️ Photo galleries</li>
            <li>🔍 Advanced search</li>
            <li>📄 PDF reports</li>
            <li>📊 Analytics</li>
            <li>📅 Real-time updates</li>
          </ul>
        </div>

        <div class="footer-section">
          <h3>Quick Links</h3>
          <div class="links">
            <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
            <a href="<?php echo BASE_URL; ?>/events.php">Events</a>
            <a href="<?php echo BASE_URL; ?>/about.php">About</a>
            <a href="<?php echo BASE_URL; ?>/contact.php">Contact</a>
            <?php if (is_admin_logged_in()): ?>
              <a href="<?php echo BASE_URL; ?>/admin/dashboard.php">Admin Dashboard</a>
            <?php else: ?>
              <a href="<?php echo BASE_URL; ?>/admin/login.php">Admin Login</a>
            <?php endif; ?>
            <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">Back to Top</a>
          </div>
        </div>

        <div class="footer-section">
          <h3>Stay Connected</h3>
          <div class="newsletter">
            <form>
              <input type="email" placeholder="Your email" />
              <button type="submit">→</button>
            </form>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php echo h(APP_NAME); ?> | 
          <a href="#">Privacy</a> • <a href="#">Terms</a> • <a href="#">Accessibility</a>
        </p>
        <p>Designed with ❤️ for Deogiri College Community by Pranav Gade, Ravi Dhawle, Ashish Ghodke</p>
      </div>
    </div>
  </footer>
  
  <style>
    .footer {
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
      color: white;
      padding: 40px 0 25px;
      margin-top: 50px;
    }
    
    .footer-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 30px;
      margin-bottom: 30px;
    }
    
    .footer-section h3 {
      font-size: 1.2rem;
      font-weight: 600;
      margin: 0 0 15px 0;
      padding-bottom: 8px;
      border-bottom: 2px solid #60a5fa;
    }
    
    .footer-section p {
      color: #bfdbfe;
      font-size: 0.9rem;
      line-height: 1.5;
      margin: 0 0 15px 0;
    }
    
    .contact-info p {
      margin: 6px 0;
      font-size: 0.85rem;
    }
    
    .footer-section ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .footer-section li {
      color: #bfdbfe;
      margin: 8px 0;
      font-size: 0.85rem;
      padding-left: 15px;
      position: relative;
    }
    
    .footer-section li:before {
      content: "•";
      color: #60a5fa;
      position: absolute;
      left: 0;
      font-weight: bold;
    }
    
    .links {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    
    .links a {
      color: #bfdbfe;
      text-decoration: none;
      font-size: 0.85rem;
      transition: color 0.2s ease;
      padding: 4px 0;
    }
    
    .links a:hover {
      color: #ffffff;
    }
    
    .newsletter form {
      display: flex;
      gap: 8px;
    }
    
    .newsletter input {
      flex: 1;
      padding: 8px 12px;
      border: none;
      border-radius: 4px;
      font-size: 0.8rem;
      outline: none;
    }
    
    .newsletter button {
      background: #ffffff;
      color: #1e3a8a;
      border: none;
      padding: 8px 14px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.2s ease;
    }
    
    .newsletter button:hover {
      background: #e0f2fe;
    }
    
    .footer-bottom {
      border-top: 1px solid #3b82f6;
      padding-top: 20px;
      text-align: center;
    }
    
    .footer-bottom p {
      color: #93c5fd;
      margin: 8px 0;
      font-size: 0.8rem;
    }
    
    .footer-bottom a {
      color: #60a5fa;
      text-decoration: none;
      margin: 0 6px;
      transition: color 0.2s ease;
    }
    
    .footer-bottom a:hover {
      color: #ffffff;
      text-decoration: underline;
    }
    
    @media (max-width: 768px) {
      .footer-content {
        grid-template-columns: 1fr;
        gap: 25px;
      }
      
      .footer-bottom p {
        font-size: 0.75rem;
      }
    }
  </style>
  
  <script src="<?php echo BASE_URL; ?>/assets/app.js"></script>
</body>
</html>