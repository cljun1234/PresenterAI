<?php
$activePage = 'campaigns';
$activeSubPage = 'live-conversion';
$pageTitle = 'Live Conversion';
require_once __DIR__ . '/../layouts/header.php';
?>

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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
