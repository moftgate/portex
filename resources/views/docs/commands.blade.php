@extends('components.layouts.docs')

@section('title', 'CLI Commands')

@section('content')
    <h1>CLI Command Reference</h1>
    <p>The Portex CLI is designed to be intuitive and minimal. Here is a breakdown of the available commands and their
        options.</p>

    <h2 id="start">portex start</h2>
    <p>Creates a secure tunnel to a local port. This is the most used command for web development.</p>

    <div class="code-header">Basic Usage</div>
    <div class="code-block">
        portex start --port 3000
    </div>

    <div class="code-header">Options</div>
    <ul>
        <li><code>--port, -p</code> (Required): The local port you want to expose.</li>
        <li><code>--subdomain, -s</code>: Specify a custom subdomain.</li>
        <li><code>--pin</code>: Secure your tunnel with a 4-digit PIN.</li>
    </ul>

    <div class="code-header">Complex Example</div>
    <div class="code-block">
        portex start -p 8080 --subdomain staging-api --pin 9988
    </div>

    <h2 id="share">portex share</h2>
    <p>Turns any local directory into a live HTTPS website. Portex starts a temporary web server for the directory and
        tunnels it automatically.</p>

    <div class="code-header">Usage</div>
    <div class="code-block">
        portex share ./public_html
    </div>
    <p>Useful for sharing static builds (React, Vue, Vite) without needing to configure a local server.</p>

    <h2 id="login">portex login</h2>
    <p>Connects your active agent to your Portex account. This unlocks premium features and persistent dashboard logging.
    </p>
    <div class="code-block">
        portex login
    </div>
    <p>This will open your default browser to complete the authentication securely.</p>

    <div class="callout">
        <span>Shortcuts</span>
        Most flags have shorthand versions. Use <code>-p</code> for port and <code>-s</code> for subdomain to save time.
    </div>
@endsection
