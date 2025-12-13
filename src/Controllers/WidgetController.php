<?php

class WidgetController {

    // Returns the JS file dynamically
    public function serveScript() {
        header('Content-Type: application/javascript');

        $widget_id = (int)($_GET['w'] ?? 0);

        // Construct the base URL for API calls
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . $host . '/api';

        echo <<<JS
(function() {
    const WIDGET_ID = '$widget_id';
    const API_BASE = '$baseUrl';

    // Configuration
    const DISPLAY_DURATION_MS = 5000;
    const HIDE_DURATION_MS = 5000;
    const CONTAINER_ID = 'trustabee-widget';

    let data = [];
    let currentIndex = 0;
    let widgetElement;
    let timerId;

    // --- API Calls ---

    async function fetchData() {
        try {
            const response = await fetch(`\${API_BASE}/data?w=\${WIDGET_ID}`);
            const json = await response.json();

            // Build queue
            data = [];

            // 1. Live count
            // Only if enabled in config
            const isLiveEnabled = json.config && json.config.live_visitor_enabled;
            if (isLiveEnabled && json.live_count > 0) {
                 data.push({
                    type: 'live_count',
                    count: json.live_count,
                    text: `\${json.live_count} people are viewing this page right now.`
                 });
            }

            // 2. Historical
            if (json.historical_count > 0) {
                data.push({
                    type: 'historical',
                    count: json.historical_count,
                    text: `\${json.historical_count} people signed up in the last 7 days.`
                });
            }

            // 3. Notifications (Real + Simulated)
            if (json.notifications && json.notifications.length > 0) {
                data = data.concat(json.notifications);
            }

            if (data.length > 0) {
                initWidget(json.config ? json.config.live_visitor_config : null);
            }

            if (json.config && json.config.magical_detection) {
                enableMagicalDetection();
            }
        } catch (e) {
            console.error('Trustabee: Error fetching data', e);
        }
    }

    function sendHeartbeat() {
        let visitorId = localStorage.getItem('trustabee_vid');
        if (!visitorId) {
            visitorId = 'v_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('trustabee_vid', visitorId);
        }

        const payload = new URLSearchParams();
        payload.append('w', WIDGET_ID);
        payload.append('vid', visitorId);
        payload.append('url', window.location.href);

        if (navigator.sendBeacon) {
            navigator.sendBeacon(`\${API_BASE}/heartbeat`, payload);
        } else {
            fetch(`\${API_BASE}/heartbeat`, { method: 'POST', body: payload });
        }
    }

    function trackConversion(formData) {
        let visitorId = localStorage.getItem('trustabee_vid');
        const payload = new URLSearchParams();
        payload.append('w', WIDGET_ID);
        payload.append('vid', visitorId);
        payload.append('type', 'form_submit');
        payload.append('page', window.location.href);

        let formObj = {};
        formData.forEach((value, key) => { formObj[key] = value });
        payload.append('payload', JSON.stringify(formObj));

         if (navigator.sendBeacon) {
            navigator.sendBeacon(`\${API_BASE}/track`, payload);
        } else {
            fetch(`\${API_BASE}/track`, { method: 'POST', body: payload });
        }
    }

    // --- Widget Logic ---

    function createWidget() {
        if (document.getElementById(CONTAINER_ID)) return;

        widgetElement = document.createElement('div');
        widgetElement.id = CONTAINER_ID;
        widgetElement.className = 'sales-notification-widget hide';

        widgetElement.innerHTML = `
            <div class="map-placeholder"><img src="https://provely-public.s3.amazonaws.com/images/maps/default.jpg" alt="map" /></div>
            <div class="content">
                <p class="name"></p>
                <p class="action-text"></p>
                <div class="verification" style="display:none">
                    <span class="checkmark">&#x2713;</span>
                    <span class="verified-text">Verified by TrustPilot</span>
                </div>
            </div>
        `;
        document.body.appendChild(widgetElement);
    }

    function updateWidgetContent() {
        const item = data[currentIndex];
        if (!item) return;

        const mapEl = widgetElement.querySelector('.map-placeholder');
        const nameEl = widgetElement.querySelector('.name');
        const actionEl = widgetElement.querySelector('.action-text');
        const verifyEl = widgetElement.querySelector('.verification');

        // Reset defaults
        verifyEl.style.display = 'none';
        mapEl.style.display = 'block';

        if (item.type === 'live_count') {
            mapEl.style.display = 'none'; // Hide map for simple count, or show eye icon
            nameEl.textContent = 'Live Visitors';
            actionEl.textContent = item.text;
        } else if (item.type === 'historical') {
            mapEl.style.display = 'none';
            nameEl.textContent = 'Popular';
            actionEl.textContent = item.text;
        } else {
            // Normal notification
            nameEl.textContent = item.name;
            actionEl.textContent = item.actionText;
            if (item.is_real) {
                verifyEl.style.display = 'flex';
            }
        }
    }

    function startCycle() {
        if (data.length === 0) return;

        updateWidgetContent();
        widgetElement.classList.remove('hide');

        setTimeout(() => {
            widgetElement.classList.add('hide');
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % data.length;
                startCycle();
            }, HIDE_DURATION_MS);
        }, DISPLAY_DURATION_MS);
    }

    function injectStyles(config) {
        let bottom = '20px';
        let left = '20px';
        let right = 'auto';
        let bgColor = 'white';
        let textColor = '#333';

        if (config) {
            if (config.position === 'bottom-right') {
                left = 'auto';
                right = '20px';
            }
            if (config.bg_color) bgColor = config.bg_color;
            if (config.text_color) textColor = config.text_color;
        }

        const style = document.createElement('style');
        style.innerHTML = `
            .sales-notification-widget {
                position: fixed;
                bottom: \${bottom};
                left: \${left};
                right: \${right};
                z-index: 9999;
                background: \${bgColor};
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                padding: 10px;
                display: flex;
                align-items: center;
                width: 300px;
                opacity: 1;
                transform: translateX(0);
                transition: opacity 0.5s, transform 0.5s;
                font-family: sans-serif;
                color: \${textColor};
            }
            .sales-notification-widget.hide {
                opacity: 0;
                transform: translateX(\${left === 'auto' ? '150%' : '-150%'});
            }
            .sales-notification-widget .map-placeholder { width: 50px; height: 50px; background: #eee; border-radius: 4px; margin-right: 10px; flex-shrink: 0; overflow: hidden; }
            .sales-notification-widget .map-placeholder img { width: 100%; height: 100%; object-fit: cover; }
            .sales-notification-widget .content { display: flex; flex-direction: column; justify-content: center; flex-grow: 1; line-height: 1.2; }
            .sales-notification-widget .name { font-weight: 700; color: inherit; font-size: 14px; margin: 0; }
            .sales-notification-widget .action-text { margin: 2px 0 5px 0; color: inherit; opacity: 0.8; font-size: 13px; }
            .sales-notification-widget .verification { display: flex; align-items: center; font-size: 11px; color: #1a73e8; font-weight: 500; }
            .sales-notification-widget .checkmark { font-weight: bold; margin-right: 4px; }
        `;
        document.head.appendChild(style);
    }

    function initWidget(config) {
        injectStyles(config);
        createWidget();
        startCycle();
    }

    function enableMagicalDetection() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const formData = new FormData(form);
            trackConversion(formData);
        });
    }

    setInterval(sendHeartbeat, 30000);
    sendHeartbeat();
    fetchData();

})();
JS;
    }

    public function getData() {
        $this->addCors();
        header('Content-Type: application/json');

        $widget_id = $_GET['w'] ?? 0;
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT * FROM widgets WHERE id = ?");
        $stmt->execute([$widget_id]);
        $widget = $stmt->fetch();

        if (!$widget) {
            echo json_encode(['error' => 'Widget not found']);
            return;
        }

        // Lazy Logging: Check if we need to take a snapshot
        $this->logTrafficSnapshot($widget_id, $pdo);

        // 1. Live Visitors (Active in last 30 minutes, distinct)
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT visitor_id) as count FROM live_visitors WHERE widget_id = ? AND last_seen > (NOW() - INTERVAL 30 MINUTE)");
        $stmt->execute([$widget_id]);
        $live_count = $stmt->fetch()['count'];

        // 2. Historical (Last 7 days events)
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM events WHERE widget_id = ? AND created_at > (NOW() - INTERVAL 7 DAY)");
        $stmt->execute([$widget_id]);
        $historical_count = $stmt->fetch()['count'];

        // 3. Simulated Data
        $stmt = $pdo->prepare("SELECT name, action_text as actionText, location, image_url FROM notifications WHERE widget_id = ? AND active = 1");
        $stmt->execute([$widget_id]);
        $simulated = $stmt->fetchAll();
        foreach ($simulated as &$s) { $s['is_real'] = false; }

        // 4. Real Data
        $stmt = $pdo->prepare("SELECT * FROM events WHERE widget_id = ? AND type='form_submit' ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$widget_id]);
        $real_events = $stmt->fetchAll();

        $real = [];
        foreach ($real_events as $ev) {
            $real[] = [
                'name' => 'A visitor',
                'actionText' => 'Just signed up',
                'is_real' => true
            ];
        }

        $notifications = array_merge($real, $simulated);

        $live_config = json_decode($widget['live_visitor_config'] ?? '{}', true);

        echo json_encode([
            'config' => [
                'magical_detection' => (bool)$widget['magical_detection'],
                'live_visitor_enabled' => (bool)($widget['live_visitor_enabled'] ?? false),
                'live_visitor_config' => $live_config
            ],
            'live_count' => $live_count,
            'historical_count' => $historical_count,
            'notifications' => $notifications
        ]);
    }

    private function logTrafficSnapshot($widget_id, $pdo) {
        // Check for any snapshot in the last 5 minutes (to avoid race conditions/duplicates)
        $stmt = $pdo->prepare("SELECT id FROM traffic_snapshots WHERE widget_id = ? AND created_at > (NOW() - INTERVAL 5 MINUTE) LIMIT 1");
        $stmt->execute([$widget_id]);
        $recent_exists = $stmt->fetchColumn();

        if (!$recent_exists) {
            // Take snapshot
            // Count distinct visitors active in last 30 mins
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT visitor_id) FROM live_visitors WHERE widget_id = ? AND last_seen > (NOW() - INTERVAL 30 MINUTE)");
            $stmt->execute([$widget_id]);
            $count = $stmt->fetchColumn();

            $stmt = $pdo->prepare("INSERT INTO traffic_snapshots (widget_id, visitor_count, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$widget_id, $count]);
        }
    }

    public function heartbeat() {
        $this->addCors();
        $widget_id = $_POST['w'] ?? 0;
        $visitor_id = $_POST['vid'] ?? 'unknown';
        $url = $_POST['url'] ?? '';

        if (!$widget_id) return;

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT id FROM live_visitors WHERE widget_id = ? AND visitor_id = ?");
        $stmt->execute([$widget_id, $visitor_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $pdo->prepare("UPDATE live_visitors SET last_seen = NOW(), url = ? WHERE id = ?");
            $stmt->execute([$url, $exists['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO live_visitors (widget_id, visitor_id, url, last_seen) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$widget_id, $visitor_id, $url]);
        }
    }

    public function trackEvent() {
        $this->addCors();
        $widget_id = $_POST['w'] ?? 0;
        $type = $_POST['type'] ?? 'unknown';
        $payload = $_POST['payload'] ?? '';
        $visitor_id = $_POST['vid'] ?? null;
        $page = $_POST['page'] ?? '';

        if (!$widget_id) return;

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO events (widget_id, type, payload, visitor_id, page_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$widget_id, $type, $payload, $visitor_id, $page]);
    }

    private function addCors() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }
    }
}
