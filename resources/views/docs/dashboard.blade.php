@extends('components.layouts.docs')

@section('title', 'Dashboard Guide')

@section('content')
    <h1>Monitoring & Dashboard</h1>
    <p>The Portex Dashboard provides real-time insights into your active tunnels and incoming traffic. It's the mission
        control for your development environment.</p>

    <img src="/docs_dashboard_mockup.png" alt="Dashboard Mockup" class="doc-image">

    <h2>Tunnel Management</h2>
    <p>In the "Tunnels" section, you can see all agents currently connected to your account. You can view their public URLs,
        local ports, and current status (Online/Offline).</p>

    <h2>Real-time Activity Log</h2>
    <p>One of the most powerful features of Portex is the Activity Log. For every request that hits your tunnel, we capture:
    </p>
    <ul>
        <li><strong>Method & Path:</strong> e.g., <code>POST /api/webhooks</code></li>
        <li><strong>Status Code:</strong> See if your local server responded with an error instantly.</li>
        <li><strong>Response Time:</strong> Monitor the latency of your local service.</li>
        <li><strong>Full Payload:</strong> Inspect headers and body content to debug integration issues.</li>
    </ul>

    <h2>Replay Requests</h2>
    <p>Debugged your code but missed the original webhook? No problem. Use the <strong>Replay</strong> button in the
        dashboard to resend the exact same request to your local environment without having to trigger the external service
        again.</p>

    <div class="callout">
        <span>Analytics</span>
        Premium users get access to bandwidth usage stats and extended log retention.
    </div>
@endsection
