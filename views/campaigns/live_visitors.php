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

<style>
/* Switch Toggle CSS */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px; bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider { background-color: var(--primary-color); }
input:focus + .slider { box-shadow: 0 0 1px var(--primary-color); }
input:checked + .slider:before { transform: translateX(24px); }
.form-group.toggle { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.form-group.toggle label { margin-bottom: 0; }
</style>

<div class="row" style="display: flex; gap: 20px;">
    <!-- Left Column: Config -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card">
            <h2>Configuration</h2>

            <div class="form-group toggle">
                <label>Enable Live Visitor Widget</label>
                <label class="switch">
                    <input type="checkbox" onchange="toggleFeature('live_visitor', this.checked)" <?php echo $config['enabled'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
            </div>

            <form action="/save-live-visitor-config" method="POST">
                <input type="hidden" name="widget_id" value="<?php echo $widget['id']; ?>">
                <!-- Hidden input to maintain legacy form structure if needed, but AJAX handles the toggle above -->

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

                <button type="submit" class="btn">Save Appearance</button>
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

<div id="toast" style="visibility: hidden; min-width: 250px; margin-left: -125px; background-color: #333; color: #fff; text-align: center; border-radius: 2px; padding: 16px; position: fixed; z-index: 1; left: 50%; bottom: 30px; font-size: 17px;">
  Setting updated successfully!
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

    const WIDGET_ID = <?php echo json_encode($widget['id']); ?>;

    async function toggleFeature(featureName, isEnabled) {
        try {
            const response = await fetch('/api/toggle-feature', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    feature: featureName,
                    enabled: isEnabled,
                    widget_id: WIDGET_ID
                })
            });
            const text = await response.text();
            try {
                const json = JSON.parse(text);
                if (json.success) {
                    showToast();
                } else {
                    console.error('API Error:', json);
                    alert('Failed to update setting: ' + (json.error || 'Unknown error'));
                }
            } catch (jsonError) {
                console.error('JSON Parse Error:', jsonError, text);
                alert('Server returned invalid JSON. Check console.');
            }
        } catch (e) {
            console.error(e);
            alert('Error updating setting.');
        }
    }

    function showToast() {
      var x = document.getElementById("toast");
      x.style.visibility = "visible";
      setTimeout(function(){ x.style.visibility = "hidden"; }, 3000);
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
