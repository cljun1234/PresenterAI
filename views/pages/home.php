<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamma Interface</title>
    <style>
        :root {
            --bg-color: #1A1A1A;
            --sidebar-bg: #111111;
            --text-color: #FFFFFF;
            --accent-color: #7C3AED;
            --accent-hover: #6D28D9;
            --card-bg: #2D2D2D;
            --border-color: #333;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 20px;
            flex-shrink: 0;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 30px;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo span {
            color: var(--accent-color);
        }

        .nav-item {
            padding: 10px 15px;
            border-radius: 8px;
            color: #aaa;
            text-decoration: none;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s, color 0.2s;
            cursor: pointer;
        }

        .nav-item:hover {
            background-color: rgba(255,255,255,0.05);
            color: var(--text-color);
        }

        .nav-item.active {
            background-color: rgba(124, 58, 237, 0.1);
            color: var(--accent-color);
            font-weight: 500;
        }

        .btn-new {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-new:hover {
            background-color: var(--accent-hover);
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .top-bar {
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-link {
            color: #aaa;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .logout-link:hover {
            color: #ff4d4d;
        }

        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Center Area */
        .creation-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-align: center;
            background: linear-gradient(135deg, #fff 0%, #aaa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: #888;
            margin-bottom: 40px;
            font-size: 1.1rem;
            text-align: center;
        }

        .input-container {
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            position: relative;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .tab {
            background: rgba(255,255,255,0.05);
            border: 1px solid transparent;
            color: #ccc;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab:hover {
            background: rgba(255,255,255,0.1);
        }

        .tab.active {
            background: rgba(124, 58, 237, 0.2);
            color: var(--accent-color);
            border-color: rgba(124, 58, 237, 0.5);
        }

        textarea {
            width: 100%;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.1rem;
            resize: none;
            outline: none;
            min-height: 80px;
            font-family: inherit;
        }

        textarea::placeholder {
            color: #555;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 15px;
        }

        .generate-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .generate-btn:hover {
            background: var(--accent-hover);
        }

        .icon-btn {
            background: transparent;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
        }

        .icon-btn:hover {
            color: #aaa;
            background: rgba(255,255,255,0.05);
        }

    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">
            <span>⚡</span> Gamma
        </div>

        <button class="btn-new">
            <span>+</span> New with AI
        </button>

        <a href="#" class="nav-item active">
            <span>🏠</span> Home
        </a>
        <a href="#" class="nav-item">
            <span>📄</span> Templates
        </a>
        <a href="#" class="nav-item">
            <span>🎨</span> Themes
        </a>
        <a href="#" class="nav-item">
            <span>Aa</span> Custom Fonts
        </a>

        <div style="flex-grow: 1;"></div>

        <a href="#" class="nav-item">
            <span>🗑️</span> Trash
        </a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="user-menu">
                <a href="/logout" class="logout-link">Log out</a>
                <div class="avatar">U</div>
            </div>
        </div>

        <div class="creation-area">
            <h1>Create with AI</h1>
            <div class="subtitle">What would you like to create today?</div>

            <div class="input-container">
                <div class="tabs">
                    <button class="tab active">Presentation</button>
                    <button class="tab">Document</button>
                    <button class="tab">Webpage</button>
                </div>

                <textarea placeholder="Describe what you'd like to make, e.g. 'A pitch deck for a new coffee brand called BeanThere'"></textarea>

                <div class="action-bar">
                    <div style="display: flex; gap: 5px;">
                        <button class="icon-btn" title="Add file">📎</button>
                        <button class="icon-btn" title="Settings">⚙️</button>
                    </div>
                    <button class="generate-btn">
                        Generate outline <span>→</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
