<?php
$activePage = 'home';
$pageTitle = 'Home';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <h2>Live Activity</h2>
    <div style="text-align: center; padding: 2rem;">
        <div class="stat-box" style="font-size: 3rem; min-width: 150px;">
            <?php echo $live_count; ?>
            <span class="stat-label" style="font-size: 1rem; margin-top: 5px;">Live Visitors</span>
        </div>
        <p style="color: #666; font-size: 1rem; margin-top: 20px;">
            People currently viewing your site (updated via heartbeat).
        </p>
    </div>
</div>

<div class="card">
    <h2>Welcome back!</h2>
    <p>Use the <strong>Campaigns</strong> menu on the left to manage your widgets and notifications.</p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
