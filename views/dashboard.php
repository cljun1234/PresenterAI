<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trustabee - Dashboard</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 0; }
        header { background: white; padding: 1rem 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: bold; font-size: 1.2rem; color: #1a73e8; }
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        h2 { margin-top: 0; font-size: 1.2rem; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .stat-box { background: #e8f0fe; color: #1a73e8; padding: 1rem; border-radius: 8px; text-align: center; font-size: 1.5rem; font-weight: bold; display: inline-block; min-width: 100px; }
        .stat-label { font-size: 0.8rem; font-weight: normal; display: block; color: #555; }
        textarea.code-block { width: 100%; height: 60px; font-family: monospace; padding: 10px; background: #2d2d2d; color: #f8f8f2; border-radius: 4px; border: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; }
        th { color: #666; font-size: 0.9rem; }
        .btn { padding: 8px 16px; background: #1a73e8; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
        .btn-sm { padding: 4px 8px; font-size: 0.8rem; background: #dc3545; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; }
        .toggle { display: flex; align-items: center; }
        .toggle input { margin-right: 10px; }
    </style>
</head>
<body>
    <header>
        <div class="logo">Trustabee</div>
        <div>
            <a href="/logout" style="color: #666; text-decoration: none;">Logout</a>
        </div>
    </header>

    <div class="container">

        <!-- Live Stats -->
        <div class="card">
            <h2>Live Activity</h2>
            <div class="stat-box">
                <?php echo $live_count; ?>
                <span class="stat-label">Live Visitors</span>
            </div>
            <p style="color: #666; font-size: 0.9rem; margin-top: 10px;">
                People currently viewing your site (updated via heartbeat).
            </p>
        </div>

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
                    <label>
                        <input type="checkbox" name="magical_detection" value="1" <?php if($widget['magical_detection']) echo 'checked'; ?>>
                        Enable "Magical Detection" (Auto-capture form submissions)
                    </label>
                </div>
                <button type="submit" class="btn">Save Settings</button>
            </form>
        </div>

        <!-- Simulated Data -->
        <div class="card">
            <h2>Simulated Notifications</h2>
            <p>Add fake sales/activity to boost social proof.</p>

            <form action="/notification/add" method="POST" style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="name" placeholder="Name (e.g. John D.)" required style="flex: 1;">
                    <input type="text" name="action_text" placeholder="Action (e.g. Purchased a Pro Plan)" required style="flex: 2;">
                    <button type="submit" class="btn">Add</button>
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Action</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($notifications as $notif): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($notif['name']); ?></td>
                        <td><?php echo htmlspecialchars($notif['action_text']); ?></td>
                        <td style="text-align: right;">
                            <a href="/notification/delete/<?php echo $notif['id']; ?>" class="btn btn-sm">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($notifications)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No notifications configured.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
