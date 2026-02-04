@extends('components.layouts.docs')

@section('title', 'Troubleshooting & FAQ')

@section('content')
    <h1>Troubleshooting</h1>
    <p>Running into issues? Here are solutions to common problems and frequently asked questions.</p>

    <h2>Common Issues</h2>

    <h3>Connection Refused / Tunnel Won't Start</h3>
    <p><strong>Problem:</strong> The agent can't connect to the Portex server.</p>
    <p><strong>Solution:</strong></p>
    <ul style="margin-left: 24px; margin-bottom: 32px;">
        <li>Check your internet connection</li>
        <li>Verify that port 443 (HTTPS) and WebSocket connections aren't blocked by your firewall</li>
        <li>Try running with <code>--verbose</code> flag for detailed logs</li>
    </ul>

    <h3>Local Server Not Responding</h3>
    <p><strong>Problem:</strong> The tunnel is active but requests return 502 Bad Gateway.</p>
    <p><strong>Solution:</strong></p>
    <ul style="margin-left: 24px; margin-bottom: 32px;">
        <li>Ensure your local server is actually running on the specified port</li>
        <li>Test locally: <code>curl http://localhost:3000</code></li>
        <li>Check if another process is using the port: <code>lsof -i :3000</code></li>
    </ul>

    <h3>Subdomain Already Taken</h3>
    <p><strong>Problem:</strong> You get an error that your custom subdomain is unavailable.</p>
    <p><strong>Solution:</strong></p>
    <ul style="margin-left: 24px; margin-bottom: 32px;">
        <li>Try a different subdomain name</li>
        <li>If you previously used this subdomain, log in to release it from your account</li>
        <li>Premium users can reserve subdomains permanently</li>
    </ul>

    <h2>Frequently Asked Questions</h2>

    <h3>Is Portex free?</h3>
    <p>Yes! Portex offers a generous free tier with unlimited tunnels and basic features. Premium plans add custom domains,
        extended logs, and priority support.</p>

    <h3>Do I need to create an account?</h3>
    <p>No. You can use Portex in guest mode without authentication. However, logging in unlocks features like custom
        subdomains, request history, and dashboard access.</p>

    <h3>Is my data secure?</h3>
    <p>Absolutely. All traffic is encrypted via TLS. We don't store request/response bodies unless you're logged in and have
        explicitly enabled logging. Even then, only you can access your data.</p>

    <h3>Can I use Portex in production?</h3>
    <p>Portex is designed for development and testing. While it's stable and secure, we recommend using dedicated
        infrastructure for production workloads.</p>

    <h3>What's the difference between Portex and ngrok?</h3>
    <p>Portex is built with modern developer workflows in mind. We offer:</p>
    <ul style="margin-left: 24px; margin-bottom: 32px;">
        <li>Open-source agent (full transparency)</li>
        <li>Built-in static file sharing</li>
        <li>PIN protection without authentication</li>
        <li>Beautiful, modern dashboard</li>
        <li>Competitive pricing with a generous free tier</li>
    </ul>

    <h3>How do I update the Portex agent?</h3>
    <p>Simply re-run the installation script. It will download and replace the old binary with the latest version:</p>
    <div class="code-block">
        curl -fsSL https://portex.space/install.sh | bash
    </div>

    <h3>How do I remove Portex from my computer?</h3>
    <p>Check out our <a href="{{ route('docs.uninstall') }}"
            style="color: var(--orange); font-weight: 600; text-decoration: none;">Uninstall Guide</a> for detailed steps on
        removing the binary and configuration files for your system.</p>

    <div class="callout">
        <span>Still Need Help?</span>
        Join our community on Discord or open an issue on <a href="https://github.com/orgs/portex-space/repositories"
            target="_blank" style="color: var(--orange); font-weight: 600; text-decoration: none;">GitHub</a>.
    </div>
@endsection
