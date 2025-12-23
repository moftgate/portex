<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title') - Portex</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            -webkit-font-smoothing: antialiased;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Nav */
        nav {
            padding: 24px 0;
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            z-index: 50;
            border-bottom: 1px solid var(--border);
        }

        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: 800;
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links a {
            color: var(--slate);
            text-decoration: none;
            margin-left: 32px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--orange);
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 220px;
            box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 12px;
            z-index: 100;
            border: 1px solid var(--border);
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Bridge the gap for smoother hover */
        .dropdown::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            height: 10px;
            display: none;
        }

        .dropdown:hover::after,
        .dropdown.active::after {
            display: block;
        }

        .dropdown.active .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            margin: 0 !important;
            padding: 10px 16px;
            display: block;
            border-radius: 8px;
            font-size: 14px;
            color: var(--slate);
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background: var(--light);
            color: var(--orange);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--orange);
            color: white;
        }

        .btn-outline {
            border: 1px solid var(--border);
            color: var(--dark);
        }

        .hero {
            padding: 100px 0 60px;
            text-align: center;
        }

        .hero h1 {
            font-size: 56px;
            line-height: 1.1;
            margin-bottom: 24px;
        }

        .hero p {
            font-size: 20px;
            color: var(--slate);
            max-width: 700px;
            margin: 0 auto 40px;
        }

        .section {
            padding: 80px 0;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
            margin-top: 48px;
        }

        .feature-card {
            padding: 32px;
            background: var(--light);
            border-radius: 20px;
            border: 1px solid var(--border);
        }

        .footer {
            padding: 80px 0 40px;
            background: var(--light);
            border-top: 1px solid var(--border);
            margin-top: 80px;
        }

        .badge {
            display: inline-block;
            background: var(--orange-dim);
            color: var(--orange);
            font-weight: 600;
            font-size: 14px;
            padding: 6px 16px;
            border-radius: 100px;
            margin-bottom: 24px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
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
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                <rect x="2" y="2" width="28" height="28" rx="8" fill="#0F172A"/>
                <circle cx="16" cy="16" r="6" stroke="#FF6B2C" stroke-width="3"/>
                <path d="M22 16H27" stroke="#FF6B2C" stroke-width="3" stroke-linecap="round"/>
            </svg>
            Portex
        </a>
        <div class="nav-links">
            <a href="/#guide">Guide</a>
            <div class="dropdown">
                <a href="#" onclick="return false;">Solutions ▾</a>
                <div class="dropdown-content">
                    <a href="{{ route('solutions.webhooks') }}">Webhook Debugging</a>
                    <a href="{{ route('solutions.mobile') }}">Mobile Development</a>
                    <a href="{{ route('solutions.static') }}">Static Hosting</a>
                    <a href="{{ route('solutions.ci-cd') }}">CI/CD Integration</a>
                </div>
            </div>
            <a href="/#features">Features</a>
            <a href="{{ route('docs.index') }}">Docs</a>
            <a href="https://github.com/orgs/portex-space/repositories" target="_blank">GitHub</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary"
                   style="margin-left: 32px; color: white;">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary" style="margin-left: 32px; color: white;">Sign
                    in</a>
            @endauth
        </div>
    </div>
</nav>

@yield('content')

<footer class="footer">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <p style="color: var(--slate); font-size: 14px;">© 2024 Portex Space. Open source and secure.</p>
            <div class="nav-links" style="margin: 0;">
                <a href="https://github.com/orgs/portex-space/repositories" target="_blank">GitHub</a>
                <a href="{{ route('docs.index') }}">Documentation</a>
            </div>
        </div>
    </div>
</footer>
<script>
    // Dropdown Click Handle
    document.querySelector('.dropdown > a').addEventListener('click', function (e) {
        e.preventDefault();
        this.parentElement.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) {
            const dropdown = document.querySelector('.dropdown');
            if (dropdown) dropdown.classList.remove('active');
        }
    });
</script>
</body>

</html>
