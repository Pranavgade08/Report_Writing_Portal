<?php
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo '<div class="card" style="margin-top:16px">Invalid event.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = db()->prepare(
    'SELECT e.*, d.name AS department_name, y.year_label
     FROM events e
     JOIN departments d ON d.id = e.department_id
     JOIN academic_years y ON y.id = e.academic_year_id
     WHERE e.id = ?'
);
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    http_response_code(404);
    echo '<div class="card" style="margin-top:16px">Event not found.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$photosStmt = db()->prepare('SELECT id, file_path, caption FROM event_photos WHERE event_id = ? ORDER BY id DESC');
$photosStmt->execute([$id]);
$photos = $photosStmt->fetchAll();

// Fetch attendance photos (multiple)
$attendancePhotosStmt = db()->prepare('SELECT id, file_path FROM attendance_photos WHERE event_id = ? ORDER BY id DESC');
$attendancePhotosStmt->execute([$id]);
$attendancePhotos = $attendancePhotosStmt->fetchAll();

$title = $event['title'] . ' - ' . APP_NAME;
?>

<section class="card event-page" style="margin-top:16px">
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between">
    <div>
      <div class="badge"><?php echo h($event['event_type']); ?></div>
      <h2 style="margin:10px 0 6px"><?php echo h($event['title']); ?></h2>
      <div class="muted"><?php echo h($event['department_name']); ?> | Academic Year: <?php echo h($event['year_label']); ?></div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <a class="btn" href="<?php echo BASE_URL; ?>/events.php">Back</a>
      <button class="btn primary" type="button" id="btnPdf">Download PDF</button>
    </div>
  </div>

  <hr style="border:none;border-top:1px solid var(--border);margin:14px 0" />

  <div class="card" id="reportArea">
      <h3 style="margin-top:0">Event Information</h3>
      <table class="table" style="width:100%; margin-top:15px;">
        <tr>
          <td style="font-weight:bold; width:30%;">Date:</td>
          <td><?php echo h($event['event_date']); ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;">Time:</td>
          <td><?php echo h($event['event_time'] ?: 'NA'); ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;">Venue:</td>
          <td><?php echo h($event['venue']); ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;">Organizer / Department:</td>
          <td><?php echo h($event['organizer']); ?> (<?php echo h($event['department_name']); ?>)</td>
        </tr>
        <tr>
          <td style="font-weight:bold;">Guest / Speaker:</td>
          <td><?php echo h($event['guest_speaker'] ?: 'NA'); ?></td>
        </tr>
        <tr>
          <td style="font-weight:bold;">Participants:</td>
          <td><?php echo (int)$event['participants_count']; ?></td>
        </tr>
      </table>
      
      <h3 style="margin:20px 0 10px;">Objectives & Outcomes</h3>
      <table class="table" style="width:100%; margin-top:15px;">
        <tr>
          <td style="font-weight:bold; width:30%; vertical-align:top;">Objectives:</td>
          <td><div class="muted" style="white-space:pre-wrap"><?php echo h($event['objectives'] ?: 'NA'); ?></div></td>
        </tr>
        <tr>
          <td style="font-weight:bold; width:30%; vertical-align:top;">Outcomes:</td>
          <td><div class="muted" style="white-space:pre-wrap"><?php echo h($event['outcomes'] ?: 'NA'); ?></div></td>
        </tr>
      </table>
      
      <div class="card" style="margin-top:20px;">
        <h3 style="margin-top:0">Description</h3>
        <div style="white-space:pre-wrap" class="muted"><?php echo h($event['description'] ?: 'NA'); ?></div>
      </div>
    </div> <!-- Close reportArea div -->
    
    <div class="card" style="grid-column:1/-1; margin-top:20px;">
      <h3 style="margin-top:0">Photo Gallery</h3>
      
      <!-- Hidden section for optimized PDF printing -->
      <div id="pdfContent" style="display:none;">
        <div style="text-align:center; margin-bottom:15px;">
          <img src="<?php echo BASE_URL; ?>/assets/images/deogirisymbol.jpeg" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" style="height:80px; margin-bottom:8px;" alt="College Logo" />
