@extends('components.layouts.marketing')

@section('title', 'CI/CD & Automated Testing')

@section('content')
    <header class="hero">
        <div class="container">
            <div class="badge">Solutions</div>
            <h1>Bridge the Gap Between <br>CI and Local Environments</h1>
            <p>Use Portex to expose CI services, trigger local webhooks during testing, or create ephemeral preview
                environments in your CI/CD pipeline.</p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="/#guide" class="btn btn-primary">Get API Key</a>
                <a href="{{ route('docs.index') }}" class="btn btn-outline">Integrations</a>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
                <div>
                    <h2 style="font-size: 32px; margin-bottom: 24px;">Built for Automation</h2>
                    <p style="color: var(--slate); font-size: 18px; margin-bottom: 32px;">Portex isn't just a CLI; it's a
                        programmatic tool. Use our API or the headless agent to spin up tunnels automatically in your CI
                        workflows.</p>

                    <ul style="list-style: none;">
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Headless mode for CI runners</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>GitHub Actions & GitLab support</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Deterministic subdomains for automation</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <img src="/landing_cicd.png" alt="CI/CD Integration"
                        style="width: 100%; border-radius: 20px; border: 1px solid var(--border);">
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--light);">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 48px;">GitHub Actions Example</h2>
            <div
                style="max-width: 800px; margin: 0 auto; background: #1E293B; padding: 32px; border-radius: 20px; color: #E2E8F0; font-family: 'JetBrains Mono', monospace; font-size: 14px;">
                <pre style="margin: 0;">
<span style="color: #94a3b8;"># .github/workflows/e2e-tests.yml</span>
- name: Start Portex Tunnel
  run: |
    curl -fsSL https://portex.space/install.sh | bash
    portex start --port 8080 --subdomain ci-${GITHUB_RUN_ID} --token @{{ secrets.PORTEX_TOKEN }} &
    sleep 5
</pre>
            </div>
        </div>
    </section>
@endsection
