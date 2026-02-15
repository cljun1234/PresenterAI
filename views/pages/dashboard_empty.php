<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamma Interface - Dashboard</title>
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
            text-decoration: none;
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

        /* Empty State Area */
        .empty-state-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding: 40px;
        }

        .choice-card {
            background: linear-gradient(135deg, #2D2D2D 0%, #1A1A1A 100%);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 30px;
            width: 300px;
            height: 350px;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center content vertically */
            align-items: center;     /* Center content horizontally */
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: white;
        }

        .choice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-color: var(--accent-color);
        }

        .card-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-desc {
            color: #aaa;
            font-size: 0.95rem;
            line-height: 1.5;
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

        <a href="/" class="nav-item active">
            <span>🏠</span> Home
        </a>
        <a href="/templates" class="nav-item">
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

        <div class="empty-state-container">
            <a href="/create" class="choice-card">
                <div class="card-icon">✨</div>
                <div class="card-title">Generate</div>
                <div class="card-desc">Create from a one-line prompt in a few seconds</div>
            </a>

            <div class="choice-card">
                <div class="card-icon">📝</div>
                <div class="card-title">Paste in text</div>
                <div class="card-desc">Create from notes, an outline, or existing content</div>
            </div>
        </div>
    </div>

</body>
</html>
