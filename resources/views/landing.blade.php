<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- Primary Meta Tags -->
    <title>Portex - The Developer-First Localhost Tunneling Tool</title>
    <meta name="title" content="Portex - The Developer-First Localhost Tunneling Tool">
    <meta name="description"
        content="The fastest way to expose localhost to the internet. Secure ngrok alternative with custom subdomains, traffic inspection, and static file sharing. Open source and developer-friendly.">
    <meta name="keywords"
        content="ngrok alternative, localhost tunnel, port forwarding, expose localhost, webhook debugging, static site hosting, secure tunnel, dev tools, open source tunnel">
    <meta name="author" content="Portex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="canonical" href="https://portex.space">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://portex.space/">
    <meta property="og:title" content="Portex - Your Localhost, Online in Seconds">
    <meta property="og:description"
        content="Securely expose your local server to the internet with one command. The modern, fast, and beautiful alternative to ngrok.">
    <meta property="og:image" content="https://portex.space/og-image.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://portex.space/">
    <meta property="twitter:title" content="Portex - Your Localhost, Online in Seconds">
    <meta property="twitter:description"
        content="Securely expose your local server to the internet with one command. The modern, fast, and beautiful alternative to ngrok.">
    <meta property="twitter:image" content="https://portex.space/og-image.png">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "SoftwareApplication",
      "name": "Portex",
      "operatingSystem": "Windows, macOS, Linux",
      "applicationCategory": "DeveloperApplication",
      "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
      },
      "description": "A secure tunneling tool to expose localhost to the internet. Features include custom subdomains, traffic inspection, and static file sharing."
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@500;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            /* Palette matched to provided guidelines */
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
        }

        code,
        pre,
        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Nav */
        nav {
            padding: 24px 0;
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            z-index: 50;
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s;
        }

        nav.scrolled {
            border-color: var(--border);
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
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        .logo svg {
            flex-shrink: 0;
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
            box-shadow: 0 4px 12px rgba(255, 107, 44, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(255, 107, 44, 0.3);
        }

        .btn-outline {
            border: 1px solid var(--border);
            color: var(--dark);
        }

        .btn-outline:hover {
            border-color: var(--dark);
            background: var(--light);
        }

        /* Hero */
        .hero {
            padding: 180px 0 100px;
            text-align: center;
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

        .hero h1 {
            font-size: 64px;
            line-height: 1.1;
            letter-spacing: -0.02em;
            margin-bottom: 24px;
            background: linear-gradient(180deg, var(--dark) 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 20px;
            color: var(--slate);
            max-width: 600px;
            margin: 0 auto 40px;
        }

        /* Terminal Preview */
        .terminal-window {
            background: #1E293B;
            border-radius: 16px;
            box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.2);
            margin: 60px auto 0;
            max-width: 860px;
            overflow: hidden;
            border: 1px solid #334155;
            text-align: left;
        }

        .terminal-header {
            background: #0F172A;
            padding: 12px 20px;
            display: flex;
            gap: 8px;
            border-bottom: 1px solid #334155;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .dot-red {
            background: #FF5F57;
        }

        .dot-yellow {
            background: #FEBC2E;
        }

        .dot-green {
            background: #28C840;
        }

        .terminal-body {
            padding: 32px;
            color: #E2E8F0;
            font-size: 14px;
        }

        .cmd {
            color: var(--white);
            margin-bottom: 16px;
            display: block;
        }

        .cmd-prompt {
            color: #28C840;
            margin-right: 8px;
        }

        .out-dim {
            color: #64748B;
        }

        .out-hl {
            color: var(--orange);
            font-weight: bold;
        }

        .out-link {
            color: var(--white);
            text-decoration: underline;
            text-decoration-color: #64748B;
        }

        /* Guide Section - The Focus */
        .guide-section {
            padding: 120px 0;
            background: var(--light);
        }

        .section-title {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-title h2 {
            font-size: 42px;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .section-title p {
            color: var(--slate);
            font-size: 18px;
        }

        .guide-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 60px;
            max-width: 900px;
            margin: 0 auto;
        }

        .guide-step {
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: 32px;
        }

        .step-num {
            width: 64px;
            height: 64px;
            background: var(--white);
            border: 2px solid var(--border);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: var(--orange);
            font-family: 'Outfit', sans-serif;
        }

        .step-content {
            padding-top: 10px;
        }

        .step-content h3 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .step-content p {
            color: var(--slate);
            margin-bottom: 24px;
            font-size: 16px;
        }

        .command-block {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .cmd-row {
            padding: 16px 20px;
            border-bottom: 1px solid var(--light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cmd-row:last-child {
            border-bottom: none;
        }

        .cmd-row.optional {
            background: #FAFAFA;
        }

        .cmd-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            color: var(--dark);
        }

        .cmd-code span.keyword {
            color: var(--blue);
        }

        .cmd-code span.flag {
            color: var(--orange);
        }

        .cmd-tag {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--slate);
            letter-spacing: 0.05em;
            background: #F1F5F9;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .cmd-tag.opt {
            color: #B45309;
            background: #FFFBEB;
        }

        .manual-dl-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            text-decoration: none;
            color: var(--dark);
            font-size: 13px;
            font-weight: 500;
            background: var(--white);
            transition: all 0.2s;
        }

        .manual-dl-btn:hover {
            border-color: var(--orange);
            background: var(--light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .manual-dl-btn svg {
            color: var(--slate);
            transition: color 0.2s;
        }

        .manual-dl-btn:hover svg {
            color: var(--orange);
        }

        /* Features */
        .features-section {
            padding: 120px 0;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 60px;
        }

        .feature-box {
            padding: 32px;
            border-radius: 20px;
            border: 1px solid var(--border);
            transition: all 0.3s;
        }

        .feature-box:hover {
            border-color: var(--orange);
            box-shadow: 0 12px 32px -8px rgba(255, 107, 44, 0.1);
            transform: translateY(-5px);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            background: var(--orange-dim);
            color: var(--orange);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .feature-box h4 {
            font-size: 20px;
            margin-bottom: 12px;
        }

        .feature-box p {
            color: var(--slate);
            font-size: 15px;
        }

        /* Use Cases / Visual Sections */
        .use-case-section {
            padding: 120px 0;
            overflow: hidden;
        }

        .use-case-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 80px;
            margin-bottom: 120px;
        }

        .use-case-row.reverse {
            direction: rtl;
        }

        .use-case-row.reverse>* {
            direction: ltr;
        }

        .use-case-content h3 {
            font-size: 36px;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }

        .use-case-content p {
            font-size: 18px;
            color: var(--slate);
            margin-bottom: 32px;
        }

        .use-case-visual img {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 32px 64px -16px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border);
        }

        /* Pricing Section */
        .pricing-section {
            padding: 120px 0;
            background: var(--light);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 32px;
            max-width: 900px;
            margin: 60px auto 0;
        }

        .pricing-card {
            background: white;
            padding: 48px;
            border-radius: 24px;
            border: 1px solid var(--border);
            text-align: center;
            transition: all 0.3s;
        }

        .pricing-card.popular {
            border-color: var(--orange);
            box-shadow: 0 24px 48px -12px rgba(255, 107, 44, 0.15);
            transform: scale(1.05);
        }

        .price {
            font-size: 48px;
            font-weight: 800;
            margin: 24px 0;
            font-family: 'Outfit', sans-serif;
        }

        .price span {
            font-size: 16px;
            color: var(--slate);
            font-weight: 400;
        }

        .pricing-features {
            list-style: none;
            text-align: left;
            margin: 40px 0;
        }

        .pricing-features li {
            margin-bottom: 16px;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pricing-features svg {
            color: var(--orange);
        }

        /* FAQ */
        .faq-section {
            padding: 120px 0;
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            margin-bottom: 32px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 32px;
        }

        .faq-item h4 {
            font-size: 20px;
            margin-bottom: 12px;
        }

        .faq-item p {
            color: var(--slate);
            font-size: 16px;
        }

        @media (max-width: 900px) {
            .use-case-row {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .pricing-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Footer */
        footer {
            padding: 80px 0 40px;
            background: #F8FAFC;
            border-top: 1px solid var(--border);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
        }

        .footer-brand {
            color: var(--slate);
            font-size: 14px;
            max-width: 300px;
        }

        .footer-logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 16px;
            display: block;
        }

        .footer-links h5 {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--dark);
            margin-bottom: 20px;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: var(--slate);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--orange);
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 40px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .guide-step {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .step-num {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }
        }
    </style>

    <script defer src="https://cloud.umami.is/script.js" data-website-id="269e123b-a36d-485b-bec5-378554421aa9"></script>
</head>

<body>

    <nav>
        <div class="container nav-inner">
            <a href="/" class="logo">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="28" height="28" rx="8" fill="#0F172A" />
                    <circle cx="16" cy="16" r="6" stroke="#FF6B2C" stroke-width="3" />
                    <path d="M22 16H27" stroke="#FF6B2C" stroke-width="3" stroke-linecap="round" />
                </svg>
                Portex
            </a>
            <div class="nav-links">
                <a href="#guide">Guide</a>
                <a href="#features">Features</a>
                <a href="{{ route('solutions.webhooks') }}">Webhooks</a>
                <a href="{{ route('solutions.mobile') }}">Mobile</a>
                <a href="#pricing">Pricing</a>
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

    <header class="hero">
        <div class="container">
            <div class="badge">New: Static Directory Sharing</div>
            <h1>Your Localhost,<br>Online in Seconds.</h1>
            <p>The developer-first tunnel service. Expose ports, share files, and debug webhooks with a single command.
            </p>

            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="#guide" class="btn btn-primary">Get Started</a>
                <a href="{{ route('docs.index') }}" class="btn btn-outline">Documentation</a>
            </div>

            <div class="terminal-window">
                <div class="terminal-header">
                    <div class="dot dot-red"></div>
                    <div class="dot dot-yellow"></div>
                    <div class="dot dot-green"></div>
                </div>
                <div class="terminal-body">
                    <div class="cmd">
                        <span class="cmd-prompt">$</span> portex start --port 3000 <span class="out-dim">--subdomain
                            myapp</span>
                    </div>
                    <div class="out-dim">────────────────────────────────────────────────────────────</div>
                    <div class="out-row">
                        <span class="out-hl">PORTEX</span> <span class="out-dim">1.0.0</span>
                    </div>
                    <br>
                    <div class="out-row">Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span
                            style="color: #28C840;">Online</span></div>
                    <div class="out-row">Account &nbsp;&nbsp;&nbsp;&nbsp;<span class="out-dim">pk_live_...</span></div>
                    <br>
                    <div class="out-hl">ACTIVE TUNNEL</div>
                    <div class="out-link">https://myapp.portex.space</div>
                    <div class="out-dim">↳ forwarding to http://localhost:3000</div>
                </div>
            </div>
        </div>
    </header>

    <section id="guide" class="guide-section">
        <div class="container">
            <div class="section-title">
                <h2>Three Steps to Live</h2>
                <p>Simple by default, powerful when you need it.</p>
            </div>

            <div class="guide-grid">
                <!-- Step 1 -->
                <div class="guide-step">
                    <div class="step-num">1</div>
                    <div class="step-content">
                        <h3>Install</h3>
                        <p>One command to rule them all. Works on macOS, Linux, and Windows.</p>
                        <div class="command-block">
                            <div class="cmd-row">
                                <div class="cmd-code">curl -fsSL https://portex.space/install.sh | bash</div>
                                <div class="cmd-tag">macOS / Linux</div>
                            </div>
                            <div class="cmd-row">
                                <div class="cmd-code">iwr https://portex.space/install.ps1 | iex</div>
                                <div class="cmd-tag" style="background: #0078D4; color: white;">Windows</div>
                            </div>
                        </div>
                        <div style="margin-top: 24px;">
                            <p
                                style="margin-bottom: 12px; font-size: 13px; font-weight: 600; color: var(--slate); letter-spacing: 0.02em;">
                                MANUAL DOWNLOADS</p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <a href="/bin/portex-darwin-amd64" download class="manual-dl-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.5 1.3-.03 2.52.87 3.3.87.76 0 2.21-1.09 3.72-.93 1.27.06 2.41.52 3.1 1.53-2.7 1.63-2.27 5.76.71 6.96-.06.4-.11.78-.19 1.18zm-6.18-13c.27-1.63 1.6-2.92 3.08-3 0 1.58-1.35 2.9-3.08 3z" />
                                    </svg>
                                    <span>macOS (Intel)</span>
                                </a>
                                <a href="/bin/portex-darwin-arm64" download class="manual-dl-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.5 1.3-.03 2.52.87 3.3.87.76 0 2.21-1.09 3.72-.93 1.27.06 2.41.52 3.1 1.53-2.7 1.63-2.27 5.76.71 6.96-.06.4-.11.78-.19 1.18zm-6.18-13c.27-1.63 1.6-2.92 3.08-3 0 1.58-1.35 2.9-3.08 3z" />
                                    </svg>
                                    <span>macOS (Silicon)</span>
                                </a>
                                <a href="/bin/portex-linux-amd64" download class="manual-dl-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M17.6 9.48l1.84-3.18c.16-.31.04-.69-.26-.85-.29-.15-.65-.06-.83.25L16.36 9c-1.28-.56-2.73-.89-4.29-.89-1.56 0-3.01.33-4.29.89L5.8 5.7c-.18-.31-.54-.4-.83-.25-.31.16-.42.54-.26.85l1.84 3.18c-2.34 1.52-3.88 4-3.88 6.82h18.8c0-2.82-1.54-5.3-3.87-6.82zm-9.37 5.25c-.75 0-1.35-.6-1.35-1.35s.6-1.35 1.35-1.35 1.35.6 1.35 1.35-.6 1.35-1.35 1.35zm7.68 0c-.75 0-1.35-.6-1.35-1.35s.6-1.35 1.35-1.35 1.35.6 1.35 1.35-.6 1.35-1.35 1.35z" />
                                    </svg>
                                    <span>Linux (x64)</span>
                                </a>
                                <a href="/bin/portex-windows-amd64.exe" download class="manual-dl-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M3 5.48L10.05 4.5v6.86H3V5.48zm0 13.04V12.28h7.05v6.84L3 18.52zM11.08 4.35L21 3v8.36h-9.92V4.35zm0 15.3l9.92-1.35V12.28h-9.92v7.37z" />
                                    </svg>
                                    <span>Windows</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="guide-step">
                    <div class="step-num">2</div>
                    <div class="step-content">
                        <h3>Go Live</h3>
                        <p>Start a tunnel instantly. No account required to get started.</p>

                        <div class="command-block">
                            <!-- Basic usage -->
                            <div class="cmd-row">
                                <div class="cmd-code">portex start <span class="flag">--port</span> 3000</div>
                                <div class="cmd-tag">Basic</div>
                            </div>
                            <!-- Advanced usage -->
                            <div class="cmd-row optional">
                                <div class="cmd-code">
                                    <span style="opacity: 0.5">...</span>
                                    <span class="flag">--subdomain</span> myapp
                                    <span class="flag">--pin</span> 1234
                                </div>
                                <div class="cmd-tag opt">Optional</div>
                            </div>
                        </div>

                        <p style="margin-top: 24px; margin-bottom: 24px;">Or share a directory:</p>

                        <div class="command-block">
                            <div class="cmd-row">
                                <div class="cmd-code">portex share ./myfiles</div>
                                <div class="cmd-tag">Basic</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="guide-step">
                    <div class="step-num">3</div>
                    <div class="step-content">
                        <h3>Track & Debug</h3>
                        <p>Access the dashboard for traffic inspection and analytics. (Optional)</p>
                        <div class="command-block">
                            <div class="cmd-row">
                                <div class="cmd-code">portex login</div>
                                <div class="cmd-tag">Browser Auth</div>
                            </div>
                        </div>
                        <p style="font-size: 14px; color: var(--slate);">Automagically links your active tunnels to
                            your
                            account.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Case: Webhooks -->
    <section class="use-case-section">
        <div class="container">
            <div class="use-case-row">
                <div class="use-case-content">
                    <div class="badge">Developers Love This</div>
                    <h3>Debug Webhooks in Real‑Time</h3>
                    <p>Stop deploying to staging just to see a Stripe or GitHub webhook payload. Expose your local port
                        and watch internal traffic flow live in your dashboard.</p>
                    <ul class="pricing-features" style="margin-top: 0;">
                        <li><svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Inspect Full Request Payloads</li>
                        <li><svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Replay Webhooks with One Click</li>
                        <li><svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Response Time Monitoring</li>
                    </ul>
                    <a href="{{ route('solutions.webhooks') }}" class="btn btn-outline">Learn More about Webhooks</a>
                </div>
                <div class="use-case-visual">
                    <img src="/landing_webhooks.png" alt="Webhook Debugging Visual">
                </div>
            </div>

            <!-- Use Case: Mobile -->
            <div class="use-case-row reverse">
                <div class="use-case-content">
                    <div class="badge">Mobile & IoT</div>
                    <h3>Test on Physical Devices Instantly</h3>
                    <p>Scanning a QR code is all it takes to connect your mobile device to your local API. No more
                        fiddling with local IP addresses or WiFi settings.</p>
                    <ul class="pricing-features" style="margin-top: 0;">
                        <li><svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Automatic QR Code Generation</li>
                        <li><svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Cross-Device Testing over HTTPS</li>
                        <li><svg width="20" height="20" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Works through Firewalls & Corporate VPNs</li>
                    </ul>
                    <a href="{{ route('solutions.mobile') }}" class="btn btn-primary">Learn More about Mobile Dev</a>
                </div>
                <div class="use-case-visual">
                    <img src="/landing_mobile.png" alt="Mobile Testing Visual">
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Everything you need</h2>
                <p>From local development to client demos, we've got you covered.</p>
            </div>
            <div class="features-grid">
                <!-- 1. Secure -->
                <div class="feature-box">
                    <div class="icon-box">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0110 0v4"></path>
                        </svg>
                    </div>
                    <h4>Secure by Design</h4>
                    <p>Tunnels are encrypted end-to-end. Add a PIN to any tunnel to prevent unauthorized access during
                        demos.</p>
                </div>

                <!-- 2. Inspect -->
                <div class="feature-box">
                    <div class="icon-box">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                        </svg>
                    </div>
                    <h4>Traffic Inspector</h4>
                    <p>Real-time request logging. See headers, payloads, and responses. Replay requests with one click.
                    </p>
                </div>

                <!-- 3. Static -->
                <div class="feature-box">
                    <div class="icon-box">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"></path>
                            <path d="M13 2v7h7"></path>
                        </svg>
                    </div>
                    <h4>Static Hosting</h4>
                    <p>Don't have a server running? Just point Portex to a folder and we'll host it for you instantly.
                    </p>
                </div>

                <!-- 4. Subdomains -->
                <div class="feature-box">
                    <div class="icon-box">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path
                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                            </path>
                        </svg>
                    </div>
                    <h4>Custom Subdomains</h4>
                    <p>Reserve your own subdomains like <code>myapp</code> or <code>api-dev</code>. No more random
                        strings to remember.</p>
                </div>

                <!-- 5. QR Mobile -->
                <div class="feature-box">
                    <div class="icon-box">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                            <line x1="12" y1="18" x2="12.01" y2="18"></line>
                        </svg>
                    </div>
                    <h4>Mobile Testing</h4>
                    <p>We generate a QR code in your terminal. Scan it to instantly test your localhost on iOS/Android.
                    </p>
                </div>

                <!-- 6. Persistent -->
                <div class="feature-box">
                    <div class="icon-box">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h4>Persistent Connections</h4>
                    <p>Auto-reconnect logic keeps your tunnel alive even if your WiFi drops. Set it and forget it.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Row -->
    <section id="pricing" class="pricing-section">
        <div class="container">
            <div class="section-title">
                <h2>Simple, Dev-Friendly Pricing</h2>
                <p>Start for free, upgrade when you need custom domains and team features.</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3>Free Beta</h3>
                    <div class="price">$0<span>/mo</span></div>
                    <ul class="pricing-features">
                        <li><svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Unlimited Tunnels</li>
                        <li><svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Custom Subdomains</li>
                        <li><svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 6L9 17L4 12" />
                            </svg> 50 Recent Request Logs</li>
                        <li><svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Static File Sharing</li>
                    </ul>
                    <a href="#guide" class="btn btn-outline" style="width: 100%;">Get Started</a>
                </div>
                <div class="pricing-card popular">
                    <h3>Pro Plan (Coming Soon)</h3>
                    <div class="price">$7<span>/mo</span></div>
                    <ul class="pricing-features">
                        <li><svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Everything in Free</li>
                        <li><svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Reserved Subdomains</li>
                        <li><svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Unlimited History</li>
                        <li><svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 6L9 17L4 12" />
                            </svg> Priority Support</li>
                    </ul>
                    <a href="#" class="btn btn-primary" style="width: 100%;">Soon</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section">
        <div class="container">
            <div class="section-title" style="margin-bottom: 60px;">
                <h2>Common Questions</h2>
            </div>
            <div class="faq-item">
                <h4>How is Portex different from ngrok?</h4>
                <p>Portex is built with the modern stack (Go + Laravel) and offers a more developer-centric experience.
                    We include features like static file sharing and PIN protection out of the box, with a focus on open
                    source transparency.</p>
            </div>
            <div class="faq-item">
                <h4>Is my local server exposed securely?</h4>
                <p>Yes. All traffic is encrypted with TLS. You can also add a 4-digit PIN to your tunnel (<code>--pin
                        1234</code>) to ensure only authorized visitors can access your service.</p>
            </div>
            <div class="faq-item">
                <h4>Can I use it for static builds?</h4>
                <p>Definitely! Use <code>portex share ./dist</code> to instantly host and tunnel any directory without
                    needing to configure Nginx, Apache, or a Node.js server.</p>
            </div>
            <div class="faq-item">
                <h4>Do I need an account to start?</h4>
                <p>No account is required for basic usage. You can run one tunnel as a guest. Logging in unlocks
                    dashboard tracking, persistent subdomains, and analytics.</p>
            </div>
        </div>
    </section>



    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="#" class="logo footer-logo">
                        <svg width="24" height="24" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="2" width="28" height="28" rx="8" fill="#0F172A" />
                            <circle cx="16" cy="16" r="6" stroke="#FF6B2C" stroke-width="3" />
                            <path d="M22 16H27" stroke="#FF6B2C" stroke-width="3" stroke-linecap="round" />
                        </svg>
                        Portex
                    </a>
                    <p class="footer-brand">Made with precision for developers who care about their tools.</p>
                </div>
                <div class="footer-links">
                    <h5>Product</h5>
                    <ul>
                        <li><a href="#guide">Download</a></li>
                        <li><a href="{{ route('docs.index') }}">Documentation</a></li>
                        <li><a href="https://github.com/orgs/portex-space/repositories" target="_blank">GitHub</a>
                        </li>
                        <li><a href="#pricing">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h5>Legal</h5>
                    <ul>
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Terms</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 20) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>

</body>

</html>
