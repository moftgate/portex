@extends('components.layouts.marketing')

@section('title', 'Webhook Debugging & Testing')

@section('content')
    <header class="hero">
        <div class="container">
            <div class="badge">Solutions</div>
            <h1>Debug Webhooks <br>Without the Headache</h1>
            <p>Receiving Stripe, GitHub, or Shopify webhooks on localhost has never been easier. No more repetitive
                deployments just to test a single integration.</p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="/#guide" class="btn btn-primary">Start Tunneling</a>
                <a href="{{ route('docs.use-cases') }}" class="btn btn-outline">Read Guide</a>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
                <div>
                    <img src="/landing_webhooks.png" alt="Webhook Debugging"
                        style="width: 100%; border-radius: 20px; border: 1px solid var(--border);">
                </div>
                <div>
                    <h2 style="font-size: 32px; margin-bottom: 24px;">Full Visibility Into Every Request</h2>
                    <p style="color: var(--slate); font-size: 18px; margin-bottom: 32px;">Portex allows you to inspect every
                        part of the incoming webhook. Headers, body, and timing are all logged and visible in your
                        dashboard.</p>

                    <ul style="list-style: none;">
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Live traffic monitoring</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Payload replay with one click</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Support for nested JSON and form-data</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--light);">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 48px;">How it works</h2>
            <div class="feature-grid">
                <div class="feature-card" style="background: white;">
                    <div style="font-size: 24px; font-weight: 800; color: var(--orange); margin-bottom: 16px;">01.</div>
                    <h3>Start Portex</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Fire up your terminal and run <code>portex start
                            --port 3000</code>.</p>
                </div>
                <div class="feature-card" style="background: white;">
                    <div style="font-size: 24px; font-weight: 800; color: var(--orange); margin-bottom: 16px;">02.</div>
                    <h3>Set Endpoint</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Copy your public portex URL (e.g. my-app.portex.space)
                        into your provider's webhook settings.</p>
                </div>
                <div class="feature-card" style="background: white;">
                    <div style="font-size: 24px; font-weight: 800; color: var(--orange); margin-bottom: 16px;">03.</div>
                    <h3>Debug Live</h3>
                    <p style="color: var(--slate); margin-top: 12px;">Trigger the webhook and watch the request reach your
                        local machine instantly.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
