<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>CSS Test</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/style.css" />
</head>
<body style="background: var(--bg, #0f172a); color: var(--text, #f1f5f9); padding: 20px;">
    <h1>CSS Loading Test</h1>
    
    <div style="background: var(--primary, #3b82f6); color: white; padding: 20px; margin: 20px 0; border-radius: 10px; font-weight: bold;">
        This box should be blue with white text if CSS is loading correctly
    </div>
    
    <div style="background: var(--danger, #ef4444); color: white; padding: 20px; margin: 20px 0; border-radius: 10px;">
        This box should be red if there's a CSS issue
    </div>
    
    <div style="background: var(--card, #1e293b); color: var(--text, #f1f5f9); padding: 15px; margin: 15px 0; border: 1px solid var(--border, #334155); border-radius: 8px;">
        Testing card variables: This should have dark background with light text
    </div>
    
    <button style="background: var(--primary, #3b82f6); color: white; padding: 12px 24px; border: none; border-radius: 8px; margin: 10px 0; cursor: pointer;">
        Test Button
    </button>
    
    <p style="margin-top: 20px;">Check your browser's developer tools (F12) to see if there are any CSS loading errors in the Network tab.</p>
</body>
</html>