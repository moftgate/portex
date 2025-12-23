@extends('components.layouts.docs')

@section('title', 'System Architecture & Infrastructure')

@section('content')
    <h1>How it Works</h1>
    <p>Portex is built as a distributed system that manages secure tunnels between your local development environment and
        our global gateway. Understanding the flow of data helps in debugging complex integrations.</p>

    <img src="/docs_architecture_diagram.png" alt="Architecture Diagram" class="doc-image">

    <h2>System Components</h2>
    <div class="feature-grid">
        <div class="feature-card">
            <h3>The Agent (CLI)</h3>
            <p>Written in <strong>Go</strong>, the agent establishes a persistent WebSocket connection to our tunnel server.
                It serves as a bridge, forwarding incoming traffic to your local port.</p>
        </div>
        <div class="feature-card">
            <h3>Tunnel Server</h3>
            <p>Our high-performance Go-based gateway. It manages active connections, handles subdomain routing, and proxies
                requests in real-time with microsecond latency.</p>
        </div>
        <div class="feature-card">
            <h3>Backend (API)</h3>
            <p>A <strong>Laravel 12</strong> application that handles user authentication, tunnel registration, and stores
                the activity logs you see in the dashboard.</p>
        </div>
        <div class="feature-card">
            <h3>The Gateway</h3>
            <p>Our edge servers handle SSL termination and PIN verification before traffic even touches the tunnel, ensuring
                only authorized requests reach your machine.</p>
        </div>
    </div>

    <h2>The Request Lifecycle</h2>
    <ol style="margin-left: 24px; margin-bottom: 32px;">
        <li style="margin-bottom: 16px;"><strong>Connection:</strong> When you run <code>portex start</code>, the agent
            connects via <strong>Secure WebSockets (WSS)</strong> to our server.</li>
        <li style="margin-bottom: 16px;"><strong>Ingress:</strong> A visitor hits your public URL (e.g.,
            <code>myapp.portex.space</code>).</li>
        <li style="margin-bottom: 16px;"><strong>Verification:</strong> The Portex Gateway checks if the tunnel is active
            and verifies any required PIN or authentication.</li>
        <li style="margin-bottom: 16px;"><strong>Proxying:</strong> The request is encapsulated and sent through the
            WebSocket tunnel to your local agent.</li>
        <li style="margin-bottom: 16px;"><strong>Execution:</strong> Your local server receives the request, processes it,
            and sends a response back through the same tunnel.</li>
    </ol>

    <div class="callout">
        <span>Performance</span>
        By using Go for the data plane (Tunnel Server & Agent), we ensure that Portex adds negligible overhead to your
        request times.
    </div>

    <h2>Infrastructure Safety</h2>
    <p>Every tunnel runs in an isolated context. We don't store your request/response bodies unless you are logged in and
        have enabled logging, and even then, they are stored securely and only accessible by you.</p>
@endsection