<div style="display:none; font-size:16px; font-weight:bold; margin-bottom:8px;">[College Logo]</div>
          <h1 style="margin:0; font-size:20px;">Marathwada Shikshan Prasarak Mandal's</h1>
          <h1 style="margin:0; font-size:20px;">Deogiri College, Chhatrapati Sambhajinagar</h1>
        </div>
        <h2 style="text-align:center; margin-top:0;">Event Report: <?php echo h($event['title']); ?></h2>
        <p style="font-size:14px; margin:0 0 15px 0;"><strong>Department: <?php echo h($event['department_name']); ?></strong></p>
        <table style="width:100%; border-collapse: collapse; margin: 20px 0;">
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; width: 30%; font-weight: bold;">Event Type:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo h($event['event_type']); ?></td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Date:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo h($event['event_date']); ?></td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Time:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo h($event['event_time'] ?: 'NA'); ?></td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Venue:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo h($event['venue']); ?></td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Department:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo h($event['department_name']); ?></td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Organizer:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo h($event['organizer']); ?></td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Guest/Speaker:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo h($event['guest_speaker'] ?: 'NA'); ?></td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Participants:</td>
            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo (int)$event['participants_count']; ?></td>
          </tr>
        </table>
        
        <h3>Objectives</h3>
        <div style="white-space:pre-wrap; margin-bottom: 15px;"><?php echo h($event['objectives'] ?: 'NA'); ?></div>
        
        <h3>Outcomes</h3>
        <div style="white-space:pre-wrap; margin-bottom: 15px;"><?php echo h($event['outcomes'] ?: 'NA'); ?></div>
        
        <h3>Description</h3>
        <div style="white-space:pre-wrap; margin-bottom: 15px;"><?php echo h($event['description'] ?: 'NA'); ?></div>
        
        <h3>Event Photos</h3>
        <?php if ($photos): ?>
          <div class="pdf-photo-gallery">
            <?php foreach ($photos as $p): ?>
              <div class="pdf-photo-item">
                <img src="<?php echo h($p['file_path']); ?>" />
                <?php if (!empty($p['caption'])): ?>
                  <div class="pdf-photo-caption"><?php echo h($p['caption']); ?></div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p>No photos uploaded for this event.</p>
        <?php endif; ?>
        
        <?php if (!empty($attendancePhotos)): ?>
          <div style="page-break-before: always;">
            <h3>Attendance Photos</h3>
            <div class="pdf-photo-gallery">
              <?php foreach ($attendancePhotos as $ap): ?>
                <div class="pdf-photo-item">
                  <img src="<?php echo h($ap['file_path']); ?>" />
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <!-- HOD and Incharge Signatures -->
        <div style="margin-top:40px; page-break-inside: avoid;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 30px;">
            <div style="text-align:center;">
              <p style="margin:0 0 5px 0; font-weight:bold; font-size:16px;"><?php echo h($event['hod_name'] ?: 'HOD Name'); ?></p>
              <p style="margin:0; font-size:14px; color:#666;">Head of Department</p>
            </div>
            <div style="text-align:center;">
              <p style="margin:0 0 5px 0; font-weight:bold; font-size:16px;"><?php echo h($event['incharge_name'] ?: 'Incharge Name'); ?></p>
              <p style="margin:0; font-size:14px; color:#666;">Event Incharge</p>
            </div>
          </div>
        </div>
      </div>
      <?php if (!$photos): ?>
        <div class="muted">No photos uploaded for this event yet.</div>
      <?php else: ?>
        <div class="photo-gallery-container">
          <?php foreach ($photos as $p): ?>
            <div class="photo-card">
              <img alt="Event photo" src="<?php echo h($p['file_path']); ?>" />
              <?php if (!empty($p['caption'])): ?>
                <div class="photo-caption"><?php echo h($p['caption']); ?></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!empty($attendancePhotos)): ?>
      <div class="card" style="grid-column:1/-1; margin-top:20px;">
        <h3 style="margin-top:0">Attendance Photos</h3>
        <div class="grid" style="margin-top:10px">
          <?php foreach ($attendancePhotos as $ap): ?>
            <div class="card" style="grid-column: span 3; padding:10px">
              <img alt="Attendance" src="<?php echo h($ap['file_path']); ?>" style="width:100%;height:200px;object-fit:cover;border-radius:12px" />
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    
    <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end; margin:20px 0;">
      <button class="btn primary" type="button" id="btnPdf">Download PDF</button>
    </div>
    
    <style>
      /* PDF capture layout (screen) */
      #pdfContent.pdf-capture {
        display: block !important;
        position: fixed;
        left: -10000px;
        top: 0;
        visibility: visible !important;
      }

      #pdfContent {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        font-size: 18px;
        color: #111827;
        background: #ffffff;
        padding: 20px;
        border-radius: 14px;
        width: 820px;
        max-width: 820px;
        margin: 0 auto;
      }

      #pdfContent h2 { font-size: 28px; margin: 0 0 12px 0; }
      #pdfContent h3 { font-size: 20px; margin-top: 18px; margin-bottom: 8px; }

      #pdfContent table { font-size: 18px; }
      #pdfContent table td { font-size: 18px; }

      #pdfContent .photo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
      #pdfContent .photo-item { text-align: center; border: 2px solid #e5e7eb; border-radius: 12px; padding: 12px; }
      #pdfContent .photo-item img { width: 100% !important; height: 280px !important; object-fit: cover !important; }
      
      /* Force attendance photos to start on new page */
      #pdfContent div[style*="page-break-before: always"] {
        page-break-before: always !important;
        break-before: page !important;
        page-break-after: always !important;
        break-after: page !important;
      }
      
      #pdfContent div[style*="page-break-before: always"] .photo-grid {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 15px !important;
      }
      
      #pdfContent div[style*="page-break-before: always"] .photo-item {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
      #pdfContent .photo-caption { margin-top: 8px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 10px; background: #f8fafc; font-weight: 700; font-size: 16px; }

      /* PDF Photo Gallery Styles */
      #pdfContent .pdf-photo-gallery {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin: 20px 0;
        page-break-inside: avoid;
      }
      
      #pdfContent .pdf-photo-item {
        text-align: center;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        background: #ffffff;
        page-break-inside: avoid;
      }
      
      #pdfContent .pdf-photo-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
      }
      
      #pdfContent .pdf-photo-caption {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        background: #f8fafc;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        margin-top: 8px;
      }

      .photo-caption { margin-top: 8px; padding: 10px; border: 1px solid var(--border); border-radius: 12px; background: rgba(255,255,255,0.65); font-weight: 800; }

      /* Improved Photo Gallery Styles */
      .photo-gallery-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 15px;
      }
      
      .photo-card {
        background: var(--card);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid var(--border);
      }
      
      .photo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
      }
      
      .photo-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
      }
      
      .photo-card .photo-caption {
        margin: 0;
        border: none;
        border-radius: 0;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 12px 16px;
        font-size: 14px;
        border-top: 1px solid var(--border);
      }

      @media print {
        /* Reset body margins for printing */
        body {
          margin: 0;
          padding: 0;
        }
        
        /* Hide UI elements during printing */
        .nav, .footer, .btn, #btnPrint, #btnPdf, .btnPrint, .btnPdf, [id^='btn'] {
          display: none !important;
        }
        
        /* Ensure content is visible during printing */
        #pdfContent {
          display: block !important;
          margin: 0 !important;
          padding: 20px !important;
          width: 100% !important;
          max-width: 100% !important;
        }
        
        /* But allow specific sections to break before */
        #pdfContent > div[style*="page-break-before: always"] {
          page-break-before: always !important;
          break-before: page !important;
        }
        
        /* Hide the main report area and show only the optimized print content */
        #reportArea {
          display: none !important;
        }
        
        /* Prevent page breaks inside elements */
        #pdfContent h2,
        #pdfContent h3,
        #pdfContent table,
        #pdfContent div[style*="white-space:pre-wrap"] {
          page-break-inside: auto;
          line-height: 1.4;
        }
        
        /* Control page breaks for photo items */
        #pdfContent .photo-item {
          page-break-inside: avoid;
        }
        
        /* Print-specific styles for pdfContent */
        #pdfContent { width: 100% !important; max-width: 100% !important; font-size: 18px; }
        #pdfContent h2 { font-size: 28px; }
        #pdfContent h3 { font-size: 20px; }
        
        #pdfContent table {
          width: 100%;
          border-collapse: collapse;
          margin: 20px 0;
          page-break-inside: avoid;
          font-size: 14px;
        }
        
        #pdfContent table th,
        #pdfContent table td {
          padding: 10px;
          border: 1px solid #ccc;
          vertical-align: top;
        }
        
        #pdfContent table th {
          background-color: #f1f5f9;
          font-weight: bold;
          width: 30%;
        }
        
        #pdfContent div[style*="white-space:pre-wrap"] {
          line-height: 1.5;
          margin: 10px 0;
          padding: 10px;
          background-color: #f8fafc;
          border-left: 3px solid #3b82f6;
          border-radius: 0 4px 4px 0;
          page-break-inside: avoid;
        }
        
        #pdfContent .photo-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
        
        #pdfContent .photo-item {
          text-align: center;
          page-break-inside: avoid;
        }
        
        /* Force attendance photos to start on new page */
        #pdfContent div[style*="page-break-before: always"] {
          page-break-before: always;
          break-before: page;
          page-break-after: always;
          break-after: page;
        }
        
        #pdfContent div[style*="page-break-before: always"] .photo-grid {
          page-break-inside: avoid;
          break-inside: avoid-page;
          grid-template-columns: 1fr 1fr !important;
          gap: 15px !important;
        }
        
        #pdfContent div[style*="page-break-before: always"] .photo-item {
          page-break-inside: avoid;
          break-inside: avoid-page;
        }
        
        #pdfContent .photo-item img { width: 100%; height: auto; max-height: 600px; object-fit: contain; }
      }
      
      /* Hide pdfContent during normal viewing */
      #pdfContent {
        display: none;
      }

      #pdfAttendance {
        display: none;
      }
    </style>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
