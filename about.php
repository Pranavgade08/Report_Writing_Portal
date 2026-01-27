<?php
require_once __DIR__ . '/includes/header.php';
$title = 'About - ' . APP_NAME;
?>

<!-- Enhanced About Section -->
<div class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 60px 20px; margin-bottom: 40px; border-radius: 12px; color: white; text-align: center;">
  <h1 style="color: white; margin: 0 0 15px 0; font-size: 2.5rem;">About Our Event Portal</h1>
  <p style="font-size: 1.2rem; margin: 0; max-width: 800px; margin: 0 auto; opacity: 0.9;">
    Deogiri College Event Portal is designed to showcase all college events, activities, and reports in one centralized location. This portal helps students, faculty, and staff stay updated with the latest events happening in different departments of our college.
  </p>
</div>

<section class="card about-section" style="margin-top: -20px;">
  <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 30px;">
    <div class="feature-card">
      <h3 style="color: var(--primary); margin-top: 0; font-size: 1.5rem; border-bottom: 2px solid var(--primary); padding-bottom: 10px; display: inline-block;">
        Comprehensive Event Management
      </h3>
      <p style="margin: 20px 0; line-height: 1.6;">
        Our portal provides comprehensive event management features including event registration, photo galleries, detailed reports, and downloadable resources. Faculty members can create and manage events, while students can browse, search, and access event details and reports.
      </p>
      <div class="feature-list">
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Centralized event repository</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Real-time updates</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Easy navigation and search</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Resource downloads</span>
        </div>
      </div>
    </div>
    
    <div class="feature-card">
      <h3 style="color: var(--primary); margin-top: 0; font-size: 1.5rem; border-bottom: 2px solid var(--primary); padding-bottom: 10px; display: inline-block;">
        Student Experience
      </h3>
      <p style="margin: 20px 0; line-height: 1.6;">
        Designed with students in mind, our portal offers intuitive browsing and easy access to all college events and activities.
      </p>
      <div class="feature-list">
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Browse upcoming events</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Access event reports</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>View photo galleries</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Download resources</span>
        </div>
      </div>
      
      <h3 style="color: var(--primary); margin-top: 30px; font-size: 1.5rem; border-bottom: 2px solid var(--primary); padding-bottom: 10px; display: inline-block;">
        Faculty Tools
      </h3>
      <p style="margin: 20px 0; line-height: 1.6;">
        Powerful administrative tools for faculty to manage all event-related activities efficiently.
      </p>
      <div class="feature-list">
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Create and manage events</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Upload event photos</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Generate event reports</span>
        </div>
        <div style="display: flex; align-items: flex-start; margin: 15px 0;">
          <span style="color: var(--primary); font-weight: bold; margin-right: 10px;">✓</span>
          <span>Track event participation</span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
