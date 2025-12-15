<?php
$activePage = 'campaigns';
$activeSubPage = 'newsletter';
$pageTitle = 'Newsletter Leads';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Leads: <?php echo htmlspecialchars($newsletter['title']); ?></h2>
        <div style="display: flex; gap: 10px;">
            <a href="/campaigns/newsletter/export/<?php echo $newsletter['id']; ?>" class="btn" style="background: #28a745;">Export CSV</a>
            <a href="/campaigns/newsletter" class="btn" style="background: #6c757d;">Back</a>
        </div>
    </div>

    <?php if (empty($leads)): ?>
        <p style="text-align: center; color: #777; padding: 20px;">No leads captured yet.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Email</th>
                    <?php if ($newsletter['allow_name']): ?>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Name</th>
                    <?php endif; ?>
                    <?php if ($newsletter['allow_phone']): ?>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Phone</th>
                    <?php endif; ?>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $lead): ?>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px;"><?php echo htmlspecialchars($lead['email']); ?></td>
                        <?php if ($newsletter['allow_name']): ?>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($lead['name'] ?? '-'); ?></td>
                        <?php endif; ?>
                        <?php if ($newsletter['allow_phone']): ?>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($lead['phone'] ?? '-'); ?></td>
                        <?php endif; ?>
                        <td style="padding: 12px;"><?php echo $lead['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
