<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portex - Secure Tunnel Service | ngrok Alternative</title>
    <meta name="description"
        content="Self-hosted tunnel service that exposes local services to the internet. Fast, secure, and easy to use. The perfect ngrok, bore, and localtunnel alternative.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        /* ===== CSS Variables (Netbird-inspired colors) ===== */
        :root {
            /* Primary Colors from Netbird */
            --color-primary: #FF6B2C;
            --color-secondary: #2D5BFF;
            --color-accent: #00D4AA;

            /* Gradient variations */
            --gradient-primary: linear-gradient(135deg, #FF6B2C 0%, #FF8F6B 100%);
            --gradient-secondary: linear-gradient(135deg, #2D5BFF 0%, #5B7FFF 100%);
            --gradient-accent: linear-gradient(135deg, #00D4AA 0%, #00F5C4 100%);

            /* Neutral colors */
            --color-bg: #FFFFFF;
            --color-bg-secondary: #F8F9FA;
            --color-bg-tertiary: #F1F3F5;
            --color-text: #1A1A1A;
            --color-text-secondary: #6B7280;
            --color-text-muted: #9CA3AF;
            --color-border: #E5E7EB;

            /* Spacing */
            --spacing-xs: 0.5rem;
            --spacing-sm: 1rem;
            --spacing-md: 1.5rem;
            --spacing-lg: 2rem;
            --spacing-xl: 3rem;
            --spacing-2xl: 4rem;
            --spacing-3xl: 6rem;

            /* Typography */
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 2rem;
            --font-size-4xl: 2.5rem;
            --font-size-5xl: 3rem;

            /* Border radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);

            /* Transitions */
            --transition-fast: 150ms ease;
            --transition-base: 250ms ease;
            --transition-slow: 350ms ease;
        }

        /* ===== Reset & Base ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-family);
            font-size: var(--font-size-base);
            line-height: 1.6;
            color: var(--color-text);
            background-color: var(--color-bg);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ===== Container ===== */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
        }

        /* ===== Navigation ===== */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--color-border);
            transition: all var(--transition-base);
        }

        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--color-text);
            text-decoration: none;
        }

        .nav-logo-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }

        .nav-link {
            color: var(--color-text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: var(--font-size-sm);
            transition: color var(--transition-fast);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-md);
        }

        .nav-link:hover {
            color: var(--color-primary);
        }

        /* ===== Buttons ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            padding: 0.625rem 1.25rem;
            font-size: var(--font-size-sm);
            font-weight: 600;
            text-decoration: none;
            border-radius: var(--radius-lg);
            transition: all var(--transition-base);
            cursor: pointer;
            border: none;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 107, 44, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 44, 0.35);
        }

        .btn-secondary {
            background: var(--color-bg-secondary);
            color: var(--color-text);
            border: 1px solid var(--color-border);
        }

        .btn-secondary:hover {
            background: var(--color-bg-tertiary);
            border-color: var(--color-text-muted);
        }

        .btn-large {
            padding: 0.875rem 1.75rem;
            font-size: var(--font-size-base);
        }

        /* ===== Hero Section ===== */
        .hero {
            padding-top: 140px;
            padding-bottom: var(--spacing-3xl);
            background: linear-gradient(180deg, #FFFFFF 0%, #F8F9FA 100%);
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            padding: 0.5rem 1rem;
            background: var(--color-bg-secondary);
            border: 1px solid var(--color-border);
            border-radius: 999px;
            font-size: var(--font-size-sm);
            color: var(--color-text-secondary);
            margin-bottom: var(--spacing-lg);
            animation: fadeInUp 0.6s ease;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--gradient-accent);
            animation: pulse 2s ease infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .hero-title {
            font-size: var(--font-size-5xl);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: var(--spacing-md);
            color: var(--color-text);
            animation: fadeInUp 0.6s ease 0.1s backwards;
        }

        .gradient-text {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            font-size: var(--font-size-xl);
            color: var(--color-text-secondary);
            margin-bottom: var(--spacing-xl);
            line-height: 1.6;
            animation: fadeInUp 0.6s ease 0.2s backwards;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-2xl);
            animation: fadeInUp 0.6s ease 0.3s backwards;
        }

        .hero-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-2xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid var(--color-border);
            animation: fadeInUp 0.6s ease 0.4s backwards;
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: var(--font-size-3xl);
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: var(--spacing-xs);
        }

        .stat-label {
            font-size: var(--font-size-sm);
            color: var(--color-text-muted);
        }

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

        /* ===== Hero Visual (Terminal) ===== */
        .hero-visual {
            max-width: 900px;
            margin: var(--spacing-3xl) auto 0;
            animation: fadeInUp 0.8s ease 0.5s backwards;
        }

        .terminal-window {
            background: #1A1A1A;
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-xl);
        }

        .terminal-header {
            display: flex;
            align-items: center;
            padding: var(--spacing-md);
            background: #2A2A2A;
            border-bottom: 1px solid #3A3A3A;
        }

        .terminal-buttons {
            display: flex;
            gap: var(--spacing-xs);
        }

        .terminal-button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .terminal-button-red {
            background: #FF5F56;
        }

        .terminal-button-yellow {
            background: #FFBD2E;
        }

        .terminal-button-green {
            background: #27C93F;
        }

        .terminal-title {
            flex: 1;
            text-align: center;
            color: #8A8A8A;
            font-size: var(--font-size-sm);
            font-weight: 500;
        }

        .terminal-body {
            padding: var(--spacing-lg);
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Code', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .terminal-line {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: 8px;
            opacity: 0;
            animation: terminalLine 0.3s ease forwards;
        }

        .terminal-text {
            color: #d1d1d1;
        }

        .terminal-orange {
            color: #FF6B2C;
            font-weight: bold;
        }

        .terminal-green {
            color: #00D4AA;
        }

        .terminal-blue {
            color: #2D5BFF;
        }

        .terminal-white {
            color: #FFFFFF;
            font-weight: bold;
        }

        .terminal-muted {
            color: #8A8A8A;
        }

        @keyframes terminalLine {
            to {
                opacity: 1;
            }
        }

        .terminal-cursor {
            display: inline-block;
            width: 8px;
            height: 16px;
            background: #FF6B2C;
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }

        /* ===== Section Styles ===== */
        section {
            padding: var(--spacing-3xl) 0;
        }

        .section-header {
            text-align: center;
            max-width: 800px;
            margin: 0 auto var(--spacing-2xl);
        }

        .section-title {
            font-size: var(--font-size-4xl);
            font-weight: 800;
            margin-bottom: var(--spacing-md);
            color: var(--color-text);
        }

        .section-description {
            font-size: var(--font-size-lg);
            color: var(--color-text-secondary);
        }

        /* ===== Features Section ===== */
        .features {
            background: var(--color-bg);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: var(--spacing-lg);
        }

        .feature-card {
            padding: var(--spacing-xl);
            background: var(--color-bg-secondary);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            transition: all var(--transition-base);
            opacity: 0;
            transform: translateY(20px);
        }

        .feature-card.animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--color-primary);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--spacing-md);
        }

        .feature-icon-orange {
            background: linear-gradient(135deg, rgba(255, 107, 44, 0.1) 0%, rgba(255, 143, 107, 0.1) 100%);
            color: var(--color-primary);
        }

        .feature-icon-blue {
            background: linear-gradient(135deg, rgba(45, 91, 255, 0.1) 0%, rgba(91, 127, 255, 0.1) 100%);
            color: var(--color-secondary);
        }

        .feature-icon-green {
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(0, 245, 196, 0.1) 100%);
            color: var(--color-accent);
        }

        .feature-title {
            font-size: var(--font-size-xl);
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
            color: var(--color-text);
        }

        .feature-description {
            color: var(--color-text-secondary);
            line-height: 1.6;
        }

        /* ===== How It Works Section ===== */
        .how-it-works {
            background: var(--color-bg-secondary);
        }

        .steps {
            max-width: 900px;
            margin: 0 auto;
        }

        .step {
            display: grid;
            grid-template-columns: 80px 1fr;
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-2xl);
            opacity: 0;
            transform: translateY(20px);
        }

        .step.animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }

        .step:last-child {
            margin-bottom: 0;
        }

        .step-number {
            font-size: var(--font-size-3xl);
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .step-title {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
            color: var(--color-text);
        }

        .step-description {
            color: var(--color-text-secondary);
            margin-bottom: var(--spacing-md);
            line-height: 1.6;
        }

        .code-block {
            background: #1A1A1A;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .code-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--spacing-sm) var(--spacing-md);
            background: #2A2A2A;
            border-bottom: 1px solid #3A3A3A;
        }

        .code-lang {
            font-size: var(--font-size-xs);
            color: #8A8A8A;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .code-copy {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            background: transparent;
            border: 1px solid #3A3A3A;
            border-radius: var(--radius-sm);
            color: #8A8A8A;
            font-size: var(--font-size-xs);
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .code-copy:hover {
            background: #3A3A3A;
            color: #FFFFFF;
        }

        .code-block pre {
            padding: var(--spacing-md);
            margin: 0;
            overflow-x: auto;
        }

        .code-block code {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Code', monospace;
            font-size: var(--font-size-sm);
            color: #FFFFFF;
            line-height: 1.6;
        }

        /* ===== Comparison Section ===== */
        .comparison {
            background: var(--color-bg);
        }

        .comparison-table {
            max-width: 900px;
            margin: 0 auto;
            background: var(--color-bg-secondary);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
        }

        .comparison-header {
            display: grid;
            grid-template-columns: 2fr repeat(3, 1fr);
            gap: var(--spacing-md);
            padding: var(--spacing-lg);
            background: var(--color-bg-tertiary);
            border-bottom: 2px solid var(--color-border);
        }

        .comparison-row {
            display: grid;
            grid-template-columns: 2fr repeat(3, 1fr);
            gap: var(--spacing-md);
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--color-border);
            transition: background var(--transition-fast);
            opacity: 0;
            transform: translateY(10px);
        }

        .comparison-row.animate-in {
            animation: fadeInUp 0.4s ease forwards;
        }

        .comparison-row:last-child {
            border-bottom: none;
        }

        .comparison-row:hover {
            background: rgba(255, 107, 44, 0.03);
        }

        .comparison-cell {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .comparison-feature {
            justify-content: flex-start;
            font-weight: 600;
            color: var(--color-text);
        }

        .comparison-logo {
            font-weight: 700;
            font-size: var(--font-size-lg);
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .comparison-logo-alt {
            background: linear-gradient(135deg, #6B7280 0%, #9CA3AF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .check {
            color: var(--color-accent);
            font-size: var(--font-size-xl);
            font-weight: 700;
        }

        .cross {
            color: var(--color-text-muted);
            font-size: var(--font-size-xl);
        }

        /* ===== CTA Section ===== */
        .cta {
            background: linear-gradient(135deg, #FF6B2C 0%, #FF8F6B 100%);
            color: white;
        }

        .cta-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: var(--font-size-4xl);
            font-weight: 800;
            margin-bottom: var(--spacing-md);
        }

        .cta-description {
            font-size: var(--font-size-xl);
            margin-bottom: var(--spacing-xl);
            opacity: 0.95;
        }

        .cta-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-md);
        }

        .cta .btn-primary {
            background: white;
            color: var(--color-primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .cta .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .cta .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .cta .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* ===== Footer ===== */
        .footer {
            background: var(--color-bg-secondary);
            padding: var(--spacing-3xl) 0 var(--spacing-xl);
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: var(--spacing-2xl);
            margin-bottom: var(--spacing-2xl);
            padding-bottom: var(--spacing-2xl);
            border-bottom: 1px solid var(--color-border);
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-size: var(--font-size-xl);
            font-weight: 700;
            margin-bottom: var(--spacing-md);
        }

        .footer-logo span {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .footer-tagline {
            color: var(--color-text-secondary);
            font-size: var(--font-size-sm);
        }

        .footer-links {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--spacing-xl);
        }

        .footer-heading {
            font-size: var(--font-size-sm);
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: var(--spacing-md);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-link {
            display: block;
            color: var(--color-text-secondary);
            text-decoration: none;
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-sm);
            transition: color var(--transition-fast);
        }

        .footer-link:hover {
            color: var(--color-primary);
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-copyright {
            color: var(--color-text-muted);
            font-size: var(--font-size-sm);
        }

        .footer-legal {
            display: flex;
            gap: var(--spacing-lg);
        }

        /* ===== Responsive Design ===== */
        @media (max-width: 1024px) {
            .hero-title {
                font-size: var(--font-size-4xl);
            }

            .section-title {
                font-size: var(--font-size-3xl);
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero {
                padding-top: 100px;
            }

            .hero-title {
                font-size: var(--font-size-3xl);
            }

            .hero-description {
                font-size: var(--font-size-lg);
            }

            .hero-actions {
                flex-direction: column;
                width: 100%;
            }

            .hero-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .hero-stats {
                flex-direction: column;
                gap: var(--spacing-lg);
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .step {
                grid-template-columns: 1fr;
                gap: var(--spacing-md);
            }

            .comparison-header,
            .comparison-row {
                grid-template-columns: 1.5fr repeat(3, 1fr);
                gap: var(--spacing-sm);
                padding: var(--spacing-md);
                font-size: var(--font-size-sm);
            }

            .comparison-logo {
                font-size: var(--font-size-base);
            }

            .footer-content {
                grid-template-columns: 1fr;
            }

            .footer-links {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                gap: var(--spacing-md);
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 var(--spacing-md);
            }

            .hero-title {
                font-size: var(--font-size-2xl);
            }

            .section-title {
                font-size: var(--font-size-2xl);
            }

            .cta-title {
                font-size: var(--font-size-2xl);
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="nav">
        <div class="container">
            <div class="nav-content">
                <div class="nav-logo">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect width="32" height="32" rx="8" fill="url(#logo-gradient)" />
                        <path d="M16 8L24 12V20L16 24L8 20V12L16 8Z" stroke="white" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16 16L24 12M16 16L8 12M16 16V24" stroke="white" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <defs>
                            <linearGradient id="logo-gradient" x1="0" y1="0" x2="32"
                                y2="32">
                                <stop offset="0%" stop-color="#FF6B2C" />
                                <stop offset="100%" stop-color="#FF8F6B" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <span class="nav-logo-text">Portex</span>
                </div>
                <div class="nav-links">
                    <a href="#features" class="nav-link">Features</a>
                    <a href="#how-it-works" class="nav-link">How It Works</a>
                    <a href="#comparison" class="nav-link">Comparison</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary">Sign In</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="badge-dot"></span>
                    <span>Self-hosted tunnel service</span>
                </div>
                <h1 class="hero-title">
                    Expose your local services
                    <span class="gradient-text">to the internet</span>
                </h1>
                <p class="hero-description">
                    Fast, secure, and easy-to-use tunnel service. The perfect alternative to ngrok, bore, and
                    localtunnel.
                    Built with Go for maximum performance and reliability.
                </p>
                <div class="hero-actions">
                    <a href="#how-it-works" class="btn btn-secondary btn-large">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="2" />
                            <path d="M10 10L13 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        Watch Demo
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-value">99.9%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">&lt;50ms</div>
                        <div class="stat-label">Latency</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value">10K+</div>
                        <div class="stat-label">Active Tunnels</div>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="terminal-body">
                    <div class="terminal-line" style="animation-delay: 0.2s">
                        <span class="terminal-green">$</span>
                        <span class="terminal-command">portex share ./my-project --pin 1234</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 1s">
                        <span
                            class="terminal-muted">────────────────────────────────────────────────────────────</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 1.2s">
                        <span class="terminal-orange"> PORTEX</span> <span class="terminal-muted">1.0.0</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 1.4s">
                        <span class="terminal-muted">
                            ────────────────────────────────────────────────────────────</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 1.6s">
                        <span class="terminal-text"> Status </span> <span class="terminal-green">Online</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 1.8s">
                        <span class="terminal-text"> Account </span> <span
                            class="terminal-muted">pk_Zb5gs02XP8mC...</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 2s">
                        <span class="terminal-text"> Usage </span> <span class="terminal-muted">1h 45m / 3h 0m
                            (58.3%)</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 2.2s">
                        <span class="terminal-muted"> </span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 2.4s">
                        <span class="terminal-orange"> ACTIVE TUNNEL</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 2.6s">
                        <span class="terminal-white"> https://my-project.portex.space</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 2.8s">
                        <span class="terminal-muted"> ↳ forwarding to internal local server</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 3s">
                        <span class="terminal-muted"> </span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 3.2s">
                        <span class="terminal-text"> </span> <span class="terminal-white">SCAN FOR MOBILE</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 3.4s">
                        <span class="terminal-muted"> [ QR Code Generated ]</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 3.6s">
                        <span class="terminal-muted"> </span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 3.8s">
                        <span class="terminal-muted">
                            ────────────────────────────────────────────────────────────</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 4s">
                        <span class="terminal-muted"> Press Ctrl+C to stop</span>
                    </div>
                    <div class="terminal-line" style="animation-delay: 4.2s">
                        <span class="terminal-cursor"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Built for developers, <span class="gradient-text">by developers</span></h2>
                <p class="section-description">Everything you need to expose your local services securely and
                    efficiently</p>
            </div>
            <div class="features-grid">
                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon feature-icon-orange">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">PIN Protection</h3>
                    <p class="feature-description">Secure your tunnels with a 4-digit PIN. Perfect for private demos,
                        client reviews, or sensitive internal tools.</p>
                </div>

                <div class="feature-card animate-on-scroll" style="animation-delay: 0.1s">
                    <div class="feature-icon feature-icon-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </div>
                    <h3 class="feature-title">Fast Directory Sharing</h3>
                    <p class="feature-description">Instantly host any local directory with a single command. `portex
                        share .` makes file sharing and static hosting effortless.</p>
                </div>

                <div class="feature-card animate-on-scroll" style="animation-delay: 0.2s">
                    <div class="feature-icon feature-icon-green">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Traffic Inspector</h3>
                    <p class="feature-description">Real-time HTTP request logging with request/response capture. Replay
                        requests or copy them as cURL with one click.</p>
                </div>

                <div class="feature-card animate-on-scroll">
                    <div class="feature-icon feature-icon-orange">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Scan to Test</h3>
                    <p class="feature-description">The agent generates a QR code for every tunnel. Scan with your phone
                        to test mobile responsiveness instantly.</p>
                </div>

                <div class="feature-card animate-on-scroll" style="animation-delay: 0.1s">
                    <div class="feature-icon feature-icon-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path
                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Custom Subdomains</h3>
                    <p class="feature-description">Reserve your own subdomains or connect custom domains. Your tunnels,
                        your branding, your way.</p>
                </div>

                <div class="feature-card animate-on-scroll" style="animation-delay: 0.2s">
                    <div class="feature-icon feature-icon-green">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <h3 class="feature-title">High Performance</h3>
                    <p class="feature-description">Built with Go for maximum reliability. Handle high-traffic loads
                        with minimal overhead and lightning-fast speeds.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Get started in <span class="gradient-text">3 simple steps</span></h2>
                <p class="section-description">From zero to production in minutes</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">01</div>
                    <div class="step-content">
                        <h3 class="step-title">Install the Agent</h3>
                        <p class="step-description">Download and install the Portex agent on your machine. Available
                            for macOS, Linux, and Windows.</p>
                        <div class="code-block">
                            <div class="code-header">
                                <span class="code-lang">bash</span>
                                <button class="code-copy" onclick="copyCode(this)">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <rect x="5" y="5" width="9" height="9" rx="1"
                                            stroke="currentColor" stroke-width="1.5" />
                                        <path d="M3 11V3a1 1 0 011-1h8" stroke="currentColor" stroke-width="1.5" />
                                    </svg>
                                    Copy
                                </button>
                            </div>
                            <pre><code>curl -L https://portex.space/install.sh | bash</code></pre>
                        </div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">02</div>
                    <div class="step-content">
                        <h3 class="step-title">Authenticate</h3>
                        <p class="step-description">Connect your agent to the Portex server using your API credentials
                            from the dashboard.</p>
                        <div class="code-block">
                            <div class="code-header">
                                <span class="code-lang">bash</span>
                                <button class="code-copy" onclick="copyCode(this)">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <rect x="5" y="5" width="9" height="9" rx="1"
                                            stroke="currentColor" stroke-width="1.5" />
                                        <path d="M3 11V3a1 1 0 011-1h8" stroke="currentColor" stroke-width="1.5" />
                                    </svg>
                                    Copy
                                </button>
                            </div>
                            <pre><code>portex auth --api-key YOUR_KEY --api-secret YOUR_SECRET</code></pre>
                        </div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">03</div>
                    <div class="step-content">
                        <h3 class="step-title">Share Anything</h3>
                        <p class="step-description">Need to share a static site or a file? Just use the share command
                            to
                            instantly host it.</p>
                        <div class="code-block">
                            <div class="code-header">
                                <span class="code-lang">CLI</span>
                                <button class="code-copy" onclick="copyCode(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="9" y="9" width="13" height="13" rx="2"
                                            ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    Copy
                                </button>
                            </div>
                            <pre><code>portex share ./my-site --subdomain dev</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison Section -->
    <section id="comparison" class="comparison">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Why choose <span class="gradient-text">Portex</span>?</h2>
                <p class="section-description">See how we compare to other tunnel services</p>
            </div>
            <div class="comparison-table">
                <div class="comparison-header">
                    <div class="comparison-cell"></div>
                    <div class="comparison-cell">
                        <div class="comparison-logo">Portex</div>
                    </div>
                    <div class="comparison-cell">
                        <div class="comparison-logo comparison-logo-alt">ngrok</div>
                    </div>
                    <div class="comparison-cell">
                        <div class="comparison-logo comparison-logo-alt">Pinggy</div>
                    </div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-cell comparison-feature">PIN Protection</div>
                    <div class="comparison-cell"><span class="check">✓</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-cell comparison-feature">Mobile QR Testing</div>
                    <div class="comparison-cell"><span class="check">✓</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                    <div class="comparison-cell"><span class="check">✓</span></div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-cell comparison-feature">Self-hosted</div>
                    <div class="comparison-cell"><span class="check">✓</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-cell comparison-feature">Unlimited tunnels</div>
                    <div class="comparison-cell"><span class="check">✓</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-cell comparison-feature">Real-time analytics</div>
                    <div class="comparison-cell"><span class="check">✓</span></div>
                    <div class="comparison-cell"><span class="check">✓</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                </div>
                <div class="comparison-row">
                    <div class="comparison-cell comparison-feature">Open source</div>
                    <div class="comparison-cell"><span class="check">✓</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                    <div class="comparison-cell"><span class="cross">✗</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to get started?</h2>
                <p class="cta-description">Join thousands of developers using Portex to expose their local services</p>
                <div class="cta-actions">
                    <a href="#how-it-works" class="btn btn-secondary btn-large">
                        Read Documentation
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect width="32" height="32" rx="8" fill="url(#footer-logo-gradient)" />
                            <path d="M16 8L24 12V20L16 24L8 20V12L16 8Z" stroke="white" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M16 16L24 12M16 16L8 12M16 16V24" stroke="white" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <linearGradient id="footer-logo-gradient" x1="0" y1="0"
                                    x2="32" y2="32">
                                    <stop offset="0%" stop-color="#FF6B2C" />
                                    <stop offset="100%" stop-color="#FF8F6B" />
                                </linearGradient>
                            </defs>
                        </svg>
                        <span>Portex</span>
                    </div>
                    <p class="footer-tagline">Fast, secure, and self-hosted tunnel service</p>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h4 class="footer-heading">Product</h4>
                        <a href="#features" class="footer-link">Features</a>
                        <a href="{{ route('dashboard') }}" class="footer-link">Dashboard</a>
                        <a href="#how-it-works" class="footer-link">Documentation</a>
                    </div>
                    <div class="footer-column">
                        <h4 class="footer-heading">Company</h4>
                        <a href="#" class="footer-link">About</a>
                        <a href="#" class="footer-link">Blog</a>
                        <a href="#" class="footer-link">Contact</a>
                    </div>
                    <div class="footer-column">
                        <h4 class="footer-heading">Resources</h4>
                        <a href="#" class="footer-link">Community</a>
                        <a href="#" class="footer-link">Support</a>
                        <a href="#" class="footer-link">GitHub</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copyright">© 2024 Portex. All rights reserved.</p>
                <div class="footer-legal">
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Copy code functionality
        function copyCode(button) {
            const codeBlock = button.closest('.code-block');
            const code = codeBlock.querySelector('code').textContent;
            navigator.clipboard.writeText(code).then(() => {
                button.innerHTML =
                    '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13.5 4.5L6 12L2.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>Copied!';
                setTimeout(() => {
                    button.innerHTML =
                        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="5" y="5" width="9" height="9" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M3 11V3a1 1 0 011-1h8" stroke="currentColor" stroke-width="1.5"/></svg>Copy';
                }, 2000);
            });
        }

        // Terminal animation
        const terminalLines = document.querySelectorAll('.terminal-line');
        terminalLines.forEach((line, index) => {
            line.style.animationDelay = `${index * 0.3}s`;
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .step, .comparison-row').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>

</html>