(function(){
  const btn = document.getElementById('btnPdf');
  const area = document.getElementById('reportArea');
  if(!btn || !area) return;

  btn.addEventListener('click', async ()=>{
    btn.disabled = true;
    btn.textContent = 'Generating...';

    try{
      // Temporarily show the PDF content to render it
      const pdfContent = document.getElementById('pdfContent');
      pdfContent.classList.add('pdf-capture');
      
      // Wait a bit to ensure content is rendered
      await new Promise(resolve => setTimeout(resolve, 500));
      
      const canvas = await html2canvas(pdfContent, {
        scale: 2,
        useCORS: true,
        scrollX: 0,
        scrollY: 0,
        backgroundColor: '#ffffff'
      });
      
      // Hide the PDF content again
      pdfContent.classList.remove('pdf-capture');


      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF('p','mm','a4');

      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();

      const margin = 8;
      const usableWidth = pageWidth - (margin * 2);
      const usableHeight = pageHeight - (margin * 2);

      const imgWidth = usableWidth;

      // Slice the canvas into page-sized pieces (prevents missing / half content)
      const pxPerMm = canvas.width / imgWidth;
      const pageHeightPx = Math.floor((usableHeight / imgWidth) * canvas.width);
      const overlapPx = Math.floor(2 * pxPerMm); // reduced overlap to avoid extra pages

      let yPx = 0;
      let pageIndex = 0;

      while (yPx < canvas.height) {
        const sliceHeightPx = Math.min(pageHeightPx, canvas.height - yPx);
        // Skip tiny remaining slices that would create blank pages
        if (sliceHeightPx < 20) break;

        const pageCanvas = document.createElement('canvas');
        pageCanvas.width = canvas.width;
        pageCanvas.height = sliceHeightPx;

        const ctx = pageCanvas.getContext('2d');
        ctx.drawImage(canvas, 0, yPx, canvas.width, sliceHeightPx, 0, 0, canvas.width, sliceHeightPx);

        const sliceData = pageCanvas.toDataURL('image/jpeg', 0.82);
        const sliceHeightMm = Math.min(sliceHeightPx / pxPerMm, usableHeight);

        if (pageIndex > 0) {
          pdf.addPage();
        }

        pdf.addImage(sliceData, 'JPEG', margin, margin, imgWidth, sliceHeightMm);

        const step = Math.max(1, sliceHeightPx - overlapPx);
        yPx += step;
        pageIndex++;
      }



      const safeTitle = <?php echo json_encode(preg_replace('/[^a-zA-Z0-9_-]+/', '_', (string)$event['title'])); ?>;
      pdf.save(safeTitle + '_Event_Report.pdf');
    }catch(e){
      alert('PDF generation failed. Try again.');
      console.error(e);
    }finally{
      const pdfContent = document.getElementById('pdfContent');
      if (pdfContent) {
        pdfContent.classList.remove('pdf-capture');
      }
      btn.disabled = false;
      btn.textContent = 'Download PDF';
    }
  });


  const url = new URL(window.location.href);
  if (url.searchParams.get('pdf') === '1') {
    setTimeout(()=>btn.click(), 300);
  }
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
