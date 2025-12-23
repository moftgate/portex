@extends('components.layouts.marketing')

@section('title', 'API Development & Collaboration')

@section('content')
    <header class="hero">
        <div class="container">
            <div class="badge">Solutions</div>
            <h1>The Fastest Way to <br>Share Local APIs</h1>
            <p>Develop, test, and share your local APIs with frontend developers, mobile teams, or clients in seconds. No
                staging server required.</p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="/#guide" class="btn btn-primary">Start Tunneling</a>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
                <div>
                    <h2 style="font-size: 32px; margin-bottom: 24px;">Frontend-Backend Collaboration Made Easy</h2>
                    <p style="color: var(--slate); font-size: 18px; margin-bottom: 32px;">Portex allows you to share your
                        local backend with your frontend developer across the world. They can call your local API as if it
                        were running on a production server.</p>

                    <ul style="list-style: none;">
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Secure HTTPS by default</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Inspect every API request and response</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Share specific local ports instantly</span>
                        </li>
                    </ul>
                </div>
                <div
                    style="background: var(--light); padding: 40px; border-radius: 24px; border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.05);">
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 14px;">
                        <span style="color: var(--slate);"># Start your API tunnel</span><br>
                        <span style="color: var(--dark); font-weight: bold;">$ portex start --port 8000</span><br><br>
                        <span style="color: var(--slate);"># Output</span><br>
                        <span style="color: var(--orange);">https://local-api.portex.space</span><br>
                        <span style="color: var(--slate);">↳ Forwarding to http://localhost:8000</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--light);">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 48px;">Trusted Features for Modern API Dev</h2>
            <div class="feature-grid">
                <div class="feature-card" style="background: white;">
                    <h3>Real-time Logging</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Watch the logs in real-time as your partner makes
                        requests to your local machine. Debug headers and body data on the fly.</p>
                </div>
                <div class="feature-card" style="background: white;">
                    <h3>CORS Handling</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Development over HTTPS avoids common cross-origin and
                        security policy issues found in browser-to-localhost requests.</p>
                </div>
                <div class="feature-card" style="background: white;">
                    <h3>Easy Replay</h3>
                    <p style="color: var(--slate); margin-top: 12px;">One-click replay in the Portex dashboard lets you
                        reproduce bugs found by your frontend team instantly.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
