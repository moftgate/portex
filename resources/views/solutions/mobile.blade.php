@extends('components.layouts.marketing')

@section('title', 'Mobile Development & Devices')

@section('content')
    <header class="hero">
        <div class="container">
            <div class="badge">Solutions</div>
            <h1>Test on Mobile <br>Without the Hassle</h1>
            <p>Connect your physical iOS and Android devices to your local server instantly. No complex network
                configuration or manual IP typing required.</p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="/#guide" class="btn btn-primary">Try for Free</a>
                <a href="{{ route('docs.index') }}" class="btn btn-outline">Documentation</a>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
                <div>
                    <h2 style="font-size: 32px; margin-bottom: 24px;">Scan. Connect. Develop.</h2>
                    <p style="color: var(--slate); font-size: 18px; margin-bottom: 32px;">Portex generates a QR code
                        directly in your terminal. Just point your phone's camera at it and you're instantly connected to
                        your local development environment over a secure HTTPS tunnel.</p>

                    <div
                        style="background: var(--light); padding: 24px; border-radius: 16px; border-left: 4px solid var(--orange);">
                        <p style="font-family: 'JetBrains Mono', monospace; font-size: 14px; color: var(--dark);">
                            $ portex start --port 8080<br>
                            <span style="color: var(--slate);">... Tunnel Online: https://dev-mobile.portex.space</span><br>
                            <span style="color: var(--orange);">[ QR CODE GENERATED ]</span>
                        </p>
                    </div>
                </div>
                <div>
                    <img src="/landing_mobile.png" alt="Mobile Testing"
                        style="width: 100%; border-radius: 20px; border: 1px solid var(--border);">
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--light);">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 48px;">Why developers choose Portex for Mobile</h2>
            <div class="feature-grid">
                <div class="feature-card" style="background: white;">
                    <h3>Secure HTTPS</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Modern mobile OS and browsers block non-HTTPS traffic.
                        Portex provides valid SSL out of the box.</p>
                </div>
                <div class="feature-card" style="background: white;">
                    <h3>Cross-Platform</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Works seamlessly with Flutter, React Native, and
                        iOS/Android native development.</p>
                </div>
                <div class="feature-card" style="background: white;">
                    <h3>Global Edge</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Low latency connections no matter where your device
                        and server are located.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
