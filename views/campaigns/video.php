<?php
$activePage = 'campaigns';
$activeSubPage = 'video';
$pageTitle = 'Videos';
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

.video-card {
    border: 1px solid #eee;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    background: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.video-info h3 { margin: 0 0 5px 0; font-size: 1.1rem; }
.video-meta { color: #666; font-size: 0.9rem; }
.video-stats { display: flex; gap: 20px; margin-right: 20px; text-align: center; }
.stat-item { display: flex; flex-direction: column; }
.stat-value { font-weight: bold; font-size: 1.2rem; color: var(--primary-color); }
.stat-name { font-size: 0.8rem; color: #777; }

/* Modal Styles for Edit/Create */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
.modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 8px; }
.close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
.close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }

.form-row { display: flex; gap: 15px; margin-bottom: 15px; }
.form-col { flex: 1; }
</style>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Manage Videos</h2>
        <button class="btn" onclick="openModal()">+ New Video</button>
    </div>

    <?php if (empty($videos)): ?>
        <p style="text-align: center; color: #777; padding: 20px;">No videos created yet.</p>
    <?php else: ?>
        <?php foreach ($videos as $v): ?>
            <div class="video-card" style="opacity: <?php echo $v['active'] ? '1' : '0.6'; ?>">
                <div class="video-info">
                    <h3><?php echo htmlspecialchars($v['title']); ?></h3>
                    <div class="video-meta">
                        <?php echo htmlspecialchars($v['trigger_type'] == 'exit_intent' ? 'Exit Intent' : 'Delay: ' . $v['trigger_delay'] . 's'); ?> &bull;
                        <?php echo htmlspecialchars($v['frequency'] == 'session' ? 'Once per session' : 'Every page load'); ?>
                        <?php if($v['match_url']): ?> &bull; URL: <?php echo htmlspecialchars($v['match_url']); ?><?php endif; ?>
                    </div>
                </div>
                <div style="display: flex; align-items: center;">
                    <div class="video-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo $v['views']; ?></span>
                            <span class="stat-name">Views</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo $v['clicks']; ?></span>
                            <span class="stat-name">Clicks</span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn" style="background: #6c757d;" onclick='editVideo(<?php echo json_encode($v); ?>)'>Edit</button>
                        <a href="/campaigns/video/delete/<?php echo $v['id']; ?>" class="btn btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="videoModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Create Video</h2>

        <form action="/campaigns/video/save" method="POST">
            <input type="hidden" name="video_id" id="video_id">

            <div class="form-row">
                <div class="form-col">
                    <label>Title</label>
                    <input type="text" name="title" id="title" required value="Check this out">
                </div>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" id="message" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" rows="3" placeholder="Enter description..."></textarea>
            </div>

            <div class="form-group">
                <label>Video URL (YouTube, Vimeo, or Hosted)</label>
                <input type="text" name="video_url" id="video_url" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required placeholder="https://www.youtube.com/watch?v=...">
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label>Button Text</label>
                    <input type="text" name="btn_text" id="btn_text" value="Learn More">
                </div>
                <div class="form-col">
                    <label>Button Action</label>
                     <select name="btn_action" id="btn_action" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" onchange="toggleLinkInput()">
                        <option value="link">Go to URL</option>
                        <option value="close">Close Popup</option>
                    </select>
                </div>
            </div>

            <div class="form-group" id="link_group">
                <label>Button URL</label>
                <input type="text" name="btn_link" id="btn_link" placeholder="https://example.com/page">
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

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            <h3>Rules</h3>

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
                    Enable this video
                </label>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" class="btn">Save Video</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById("videoModal");
    const modalTitle = document.getElementById("modalTitle");
    const linkGroup = document.getElementById("link_group");
    const formInputs = {
        video_id: document.getElementById("video_id"),
        title: document.getElementById("title"),
        message: document.getElementById("message"),
        video_url: document.getElementById("video_url"),
        btn_text: document.getElementById("btn_text"),
        btn_action: document.getElementById("btn_action"),
        btn_link: document.getElementById("btn_link"),
        bg_color: document.getElementById("bg_color"),
        text_color: document.getElementById("text_color"),
        trigger_type: document.getElementById("trigger_type"),
        trigger_delay: document.getElementById("trigger_delay"),
        frequency: document.getElementById("frequency"),
        match_url: document.getElementById("match_url"),
        active: document.getElementById("active"),
        remove_branding: document.getElementById("remove_branding")
    };

    function openModal() {
        modal.style.display = "block";
        resetForm();
    }

    function closeModal() {
        modal.style.display = "none";
    }

    function toggleLinkInput() {
        if (formInputs.btn_action.value === 'link') {
            linkGroup.style.display = 'block';
        } else {
            linkGroup.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    }

    function resetForm() {
        modalTitle.textContent = "Create Video";
        formInputs.video_id.value = "";
        formInputs.title.value = "Check this out";
        formInputs.message.value = "";
        formInputs.video_url.value = "";
        formInputs.btn_text.value = "Learn More";
        formInputs.btn_action.value = "link";
        formInputs.btn_link.value = "";
        formInputs.bg_color.value = "#ffffff";
        formInputs.text_color.value = "#333333";
        formInputs.trigger_type.value = "delay";
        formInputs.trigger_delay.value = "0";
        formInputs.frequency.value = "every_load";
        formInputs.match_url.value = "";
        formInputs.active.checked = true;
        formInputs.remove_branding.checked = false;
        toggleLinkInput();
    }

    function editVideo(data) {
        modalTitle.textContent = "Edit Video";
        formInputs.video_id.value = data.id;
        formInputs.title.value = data.title;
        formInputs.message.value = data.message || "";
        formInputs.video_url.value = data.video_url;
        formInputs.btn_text.value = data.btn_text;
        formInputs.btn_action.value = data.btn_action;
        formInputs.btn_link.value = data.btn_link || "";
        formInputs.bg_color.value = data.bg_color;
        formInputs.text_color.value = data.text_color;
        formInputs.trigger_type.value = data.trigger_type;
        formInputs.trigger_delay.value = data.trigger_delay;
        formInputs.frequency.value = data.frequency;
        formInputs.match_url.value = data.match_url || "";
        formInputs.active.checked = data.active == 1;
        formInputs.remove_branding.checked = data.remove_branding == 1;
        toggleLinkInput();

        modal.style.display = "block";
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
