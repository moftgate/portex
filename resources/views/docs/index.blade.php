@extends('components.layouts.docs')

@section('title', 'Welcome to Portex')

@section('content')
    <h1>Introduction</h1>
    <p>Portex is the modern, developer-first way to expose your local development environment to the internet. Built with Go
        for performance and Laravel for management, it provides a seamless bridge between your localhost and the world.</p>

    <img src="/docs_tunnel_illustration.png" alt="Secure Tunnel Illustration" class="doc-image">

    <h2>Zero Friction</h2>
    <p>We believe development tools should be transparent. Portex allows you to start a tunnel without creating an account
        (guest mode), share static files instantly, and debug incoming requests in real-time.</p>

    <div class="feature-grid">
        <div class="feature-card">
            <h3>Custom Subdomains</h3>
            <p>Reserve names like <code>api-test.portex.space</code> for consistent testing.</p>
        </div>
        <div class="feature-card">
            <h3>PIN Protection</h3>
            <p>Restrict access to your tunnels with 4-digit PINs for secure demos.</p>
        </div>
        <div class="feature-card">
            <h3>Traffic Inspection</h3>
            <p>Live view of every header, payload, and response time passing through.</p>
        </div>
        <div class="feature-card">
            <h3>Directory Sharing</h3>
            <p>Serve local directories as HTTPS sites with a single command.</p>
        </div>
    </div>

    <div class="callout">
        <span>Quick Note</span>
        Portex is built for developers. Our agent is open source, and you can find all our repositories on <a
            href="https://github.com/orgs/portex-space/repositories" target="_blank"
            style="color: var(--orange); font-weight: 600; text-decoration: none;">GitHub</a>.
    </div>
@endsection
