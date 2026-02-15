<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamma Interface - Templates</title>
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

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .presentation-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }

        .presentation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .card-preview {
            height: 150px;
            background-color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            font-size: 2rem;
        }

        .card-info {
            padding: 15px;
        }

        .ppt-title {
            font-weight: 600;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ppt-meta {
            font-size: 0.8rem;
            color: #888;
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
        <a href="/templates" class="nav-item active">
            <span>📄</span> Templates
        </a>
        <a href="/themes" class="nav-item">
            <span>🎨</span> Themes
        </a>
        <a href="/fonts" class="nav-item">
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
                <div class="section-title">Templates</div>
            </div>

            <div class="grid">
                 <!-- Placeholder Templates -->
                 <div class="presentation-card">
                    <div class="card-preview">📄</div>
                    <div class="card-info">
                        <div class="ppt-title">Company Handbook</div>
                        <div class="ppt-meta">Template</div>
                    </div>
                </div>
                <div class="presentation-card">
                    <div class="card-preview">📄</div>
                    <div class="card-info">
                        <div class="ppt-title">Sales Pitch Deck</div>
                        <div class="ppt-meta">Template</div>
                    </div>
                </div>
                 <div class="presentation-card">
                    <div class="card-preview">📄</div>
                    <div class="card-info">
                        <div class="ppt-title">Project Proposal</div>
                        <div class="ppt-meta">Template</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
