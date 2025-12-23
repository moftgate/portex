@extends('components.layouts.marketing')

@section('title', 'Secure Remote Access (SSH/TCP)')

@section('content')
    <header class="hero">
        <div class="container">
            <div class="badge">Solutions</div>
            <h1>Access Your Servers <br>From Anywhere, Securely</h1>
            <p>Portex isn't just for HTTP. Tunnel SSH, RDP, or any TCP protocol to access your remote workstations or edge
                devices without a VPN or open ports.</p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="/#guide" class="btn btn-primary">Download Agent</a>
                <a href="{{ route('docs.security') }}" class="btn btn-outline">Security Guide</a>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
                <div
                    style="background: #1e293b; padding: 40px; border-radius: 24px; color: #e2e8f0; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 14px;">
                        <span style="color: #94a3b8;"># Tunnel your SSH port (22)</span><br>
                        <span style="color: #fff; font-weight: bold;">$ portex start --tcp 22</span><br><br>
                        <span style="color: #94a3b8;"># Access it from anywhere:</span><br>
                        <span style="color: var(--orange);">ssh user@tcp.portex.space -p 12345</span>
                    </div>
                </div>
                <div>
                    <h2 style="font-size: 32px; margin-bottom: 24px;">Eliminate Insecure Open Ports</h2>
                    <p style="color: var(--slate); font-size: 18px; margin-bottom: 32px;">Stop exposing your SSH ports to
                        the public internet. Portex creates a secure tunnel through our edge network, so your devices remain
                        hidden but accessible from anywhere.</p>

                    <ul style="list-style: none;">
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Direct TCP tunneling</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Secure access through firewalls</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>No static IP address required</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--light);">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 48px;">Enterprise-Grade Security</h2>
            <div class="feature-grid">
                <div class="feature-card" style="background: white;">
                    <h3>End-to-End Encryption</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Your data is encrypted from the moment it leaves your
                        device until it reaches the agent, ensuring no one can tap into your session.</p>
                </div>
                <div class="feature-card" style="background: white;">
                    <h3>Device Identity</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Each agent uses a unique identity and cryptographic
                        keys to authenticate with the Portex network.</p>
                </div>
                <div class="feature-card" style="background: white;">
                    <h3>Network Stealth</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Your devices don't need a public IP or an inbound
                        firewall rule. They only make an outbound connection to our controller.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
