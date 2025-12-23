@extends('components.layouts.docs')

@section('title', 'Pricing & Limits')

@section('content')
    <h1>Pricing</h1>
    <p>Portex is currently in public beta. During this period, all core features are free to use. As we scale, we will
        introduce a simple, developer-friendly pricing structure.</p>

    <div class="feature-grid">
        <div class="feature-card" style="border-color: var(--orange); background: var(--orange-dim);">
            <div class="code-header" style="color: var(--orange)">Current Phase</div>
            <h3>Free Beta</h3>
            <p style="font-size: 24px; font-weight: 800; margin: 16px 0;">$0 <span
                    style="font-size: 14px; font-weight: 400; color: var(--slate);">/ month</span></p>
            <ul style="list-style: none; font-size: 14px; color: #334155;">
                <li style="margin-bottom: 8px;">✅ Unlimited Tunnels</li>
                <li style="margin-bottom: 8px;">✅ Custom Subdomains</li>
                <li style="margin-bottom: 8px;">✅ Traffic Inspection (Last 50)</li>
                <li style="margin-bottom: 8px;">✅ Static Directory Sharing</li>
                <li style="margin-bottom: 8px;">✅ Global Edge Locations</li>
            </ul>
        </div>
        <div class="feature-card">
            <div class="code-header">Coming Soon</div>
            <h3>Pro Plan</h3>
            <p style="font-size: 24px; font-weight: 800; margin: 16px 0;">$7 <span
                    style="font-size: 14px; font-weight: 400; color: var(--slate);">/ month</span></p>
            <ul style="list-style: none; font-size: 14px; color: #334155;">
                <li style="margin-bottom: 8px;">➕ Everything in Free</li>
                <li style="margin-bottom: 8px;">➕ Reserved Subdomains</li>
                <li style="margin-bottom: 8px;">➕ Unlimited Request Logs</li>
                <li style="margin-bottom: 8px;">➕ Team Management</li>
                <li style="margin-bottom: 8px;">➕ Priority Support</li>
            </ul>
        </div>
    </div>

    <h2>Usage Limits</h2>
    <p>To ensure fair performance for everyone during the beta, the following soft limits apply:</p>

    <table style="width: 100%; border-collapse: collapse; margin-top: 24px; font-size: 15px;">
        <thead>
            <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                <th style="padding: 12px 0;">Resource</th>
                <th style="padding: 12px 0;">Free Quota</th>
            </tr>
        </thead>
        <tbody style="color: #334155;">
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 12px 0; font-weight: 600;">Concurrent Tunnels</td>
                <td style="padding: 12px 0;">3 active tunnels per agent</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 12px 0; font-weight: 600;">Monthly Bandwidth</td>
                <td style="padding: 12px 0;">10 GB</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 12px 0; font-weight: 600;">Request History</td>
                <td style="padding: 12px 0;">Stored for 24 hours</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 12px 0; font-weight: 600;">API Rate Limit</td>
                <td style="padding: 12px 0;">100 requests / hour</td>
            </tr>
        </tbody>
    </table>

    <div class="callout">
        <span>Open Source</span>
        The Portex agent is and will always be Open Source. Our pricing only covers the infrastructure costs of the global
        gateway and dashboard management.
    </div>
@endsection
