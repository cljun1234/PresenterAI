<?php
$activePage = 'campaigns';
$activeSubPage = 'live-conversion';
$pageTitle = 'Live Conversion';
require_once __DIR__ . '/../layouts/header.php';
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
.setting-row { display: flex; align-items: flex-start; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee; flex-wrap: wrap; }
.setting-info { flex: 1; min-width: 200px; padding-right: 20px; }
.setting-info h3 { margin: 0 0 5px 0; font-size: 1rem; }
.setting-info p { margin: 0; color: #777; font-size: 0.9rem; }

/* Widget Preview Styles */
.widget-preview-wrapper {
    width: 100%;
    margin-top: 15px;
    padding: 15px;
    background: #f4f6f8;
    border: 1px dashed #ccc;
    border-radius: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.sales-notification-widget {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 10px;
    display: flex;
    align-items: center;
    width: 300px;
    font-family: sans-serif;
    color: #333;
    position: relative;
}
.sales-notification-widget .map-placeholder { width: 50px; height: 50px; background: #eee; border-radius: 4px; margin-right: 10px; flex-shrink: 0; overflow: hidden; }
.sales-notification-widget .map-placeholder img { width: 100%; height: 100%; object-fit: cover; }
.sales-notification-widget .content { display: flex; flex-direction: column; justify-content: center; flex-grow: 1; line-height: 1.2; }
.sales-notification-widget .name { font-weight: 700; color: inherit; font-size: 14px; margin: 0; }
.sales-notification-widget .action-text { margin: 2px 0 5px 0; color: inherit; opacity: 0.8; font-size: 13px; }
.sales-notification-widget .verification { display: flex; align-items: center; font-size: 11px; color: #1a73e8; font-weight: 500; }
.sales-notification-widget .checkmark { font-weight: bold; margin-right: 4px; }
</style>

<!-- Main Toggle & Settings -->
<div class="card">
    <h2>Configuration</h2>

    <div class="setting-row">
        <div class="setting-info">
            <h3>Enable Live Conversion</h3>
            <p>Show conversion notifications on your site.</p>
        </div>
        <label class="switch">
            <input type="checkbox" onchange="toggleFeature('live_conversion', this.checked)" <?php echo ($widget['live_conversion_enabled'] ?? false) ? 'checked' : ''; ?>>
            <span class="slider"></span>
        </label>
        <!-- No specific preview for the master toggle, as it enables the system generally -->
    </div>

    <div class="setting-row">
        <div class="setting-info">
            <h3>Magical Detection</h3>
            <p>Automatically capture form submissions as real conversions.</p>
        </div>
        <label class="switch">
            <input type="checkbox" onchange="toggleFeature('magical_detection', this.checked)" <?php echo ($widget['magical_detection'] ?? true) ? 'checked' : ''; ?>>
            <span class="slider"></span>
        </label>
        <div class="widget-preview-wrapper">
             <div class="sales-notification-widget">
                <div class="map-placeholder"><img src="https://provely-public.s3.amazonaws.com/images/maps/default.jpg" alt="map" /></div>
                <div class="content">
                     <p class="name">Captured Lead</p>
                     <p class="action-text">Submitted 'Contact Us' form</p>
                     <div class="verification" style="display:flex">
                        <span class="checkmark">&#x2713;</span>
                        <span class="verified-text">Verified by TrustPilot</span>
                    </div>
                </div>
             </div>
             <div style="font-size: 0.8rem; color: #666; margin-left: 15px;">Example of captured data</div>
        </div>
    </div>

    <div class="setting-row">
        <div class="setting-info">
            <h3>Show Real Conversions</h3>
            <p>Display actual data captured from your visitors.</p>
        </div>
        <label class="switch">
            <input type="checkbox" onchange="toggleFeature('use_real_conversion', this.checked)" <?php echo ($widget['use_real_conversion'] ?? true) ? 'checked' : ''; ?>>
            <span class="slider"></span>
        </label>
        <div class="widget-preview-wrapper">
            <div class="sales-notification-widget">
                <div class="map-placeholder"><img src="https://provely-public.s3.amazonaws.com/images/maps/default.jpg" alt="map" /></div>
                <div class="content">
                     <p class="name">A visitor from London</p>
                     <p class="action-text">Just signed up</p>
                     <div class="verification" style="display:flex">
                        <span class="checkmark">&#x2713;</span>
                        <span class="verified-text">Verified by TrustPilot</span>
                    </div>
                </div>
             </div>
        </div>
    </div>

    <div class="setting-row">
        <div class="setting-info">
            <h3>Show Simulated Conversions</h3>
            <p>Mix in the manual notifications defined below.</p>
        </div>
        <label class="switch">
            <input type="checkbox" onchange="toggleFeature('use_simulated_conversion', this.checked)" <?php echo ($widget['use_simulated_conversion'] ?? true) ? 'checked' : ''; ?>>
            <span class="slider"></span>
        </label>
        <div class="widget-preview-wrapper">
            <div class="sales-notification-widget">
                <div class="map-placeholder"><img src="https://provely-public.s3.amazonaws.com/images/maps/default.jpg" alt="map" /></div>
                <div class="content">
                     <p class="name">John D.</p>
                     <p class="action-text">Purchased a Pro Plan</p>
                     <!-- Simulated data has NO verified badge -->
                </div>
             </div>
        </div>
    </div>
</div>

<!-- Simulated Data Section -->
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

<!-- Real Data Section -->
<div class="card">
    <h2>Recent Real Conversions</h2>
    <p>Data captured via Magical Detection.</p>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Page</th>
                <th>Payload</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($real_events ?? [] as $event): ?>
            <tr>
                <td><?php echo date('M d, H:i', strtotime($event['created_at'])); ?></td>
                <td><a href="<?php echo htmlspecialchars($event['page_url']); ?>" target="_blank" style="color:var(--primary-color)"><?php echo htmlspecialchars(parse_url($event['page_url'], PHP_URL_PATH)); ?></a></td>
                <td style="font-size: 0.85rem; color: #555;"><?php echo htmlspecialchars(substr($event['payload'], 0, 50)) . '...'; ?></td>
                <td style="text-align: right;">
                    <a href="/event/delete/<?php echo $event['id']; ?>" class="btn btn-sm">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($real_events)): ?>
             <tr>
                <td colspan="4" style="text-align: center; color: #999;">No real conversions detected yet.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="toast" style="visibility: hidden; min-width: 250px; margin-left: -125px; background-color: #333; color: #fff; text-align: center; border-radius: 2px; padding: 16px; position: fixed; z-index: 1; left: 50%; bottom: 30px; font-size: 17px;">
  Setting updated successfully!
</div>

<script>
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
