<?php
$activePage = 'campaigns';
$activeSubPage = 'newsletter';
$pageTitle = 'Newsletter';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
/* Reuse existing styles */
.switch { position: relative; display: inline-block; width: 50px; height: 26px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: var(--primary-color); }
input:focus + .slider { box-shadow: 0 0 1px var(--primary-color); }
input:checked + .slider:before { transform: translateX(24px); }

.newsletter-card {
    border: 1px solid #eee;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    background: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.newsletter-info h3 { margin: 0 0 5px 0; font-size: 1.1rem; }
.newsletter-meta { color: #666; font-size: 0.9rem; }
.newsletter-stats { display: flex; gap: 20px; margin-right: 20px; text-align: center; }
.stat-item { display: flex; flex-direction: column; }
.stat-value { font-weight: bold; font-size: 1.2rem; color: var(--primary-color); }
.stat-name { font-size: 0.8rem; color: #777; }

/* Modal Styles */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
.modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 8px; }
.close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
.close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }

.form-row { display: flex; gap: 15px; margin-bottom: 15px; }
.form-col { flex: 1; }
</style>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Manage Newsletters</h2>
        <button class="btn" onclick="openModal()">+ New Newsletter</button>
    </div>

    <?php if (empty($newsletters)): ?>
        <p style="text-align: center; color: #777; padding: 20px;">No newsletters created yet.</p>
    <?php else: ?>
        <?php foreach ($newsletters as $n): ?>
            <div class="newsletter-card" style="opacity: <?php echo $n['active'] ? '1' : '0.6'; ?>">
                <div class="newsletter-info">
                    <h3><?php echo htmlspecialchars($n['title']); ?></h3>
                    <div class="newsletter-meta">
                        <?php echo htmlspecialchars($n['trigger_type'] == 'exit_intent' ? 'Exit Intent' : 'Delay: ' . $n['trigger_delay'] . 's'); ?> &bull;
                        <?php echo htmlspecialchars($n['frequency'] == 'session' ? 'Once per session' : 'Every page load'); ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center;">
                    <div class="newsletter-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo $n['views']; ?></span>
                            <span class="stat-name">Views</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo $n['leads']; ?></span>
                            <span class="stat-name">Leads</span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="/campaigns/newsletter/leads/<?php echo $n['id']; ?>" class="btn" style="background: #17a2b8;">View Leads</a>
                        <button class="btn" style="background: #6c757d;" onclick='editNewsletter(<?php echo json_encode($n); ?>)'>Edit</button>
                        <a href="/campaigns/newsletter/delete/<?php echo $n['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="newsletterModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Create Newsletter</h2>

        <form action="/campaigns/newsletter/save" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="newsletter_id" id="newsletter_id">

            <div class="form-row">
                <div class="form-col">
                    <label>Title</label>
                    <input type="text" name="title" id="title" required value="Join Our Newsletter">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="description" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" rows="3" placeholder="Get the latest updates..."></textarea>
            </div>

            <div class="form-row">
                 <div class="form-col">
                    <label>Colors (Bg / Text)</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="color" name="bg_color" id="bg_color" value="#ffffff" style="height: 38px; padding: 2px;">
                        <input type="color" name="text_color" id="text_color" value="#333333" style="height: 38px; padding: 2px;">
                    </div>
                </div>
                 <div class="form-col">
                    <label class="toggle" style="margin-top: 25px;">
                        <input type="checkbox" name="remove_branding" id="remove_branding">
                        Remove Branding
                    </label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label>Image (Max 15MB)</label>
                    <input type="file" name="image_upload" id="image_upload" accept="image/*">
                    <div id="current_image_display" style="margin-top: 5px; font-size: 0.8rem; color: #666; display: none;">
                        Current: <a href="#" target="_blank" id="current_image_link">View</a>
                    </div>
                </div>
                <div class="form-col">
                    <label>Image Position</label>
                    <select name="image_style" id="image_style" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="top">Top</option>
                        <option value="left">Left (Split)</option>
                        <option value="right">Right (Split)</option>
                        <option value="background">Background</option>
                    </select>
                </div>
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            <h3>Form Fields</h3>

            <div class="form-row">
                 <div class="form-col">
                    <label class="toggle">
                        <input type="checkbox" name="allow_name" id="allow_name">
                        Include Name Field (Required if checked)
                    </label>
                </div>
                <div class="form-col">
                    <label class="toggle">
                        <input type="checkbox" name="allow_phone" id="allow_phone">
                        Include Phone Field (Required if checked)
                    </label>
                </div>
            </div>
            <p style="font-size: 0.8rem; color: #666; margin-bottom: 20px;">Email field is always required.</p>

            <div class="form-row">
                <div class="form-col">
                    <label>Button Text</label>
                    <input type="text" name="btn_text" id="btn_text" value="Subscribe">
                </div>
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            <h3>Submission Actions</h3>

            <div class="form-row">
                <div class="form-col">
                    <label>After Success</label>
                     <select name="success_action" id="success_action" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" onchange="toggleSuccessFields()">
                        <option value="message">Show Message</option>
                        <option value="redirect">Redirect URL</option>
                        <option value="close">Close Popup</option>
                    </select>
                </div>
            </div>

            <div class="form-group" id="success_message_group">
                <label>Success Message</label>
                <textarea name="success_message" id="success_message" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" rows="2">Thanks for subscribing!</textarea>
            </div>

            <div class="form-group" id="redirect_url_group" style="display:none;">
                <label>Redirect URL</label>
                <input type="text" name="redirect_url" id="redirect_url" placeholder="https://example.com/thank-you">
            </div>

            <div class="form-group">
                <label>Webhook URL (Optional)</label>
                <input type="text" name="webhook_url" id="webhook_url" placeholder="https://hooks.zapier.com/...">
                <p style="font-size: 0.8rem; color: #666;">We will POST JSON data to this URL upon submission.</p>
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            <h3>Triggers</h3>

            <div class="form-row">
                <div class="form-col">
                    <label>Trigger</label>
                    <select name="trigger_type" id="trigger_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="delay">Time Delay</option>
                        <option value="exit_intent">Exit Intent</option>
                    </select>
                </div>
                <div class="form-col">
                    <label>Delay (Seconds)</label>
                    <input type="number" name="trigger_delay" id="trigger_delay" value="0" min="0" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label>Frequency</label>
                    <select name="frequency" id="frequency" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="every_load">Every Page Load</option>
                        <option value="session">Once per Visitor (30 mins)</option>
                    </select>
                </div>
                <div class="form-col">
                    <label>Match URL (Optional)</label>
                    <input type="text" name="match_url" id="match_url" placeholder="e.g. /pricing">
                </div>
            </div>

             <div class="form-group">
                <label class="toggle">
                    <input type="checkbox" name="active" id="active" checked>
                    Enable this newsletter
                </label>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="btn">Save Newsletter</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById("newsletterModal");
    const modalTitle = document.getElementById("modalTitle");

    const formInputs = {
        newsletter_id: document.getElementById("newsletter_id"),
        title: document.getElementById("title"),
        description: document.getElementById("description"),
        btn_text: document.getElementById("btn_text"),
        allow_name: document.getElementById("allow_name"),
        allow_phone: document.getElementById("allow_phone"),
        webhook_url: document.getElementById("webhook_url"),
        success_action: document.getElementById("success_action"),
        success_message: document.getElementById("success_message"),
        redirect_url: document.getElementById("redirect_url"),
        bg_color: document.getElementById("bg_color"),
        text_color: document.getElementById("text_color"),
        trigger_type: document.getElementById("trigger_type"),
        trigger_delay: document.getElementById("trigger_delay"),
        frequency: document.getElementById("frequency"),
        match_url: document.getElementById("match_url"),
        active: document.getElementById("active"),
        image_style: document.getElementById("image_style"),
        remove_branding: document.getElementById("remove_branding")
    };

    const currentImageDisplay = document.getElementById("current_image_display");
    const currentImageLink = document.getElementById("current_image_link");

    function openModal() {
        modal.style.display = "block";
        resetForm();
    }

    function closeModal() {
        modal.style.display = "none";
    }

    function toggleSuccessFields() {
        const val = document.getElementById("success_action").value;
        document.getElementById("success_message_group").style.display = val === 'message' ? 'block' : 'none';
        document.getElementById("redirect_url_group").style.display = val === 'redirect' ? 'block' : 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    function resetForm() {
        modalTitle.textContent = "Create Newsletter";
        formInputs.newsletter_id.value = "";
        formInputs.title.value = "Join Our Newsletter";
        formInputs.description.value = "";
        formInputs.btn_text.value = "Subscribe";
        formInputs.allow_name.checked = false;
        formInputs.allow_phone.checked = false;
        formInputs.webhook_url.value = "";
        formInputs.success_action.value = "message";
        formInputs.success_message.value = "Thanks for subscribing!";
        formInputs.redirect_url.value = "";

        formInputs.bg_color.value = "#ffffff";
        formInputs.text_color.value = "#333333";
        formInputs.trigger_type.value = "delay";
        formInputs.trigger_delay.value = "0";
        formInputs.frequency.value = "every_load";
        formInputs.match_url.value = "";
        formInputs.active.checked = true;
        formInputs.image_style.value = "top";
        formInputs.remove_branding.checked = false;
        currentImageDisplay.style.display = "none";
        document.getElementById("image_upload").value = "";
        toggleSuccessFields();
    }

    function editNewsletter(data) {
        modalTitle.textContent = "Edit Newsletter";
        formInputs.newsletter_id.value = data.id;
        formInputs.title.value = data.title;
        formInputs.description.value = data.description || "";
        formInputs.btn_text.value = data.btn_text;

        formInputs.allow_name.checked = data.allow_name == 1;
        formInputs.allow_phone.checked = data.allow_phone == 1;
        formInputs.webhook_url.value = data.webhook_url || "";
        formInputs.success_action.value = data.success_action || "message";
        formInputs.success_message.value = data.success_message || "";
        formInputs.redirect_url.value = data.redirect_url || "";

        formInputs.bg_color.value = data.bg_color;
        formInputs.text_color.value = data.text_color;
        formInputs.trigger_type.value = data.trigger_type;
        formInputs.trigger_delay.value = data.trigger_delay;
        formInputs.frequency.value = data.frequency;
        formInputs.match_url.value = data.match_url || "";
        formInputs.active.checked = data.active == 1;
        formInputs.image_style.value = data.image_style || "top";
        formInputs.remove_branding.checked = data.remove_branding == 1;

        if (data.image_url) {
            currentImageDisplay.style.display = "block";
            currentImageLink.href = data.image_url;
        } else {
            currentImageDisplay.style.display = "none";
        }
        document.getElementById("image_upload").value = "";
        toggleSuccessFields();

        modal.style.display = "block";
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
