<?php
$activePage = 'settings';
$pageTitle = 'Settings';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Installation -->
<div class="card">
    <h2>Installation</h2>
    <p>Copy and paste this code into the <code>&lt;head&gt;</code> of your website.</p>
    <?php
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $scriptUrl = $protocol . $host . '/api/widget.js?w=' . $widget['id'];
    ?>
    <textarea class="code-block" readonly><script src="<?php echo $scriptUrl; ?>"></script></textarea>
</div>

<!-- Configuration -->
<div class="card">
    <h2>Configuration</h2>
    <form action="/widget/save" method="POST">
        <div class="form-group toggle">
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="magical_detection" value="1" <?php if($widget['magical_detection']) echo 'checked'; ?> style="width: auto; margin-right: 10px;">
                Enable "Magical Detection" (Auto-capture form submissions)
            </label>
        </div>
        <button type="submit" class="btn">Save Settings</button>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
