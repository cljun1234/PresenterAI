<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamma Interface - Generate</title>
    <style>
        :root {
            --bg-color: #1A1A1A;
            --sidebar-bg: #111111;
            --text-color: #FFFFFF;
            --accent-color: #7C3AED;
            --accent-hover: #6D28D9;
            --card-bg: #2D2D2D;
            --border-color: #333;
            --input-bg: rgba(255,255,255,0.08);
            --dropdown-bg: #333;
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
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        h1 {
            font-size: 2.8rem;
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

        .input-wrapper {
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.3);
            position: relative;
        }

        .type-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .type-btn {
            background: rgba(124, 58, 237, 0.1); /* Subtle purple tint */
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .type-btn:hover {
            background: rgba(124, 58, 237, 0.2);
        }

        .type-btn.inactive {
             /* Style for inactive buttons if we had them, currently just Presentation is active */
             background: transparent;
             border-color: transparent;
             color: #888;
        }

        .options-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        select {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            outline: none;
            cursor: pointer;
            appearance: none; /* Hide default arrow */
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
            padding-right: 30px;
        }

        select:hover {
            background-color: rgba(255,255,255,0.12);
        }

        select option {
            background: var(--dropdown-bg);
            color: white;
        }

        textarea {
            width: 100%;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.2rem;
            resize: none;
            outline: none;
            min-height: 100px;
            font-family: inherit;
            margin-bottom: 20px;
        }

        textarea::placeholder {
            color: #555;
        }

        .action-bar {
            display: flex;
            justify-content: flex-end; /* Align generate button to right */
            align-items: center;
            /* border-top: 1px solid rgba(255,255,255,0.05); */
            padding-top: 10px;
        }

        .generate-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 30px; /* Pill shape */
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, transform 0.1s;
            font-size: 1rem;
        }

        .generate-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">
            <span>⚡</span> Gamma
        </div>

        <a href="/" style="text-decoration: none;">
            <button class="btn-new">
                <span>+</span> Create new AI
            </button>
        </a>

        <a href="/" class="nav-item">
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
            <h1>Generate</h1>
            <div class="subtitle">What would you like to create today?</div>

            <div class="input-wrapper">
                <div class="type-selector">
                    <div class="type-btn">
                        <span>📂</span> Presentation
                    </div>
                </div>

                <div class="options-bar">
                    <select>
                        <option>1 card</option>
                        <option>2 cards</option>
                        <option>3 cards</option>
                        <option>4 cards</option>
                        <option>5 cards</option>
                        <option>6 cards</option>
                        <option>7 cards</option>
                        <option selected>8 cards</option>
                        <option>9 cards</option>
                        <option>10 cards</option>
                    </select>

                    <select>
                        <option>Default Ratio</option>
                        <option>16:9 (Traditional)</option>
                        <option>4:3 (Tall)</option>
                    </select>

                    <select>
                        <option>English (US)</option>
                        <option>Spanish</option>
                        <option>French</option>
                        <option>German</option>
                        <option>Japanese</option>
                    </select>
                </div>

                <textarea placeholder="Describe what you'd like to make, e.g. 'How to make sushi, a guide for beginners'"></textarea>

                <div class="action-bar">
                    <button class="generate-btn">
                        <span>✨</span> Generate outline
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
