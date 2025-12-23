<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portex - High-Speed Secure Tunneling | ngrok Alternative</title>
    <meta name="description"
        content="Securely expose your local services to the internet with Portex. High-performance, PIN-protected tunnels with live traffic inspection.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            /* Netbird Palette */
            --primary: #FF6B2C;
            --primary-soft: rgba(255, 107, 44, 0.1);
            --secondary: #2D5BFF;
            --accent: #00D4AA;

            --bg: #FFFFFF;
            --bg-muted: #F8F9FA;
            --border: #E5E7EB;

            --text: #0F172A;
            --text-muted: #64748B;

            --radius-lg: 16px;
            --radius-xl: 24px;

            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: var(--bg);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        h1,
        h2,
        h3,
        .brand {
            font-family: 'Outfit', sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* --- Navigation --- */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            z-index: 100;
        }

        .nav-content {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            text-decoration: none;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transform: rotate(-5deg);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-lg);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(255, 107, 44, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            background: var(--bg-muted);
        }

        /* --- Hero --- */
        .hero {
            padding: 160px 0 100px;
            text-align: center;
            background: radial-gradient(circle at 50% -20%, rgba(255, 107, 44, 0.08) 0%, transparent 60%);
        }

        .pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: var(--primary-soft);
            color: var(--primary);
            border-radius: 99px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1;
            margin-bottom: 1.5rem;
            color: var(--text);
        }

        .hero h1 span {
            color: var(--primary);
            position: relative;
        }

        .hero-desc {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 650px;
            margin: 0 auto 2.5rem;
        }

        /* --- Terminal --- */
        .preview {
            max-width: 900px;
            margin: 4rem auto 0;
            position: relative;
        }

        .terminal {
            background: #1e1e1e;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .terminal-header {
            padding: 12px 18px;
            background: #2d2d2d;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .dot-r {
            background: #ff5f56;
        }

        .dot-y {
            background: #ffbd2e;
        }

        .dot-g {
            background: #27c93f;
        }

        .terminal-body {
            padding: 24px;
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
            font-size: 13px;
        }

        .t-line {
            margin-bottom: 8px;
            color: #d1d1d1;
        }

        .t-orange {
            color: var(--primary);
            font-weight: bold;
        }

        .t-green {
            color: var(--accent);
        }

        .t-muted {
            color: #666;
        }

        .t-white {
            color: #fff;
            font-weight: bold;
        }

        /* --- Features --- */
        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-header h2 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .features {
            padding: 100px 0;
            background: white;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .feature-card {
            padding: 2.5rem;
            background: var(--bg-muted);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            background: white;
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .f-icon {
            width: 56px;
            height: 56px;
            background: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            color: var(--primary);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* --- Steps --- */
        .how {
            padding: 120px 0;
            background: var(--bg-muted);
        }

        .steps-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .step-item {
            display: flex;
            gap: 40px;
            margin-bottom: 4rem;
        }

        .step-count {
            width: 48px;
            height: 48px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            flex-shrink: 0;
            font-size: 1.25rem;
        }

        .step-item h3 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .step-item p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .code-box {
            background: white;
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-family: monospace;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .code-box span {
            color: var(--primary);
            font-weight: 600;
            margin-right: 8px;
        }

        /* --- Comparison --- */
        .table-wrap {
            background: white;
            border-radius: var(--radius-xl);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--bg-muted);
            padding: 24px;
            text-align: left;
            border-bottom: 2px solid var(--border);
            font-weight: 800;
            font-size: 1.1rem;
        }

        td {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            font-weight: 500;
        }

        .check {
            color: var(--accent);
            font-weight: 800;
        }

        .cross {
            color: #e5e7eb;
        }

        /* --- Footer --- */
        footer {
            padding: 100px 0 50px;
            border-top: 1px solid var(--border);
            background: white;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
        }

        .footer-logo-area p {
            margin-top: 20px;
            color: var(--text-muted);
            max-width: 250px;
        }

        .footer-col h4 {
            margin-bottom: 25px;
            font-size: 1.1rem;
        }

        .footer-links-list {
            list-style: none;
        }

        .footer-links-list li {
            margin-bottom: 15px;
        }

        .footer-links-list a {
            text-decoration: none;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .footer-links-list a:hover {
            color: var(--primary);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            padding-top: 30px;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        @media (max-width: 1024px) {
            .hero h1 {
                font-size: 3.5rem;
            }

            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.75rem;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .hero-desc {
                font-size: 1.1rem;
            }

            .nav-links {
                display: none;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate {
            animation: fadeInUp 0.8s ease forwards;
            opacity: 0;
        }

        .delay-1 {
            animation-delay: 0.2s;
        }

        .delay-2 {
            animation-delay: 0.4s;
        }

        .delay-3 {
            animation-delay: 0.6s;
        }
    </style>
</head>

<body>
    <nav>
        <div class="container nav-content">
            <a href="/" class="brand">
                <div class="brand-icon">P</div>
                Portex
            </a>
            <div class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="#how-it-works" class="nav-link">Guide</a>
                <a href="#comparison" class="nav-link">Comparison</a>
            </div>
            <div class="auth-btns">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Sign In</a>
                    <a href="{{ route('login') }}" class="btn btn-primary">Start Tunneling</a>
                @endauth
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <div class="pill-badge animate">Ngrok Alternative for Pro Developers</div>
            <h1 class="animate delay-1">Expose local services <span>instantly</span><br>to the internet.</h1>
            <p class="hero-desc animate delay-2">Create secure tunnels for your web apps, APIs, and webhooks. No
                firewall setup, no complex configs. Just one command to go live.</p>
            <div class="hero-actions animate delay-3">
                <a href="#how-it-works" class="btn btn-primary btn-lg">Start Tunneling Now</a>
                <a href="#solutions" class="btn btn-outline btn-lg">Explore Solutions</a>
            </div>

            <div class="preview animate delay-3">
                <div class="terminal">
                    <div class="terminal-header">
                        <div class="dot dot-r"></div>
                        <div class="dot dot-y"></div>
                        <div class="dot dot-g"></div>
                    </div>
                    <div class="terminal-body">
                        <div class="t-line">$ portex start --port 8000 --subdomain myapp --pin 1234</div>
                        <div class="t-line t-muted">────────────────────────────────────────────────────────────</div>
                        <div class="t-line"><span class="t-orange"> PORTEX</span> <span class="t-muted">1.0.0</span>
                        </div>
                        <div class="t-line t-muted"> ────────────────────────────────────────────────────────────</div>
                        <div class="t-line"> Status <span class="t-green">Online</span></div>
                        <div class="t-line"> Account <span class="t-muted">pk_aras_prod_...</span></div>
                        <div class="t-line"> Usage <span class="t-muted">1h 45m / 3h 0m (58.3%)</span></div>
                        <div class="t-line"></div>
                        <div class="t-line t-orange"> ACTIVE TUNNEL</div>
                        <div class="t-line t-white"> https://myapp.portex.space</div>
                        <div class="t-line t-muted"> ↳ forwarding to http://localhost:8000</div>
                        <div class="t-line"></div>
                        <div class="t-line t-white"> SCAN FOR MOBILE</div>
                        <div class="t-line t-muted"> [ QR CODE GENERATED ]</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="features">
        <div class="container">
            <div class="section-header">
                <h2>Why Developers Love Portex</h2>
                <p class="hero-desc">Experience the next generation of secure local service sharing.</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="f-icon">🌐</div>
                    <h3>Webhook Testing</h3>
                    <p>Receive webhooks from Stripe, GitHub, or Shopify directly on your local dev environment.</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon">�</div>
                    <h3>PIN-Bound Security</h3>
                    <p>The only tunnel service with session-based PIN protection to keep your previews private.</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon">�</div>
                    <h3>Ephemeral Tunnels</h3>
                    <p>Start a tunnel for a quick demo and vanish instantly. No persistent footprints left behind.</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon">⚡</div>
                    <h3>High Performance</h3>
                    <p>Portex is optimized for low-latency TCP forwarding, ensuring your APIs respond instantly.</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon">�</div>
                    <h3>Static Hosting</h3>
                    <p>Need to share a build? Host any directory as a live site with a single `share` command.</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon">📊</div>
                    <h3>Deep Inspection</h3>
                    <p>Inspect every request and response in detail. Replay or copy requests as cURL with ease.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="how">
        <div class="container">
            <div class="section-header">
                <h2>Getting Started</h2>
                <p class="hero-desc">From terminal to global in seconds.</p>
            </div>

            <div class="steps-container">
                <div class="step-item">
                    <div class="step-count">1</div>
                    <div>
                        <h3>Download & Install</h3>
                        <p>Get the Portex binary for macOS, Linux, or Windows and add it to your PATH.</p>
                        <div class="code-box">
                            <div><span>$</span> curl -fsSL https://portex.space/install.sh | bash</div>
                        </div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-count">2</div>
                    <div>
                        <h3>Link Account</h3>
                        <p>Connect your agent to the dashboard using the browser or API keys.</p>
                        <div class="code-box">
                            <div><span>$</span> portex login</div>
                        </div>
                    </div>
                </div>

                <div class="step-item">
                    <div class="step-count">3</div>
                    <div>
                        <h3>Go Live</h3>
                        <p>Start a tunnel for a local port or share an entire directory.</p>
                        <div class="code-box" style="margin-bottom: 20px;">
                            <div><span>$</span> portex start -p 8080 --pin 1234</div>
                        </div>
                        <div class="code-box">
                            <div><span>$</span> portex share ./dist -s my-site</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="comparison" class="features" style="background: var(--bg-muted);">
        <div class="container">
            <div class="section-header">
                <h2>Better Than The Rest</h2>
                <p class="hero-desc">How Portex stacks up against traditional tunnel services.</p>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Portex</th>
                            <th>ngrok</th>
                            <th>Pinggy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Static File Sharing</td>
                            <td><span class="check">✓</span></td>
                            <td><span class="cross">✗</span></td>
                            <td><span class="check">✓</span></td>
                        </tr>
                        <tr>
                            <td>PIN Protection</td>
                            <td><span class="check">✓</span></td>
                            <td><span class="cross">✗</span></td>
                            <td><span class="cross">✗</span></td>
                        </tr>
                        <tr>
                            <td>QR Mobile Testing</td>
                            <td><span class="check">✓</span></td>
                            <td><span class="cross">✗</span></td>
                            <td><span class="check">✓</span></td>
                        </tr>
                        <tr>
                            <td>Copy as cURL</td>
                            <td><span class="check">✓</span></td>
                            <td><span class="check">✓</span></td>
                            <td><span class="cross">✗</span></td>
                        </tr>
                        <tr>
                            <td>Self-Hosted Ready</td>
                            <td><span class="check">✓</span></td>
                            <td><span class="cross">✗</span></td>
                            <td><span class="cross">✗</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-logo-area">
                    <a href="/" class="brand">
                        <div class="brand-icon">P</div>
                        Portex
                    </a>
                    <p>The secure way to share your local environment with the world.</p>
                </div>
                <div class="footer-col">
                    <h4>Product</h4>
                    <ul class="footer-links-list">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">Guide</a></li>
                        <li><a href="#comparison">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Security</h4>
                    <ul class="footer-links-list">
                        <li><a href="#">Ephemeral Sessions</a></li>
                        <li><a href="#">Traffic Privacy</a></li>
                        <li><a href="#">Reporting</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul class="footer-links-list">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Portex Space. All rights reserved.</p>
                <p>Designed with ❤️ for developers.</p>
            </div>
        </div>
    </footer>
</body>

</html>
