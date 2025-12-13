<?php
$activePage = 'campaigns';
$activeSubPage = $campaignType;
$pageTitle = ucwords(str_replace('-', ' ', $campaignType));
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card" style="text-align: center; padding: 4rem 2rem;">
    <div style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;">
        <i class="fa-solid fa-person-digging"></i>
    </div>
    <h2>Coming Soon</h2>
    <p style="color: #666; max-width: 500px; margin: 0 auto;">
        The <strong><?php echo htmlspecialchars($pageTitle); ?></strong> feature is currently under development.
        Check back soon for updates!
    </p>
    <br>
    <a href="/" class="btn" style="background: #eee; color: #333;">Back to Dashboard</a>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
