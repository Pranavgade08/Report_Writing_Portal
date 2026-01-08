<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$eventId = (int)($_GET['event_id'] ?? 0);
if ($eventId <= 0) {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

$evtStmt = db()->prepare('SELECT id, title FROM events WHERE id = ?');
$evtStmt->execute([$eventId]);
$event = $evtStmt->fetch();

if (!$event) {
    echo '<div class="card" style="margin-top:16px">Event not found.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$photosStmt = db()->prepare('SELECT id, file_path, file_name, caption, uploaded_at FROM event_photos WHERE event_id = ? ORDER BY id DESC');
$photosStmt->execute([$eventId]);
$photos = $photosStmt->fetchAll();

$title = 'Upload Photos - ' . APP_NAME;
?>

<h2 class="section-title">Upload Photos</h2>

<section class="card">
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between">
    <div>
      <div class="muted">Event:</div>
      <div style="font-weight:900;font-size:18px;margin-top:4px"><?php echo h($event['title']); ?></div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
      <a class="btn" href="<?php echo BASE_URL; ?>/admin/events.php">Back</a>
      <a class="btn" href="<?php echo BASE_URL; ?>/event.php?id=<?php echo (int)$eventId; ?>">View Public Page</a>
    </div>
  </div>
</section>

<section class="card" style="margin-top:14px">
  <form action="<?php echo BASE_URL; ?>/admin/photos_upload.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="event_id" value="<?php echo (int)$eventId; ?>" />

    <div class="field">
      <label>Select multiple photos</label>
      <input class="input" type="file" name="photos[]" id="photosInput" multiple accept="image/*" required />
      <div class="muted" style="margin-top:6px">Tip: You can select many photos at once.</div>
    </div>

    <div id="preview" class="grid" style="margin-top:12px"></div>

    <div style="margin-top:12px;display:flex;gap:10px;flex-wrap:wrap">
      <button class="btn primary" type="submit">Upload</button>
      <a class="btn" href="<?php echo BASE_URL; ?>/admin/events.php">Cancel</a>
    </div>
  </form>
</section>

<section class="card" style="margin-top:14px">
  <h3 style="margin-top:0">Uploaded Photos</h3>
  <?php if (!$photos): ?>
    <div class="muted">No photos uploaded yet.</div>
  <?php else: ?>
    <div class="grid" style="margin-top:10px">
      <?php foreach ($photos as $p): ?>
        <div class="card" style="grid-column: span 3; padding:10px">
          <img alt="Event photo" src="<?php echo h($p['file_path']); ?>" style="width:100%;height:170px;object-fit:cover;border-radius:12px" />
          <?php if (!empty($p['caption'])): ?>
            <div style="margin-top:8px;font-weight:800"><?php echo h($p['caption']); ?></div>
          <?php endif; ?>
          <div class="muted" style="margin-top:8px;font-size:12px">Uploaded: <?php echo h($p['uploaded_at']); ?></div>
          <div style="margin-top:10px">
            <a class="btn danger" data-confirm="Delete this photo?" href="<?php echo BASE_URL; ?>/admin/photo_delete.php?id=<?php echo (int)$p['id']; ?>&event_id=<?php echo (int)$eventId; ?>">Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<script>
(function(){
  const input = document.getElementById('photosInput');
  const preview = document.getElementById('preview');
  if(!input || !preview) return;

  input.addEventListener('change', ()=>{
    preview.innerHTML = '';
    const files = Array.from(input.files || []);
    if(files.length === 0) return;

    files.forEach((file, idx)=>{
      const url = URL.createObjectURL(file);
      const wrap = document.createElement('div');
      wrap.className = 'card';
      wrap.style.gridColumn = 'span 3';
      wrap.style.padding = '10px';
      wrap.innerHTML = `
        <img src="${url}" alt="preview" style="width:100%;height:170px;object-fit:cover;border-radius:12px" />
        <div class="muted" style="margin-top:8px;font-size:12px">${file.name}</div>
        <div class="field" style="margin-top:10px">
          <label>Photo caption (optional)</label>
          <input class="input" name="captions[]" placeholder="Eg: Winners with chief guest" />
        </div>
      `;
      preview.appendChild(wrap);
    });
  });
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
