@extends('components.layouts.docs')

@section('title', 'Roadmap & Future Plans')

@section('content')
    <h1>Roadmap</h1>
    <p>Portex is actively developed with a clear vision: to become the most developer-friendly tunneling solution. Here's
        what we're working on and what's coming next.</p>

    <h2>Current Version: v0.6.0 (Beta)</h2>
    <p>We're in active beta with core features stable and production-ready. Our focus is on gathering feedback and refining
        the user experience.</p>

    <h2>Q1 2025 - Stability & Performance</h2>
    <div class="feature-grid">
        <div class="feature-card">
            <h3>✅ Multi-Region Support</h3>
            <p>Deploy edge servers in US, EU, and Asia for lower latency worldwide.</p>
        </div>
        <div class="feature-card">
            <h3>🔄 WebSocket Reconnection</h3>
            <p>Enhanced auto-reconnect logic with exponential backoff for unstable networks.</p>
        </div>
        <div class="feature-card">
            <h3>📊 Advanced Analytics</h3>
            <p>Bandwidth usage graphs, request heatmaps, and performance insights in the dashboard.</p>
        </div>
        <div class="feature-card">
            <h3>🔐 Custom Domains</h3>
            <p>Bring your own domain (e.g., <code>api.yourdomain.com</code>) and point it to Portex.</p>
        </div>
    </div>

    <h2>Q2 2025 - Developer Experience</h2>
    <ul style="margin-left: 24px; margin-bottom: 32px;">
        <li style="margin-bottom: 12px;"><strong>Request Mocking:</strong> Define mock responses for specific endpoints
            directly in the dashboard.</li>
        <li style="margin-bottom: 12px;"><strong>Webhook Forwarding:</strong> Save webhook payloads and replay them to
            multiple local environments.</li>
        <li style="margin-bottom: 12px;"><strong>Team Collaboration:</strong> Share tunnels with team members and manage
            access controls.</li>
        <li style="margin-bottom: 12px;"><strong>CLI Plugins:</strong> Extensible plugin system for custom middleware and
            transformations.</li>
    </ul>

    <h2>Q3 2025 - Enterprise Features</h2>
    <ul style="margin-left: 24px; margin-bottom: 32px;">
        <li style="margin-bottom: 12px;"><strong>SSO Integration:</strong> Support for SAML, OAuth, and enterprise identity
            providers.</li>
        <li style="margin-bottom: 12px;"><strong>Audit Logs:</strong> Comprehensive logging for compliance and security
            teams.</li>
        <li style="margin-bottom: 12px;"><strong>Private Deployments:</strong> Self-hosted Portex server for organizations
            with strict data policies.</li>
        <li style="margin-bottom: 12px;"><strong>SLA Guarantees:</strong> 99.9% uptime commitment with dedicated support.
        </li>
    </ul>

    <h2>Long-term Vision</h2>
    <p>Our goal is to build a complete development infrastructure platform. Beyond tunneling, we're exploring:</p>
    <ul style="margin-left: 24px; margin-bottom: 32px;">
        <li style="margin-bottom: 12px;">Local database tunneling (MySQL, PostgreSQL, Redis)</li>
        <li style="margin-bottom: 12px;">Integrated testing environments with automated E2E tests</li>
        <li style="margin-bottom: 12px;">Real-time collaboration tools for pair programming</li>
        <li style="margin-bottom: 12px;">AI-powered request analysis and debugging suggestions</li>
    </ul>

    <div class="callout">
        <span>Community Driven</span>
        Have a feature request? Open an issue on <a href="https://github.com/orgs/portex-space/repositories" target="_blank"
            style="color: var(--orange); font-weight: 600; text-decoration: none;">GitHub</a> or join our Discord community.
    </div>
@endsection
