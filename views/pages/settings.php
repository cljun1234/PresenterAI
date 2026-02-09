<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background: #f4f6f8; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h1 { margin-top: 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; margin-bottom: 15px; }
        button { background: #1a73e8; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #1557b0; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container">
        <h1>Settings</h1>

        <div class="card">
            <form action="/domain/save" method="POST">
                <label for="domain">Allowed Domain</label>
                <input type="text" id="domain" name="domain" value="<?php echo htmlspecialchars($domain['domain'] ?? ''); ?>" placeholder="example.com">

                <button type="submit">Save Settings</button>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
