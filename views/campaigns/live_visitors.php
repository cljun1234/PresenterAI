<?php
$pageTitle = 'Live Visitors';
require_once __DIR__ . '/../layouts/header.php';

// $widget, $current_live, $graph_data, $config passed from controller
$settings = $config['settings'] ?? [];
$pos = $settings['position'] ?? 'bottom-left';
$bg = $settings['bg_color'] ?? '#ffffff';
$txt = $settings['text_color'] ?? '#333333';

$labels = array_map(function($d) { return date('H:i', strtotime($d['created_at'])); }, $graph_data);
$counts = array_map(function($d) { return $d['visitor_count']; }, $graph_data);
?>

<div class="row" style="display: flex; gap: 20px;">
    <!-- Left Column: Config -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card">
            <h2>Configuration</h2>
            <form action="/save-live-visitor-config" method="POST">
                <input type="hidden" name="widget_id" value="<?php echo $widget['id']; ?>">

                <div class="form-group toggle">
                    <label>
                        <input type="checkbox" name="enabled" <?php echo $config['enabled'] ? 'checked' : ''; ?>>
                        Enable Live Visitor Widget
                    </label>
                </div>

                <div class="form-group">
                    <label>Position</label>
                    <select name="position" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                        <option value="bottom-left" <?php echo $pos == 'bottom-left' ? 'selected' : ''; ?>>Bottom Left</option>
                        <option value="bottom-right" <?php echo $pos == 'bottom-right' ? 'selected' : ''; ?>>Bottom Right</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Background Color</label>
                    <input type="color" name="bg_color" value="<?php echo htmlspecialchars($bg); ?>" style="width: 100%; height: 40px;">
                </div>

                 <div class="form-group">
                    <label>Text Color</label>
                    <input type="color" name="text_color" value="<?php echo htmlspecialchars($txt); ?>" style="width: 100%; height: 40px;">
                </div>

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>

        <div class="card">
             <h2>Current Status</h2>
             <div style="text-align: center; padding: 20px;">
                <div style="font-size: 3rem; font-weight: 800; color: var(--primary-color);"><?php echo $current_live; ?></div>
                <div class="text-muted">Active Visitors (Last 30m)</div>
             </div>
        </div>
    </div>

    <!-- Right Column: Graph -->
    <div style="flex: 2; min-width: 400px;">
        <div class="card">
            <h2>Visitor Trend (Last 24 Hours)</h2>
            <canvas id="visitorChart" style="width: 100%; height: 300px;"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('visitorChart').getContext('2d');
    const visitorChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Active Visitors',
                data: <?php echo json_encode($counts); ?>,
                borderColor: '#0084ff',
                backgroundColor: 'rgba(0, 132, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
