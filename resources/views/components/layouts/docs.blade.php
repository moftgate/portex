<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Documentation') - Portex</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@500;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --orange: #FF6B2C;
            --orange-dim: rgba(255, 107, 44, 0.08);
            --blue: #2D5BFF;
            --dark: #0F172A;
            --slate: #64748B;
            --light: #F8FAFC;
            --white: #FFFFFF;
            --border: #E2E8F0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--white);
            color: var(--dark);
            line-height: 1.6;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.01em;
        }

        code {
            font-family: 'JetBrains Mono', monospace;
            background: var(--light);
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        nav {
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            z-index: 100;
        }

        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .docs-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 64px;
            margin-top: 48px;
        }

        .sidebar {
            position: sticky;
            top: 100px;
            height: calc(100vh - 150px);
            overflow-y: auto;
        }

        .sidebar h4 {
            font-size: 13px;
            text-transform: uppercase;
            color: var(--slate);
            letter-spacing: 0.05em;
            margin-bottom: 16px;
            margin-top: 32px;
        }

        .sidebar h4:first-child {
            margin-top: 0;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 8px;
        }

        .sidebar a {
            text-decoration: none;
            color: var(--slate);
            font-size: 15px;
            transition: color 0.2s;
            display: block;
            padding: 4px 0;
        }

        .sidebar a:hover {
            color: var(--orange);
        }

        .sidebar a.active {
            color: var(--orange);
            font-weight: 600;
        }

        .content {
            max-width: 800px;
            padding-bottom: 100px;
        }

        .content h1 {
            font-size: 44px;
            margin-bottom: 24px;
            font-weight: 800;
        }

        .content h2 {
            font-size: 32px;
            margin-top: 56px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .content p {
            margin-bottom: 20px;
            font-size: 17px;
            color: #334155;
        }

        .code-block {
            background: var(--dark);
            color: #E2E8F0;
            padding: 24px;
            border-radius: 16px;
            margin: 24px 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            overflow-x: auto;
            border: 1px solid #1e293b;
        }

        .code-header {
            font-size: 11px;
            color: var(--slate);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
        }

        .doc-image {
            width: 100%;
            border-radius: 20px;
            margin: 32px 0;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
        }

        .callout {
            padding: 24px;
            background: var(--orange-dim);
            border-left: 4px solid var(--orange);
            border-radius: 0 16px 16px 0;
            margin: 40px 0;
        }

        .callout span {
            font-weight: 800;
            color: var(--orange);
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin: 32px 0;
        }

        .feature-card {
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 16px;
            transition: all 0.2s;
        }

        .feature-card:hover {
            border-color: var(--orange);
            transform: translateY(-2px);
        }

        .feature-card h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        footer {
            padding: 48px 0;
            border-top: 1px solid var(--border);
            margin-top: 80px;
            text-align: center;
            color: var(--slate);
            font-size: 14px;
        }

        @media (max-width: 900px) {
            .docs-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }
        }
    </style>

    <script defer src="https://cloud.umami.is/script.js"
            data-website-id="269e123b-a36d-485b-bec5-378554421aa9"></script>
</head>

<body>
<nav>
    <div class="container nav-inner">
        <a href="/" class="logo">
            <svg width="24" height="24" viewBox="0 0 32 32" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="2" width="28" height="28" rx="8" fill="#0F172A"/>
                <circle cx="16" cy="16" r="6" stroke="#FF6B2C" stroke-width="3"/>
                <path d="M22 16H27" stroke="#FF6B2C" stroke-width="3" stroke-linecap="round"/>
            </svg>
            Portex
        </a>
        <div class="nav-links" style="display: flex; gap: 20px; align-items: center;">
            <a href="https://github.com/orgs/portex-space/repositories" target="_blank"
               style="text-decoration: none; color: var(--slate); font-weight: 500; font-size: 14px;">GitHub</a>
            <a href="/"
               style="text-decoration: none; color: var(--slate); font-weight: 500; font-size: 14px;">Back to
                Home</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="docs-layout">
        <aside class="sidebar">
            <h4>Getting Started</h4>
            <ul>
                <li><a href="{{ route('docs.index') }}"
                       class="{{ request()->routeIs('docs.index') ? 'active' : '' }}">Introduction</a></li>
                <li><a href="{{ route('docs.installation') }}"
                       class="{{ request()->routeIs('docs.installation') ? 'active' : '' }}">Installation</a></li>
            </ul>

            <h4>Usage</h4>
            <ul>
                <li><a href="{{ route('docs.commands') }}"
                       class="{{ request()->routeIs('docs.commands') ? 'active' : '' }}">CLI Commands Guide</a>
                </li>
                <li><a href="{{ route('docs.security') }}"
                       class="{{ request()->routeIs('docs.security') ? 'active' : '' }}">Security & PINs</a></li>
                <li><a href="{{ route('docs.use-cases') }}"
                       class="{{ request()->routeIs('docs.use-cases') ? 'active' : '' }}">Use Cases</a></li>
            </ul>

            <h4>Advanced</h4>
            <ul>
                <li><a href="{{ route('docs.dashboard') }}"
                       class="{{ request()->routeIs('docs.dashboard') ? 'active' : '' }}">Monitoring & Logs</a>
                </li>
                <li><a href="{{ route('docs.architecture') }}"
                       class="{{ request()->routeIs('docs.architecture') ? 'active' : '' }}">How it Works</a>
                </li>

            </ul>

            <h4>Resources</h4>
            <ul>
                <li><a href="{{ route('docs.troubleshooting') }}"
                       class="{{ request()->routeIs('docs.troubleshooting') ? 'active' : '' }}">Troubleshooting</a>
                </li>
                {{-- <li><a href="{{ route('docs.pricing') }}"
                        class="{{ request()->routeIs('docs.pricing') ? 'active' : '' }}">Pricing</a>
                 </li>--}}
                <li><a href="{{ route('docs.changelog') }}"
                       class="{{ request()->routeIs('docs.changelog') ? 'active' : '' }}">Changelog</a>
                </li>
                <li><a href="{{ route('docs.roadmap') }}"
                       class="{{ request()->routeIs('docs.roadmap') ? 'active' : '' }}">Roadmap</a>
                </li>
            </ul>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>
</div>

<footer>
    <div class="container">
        <p>&copy; 2025 Portex Project. Open source and developer-focused.</p>
    </div>
</footer>
</body>

</html>
