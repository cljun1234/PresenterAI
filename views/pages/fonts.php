<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamma Interface - Custom Fonts</title>
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

        /* Content Area */
        .dashboard-container {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .upload-area {
            border: 2px dashed #444;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .upload-area:hover {
            border-color: var(--accent-color);
        }

    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">
            <span>⚡</span> Gamma
        </div>

        <a href="/create" style="text-decoration: none;">
            <button class="btn-new">
                <span>+</span> New with AI
            </button>
        </a>

        <a href="/" class="nav-item">
            <span>🏠</span> Home
        </a>
        <a href="/templates" class="nav-item">
            <span>📄</span> Templates
        </a>
        <a href="/themes" class="nav-item">
            <span>🎨</span> Themes
        </a>
        <a href="/fonts" class="nav-item active">
            <span>Aa</span> Custom Fonts
        </a>

        <div style="flex-grow: 1;"></div>

        <a href="/trash" class="nav-item">
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

        <div class="dashboard-container">
            <div class="section-header">
                <div class="section-title">Custom Fonts</div>
            </div>

            <div class="upload-area">
               <div style="font-size: 2rem; margin-bottom: 10px;">⬆️</div>
               <div>Upload your font file (TTF, OTF)</div>
            </div>
        </div>
    </div>

</body>
</html>
