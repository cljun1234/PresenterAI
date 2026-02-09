<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background: #f4f6f8; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h1 { margin-top: 0; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container">
        <h1>Dashboard</h1>

        <div class="card">
            <h2>Welcome</h2>
            <p>You are logged in.</p>
            <?php if ($domain): ?>
                <p>Your configured domain: <strong><?php echo htmlspecialchars($domain['domain']); ?></strong></p>
            <?php else: ?>
                <p>No domain configured yet. Go to <a href="/settings">Settings</a>.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
