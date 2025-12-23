@extends('components.layouts.marketing')

@section('title', 'Static Site Hosting & Previews')

@section('content')
    <header class="hero">
        <div class="container">
            <div class="badge">Solutions</div>
            <h1>Instant Static Hosting <br>From Your Machine</h1>
            <p>No Nginx to configure. No S3 buckets to setup. Just point Portex to a folder and get a public URL for your
                local static site in seconds.</p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="/#guide" class="btn btn-primary">Start Sharing</a>
                <a href="{{ route('docs.commands') }}" class="btn btn-outline">CLI Docs</a>
            </div>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
                <div>
                    <img src="/landing_static.png" alt="Static Hosting"
                        style="width: 100%; border-radius: 20px; border: 1px solid var(--border);">
                </div>
                <div>
                    <h2 style="font-size: 32px; margin-bottom: 24px;">Simple, Secure, and Blazing Fast</h2>
                    <p style="color: var(--slate); font-size: 18px; margin-bottom: 32px;">Whether it's a documentation
                        build, a React app build folder, or just some raw HTML, Portex serves your files with built-in
                        compression and security.</p>

                    <ul style="list-style: none;">
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Auto-index for directory browsing</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Secure PIN protection for client demos</span>
                        </li>
                        <li style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                            <span style="color: var(--orange); font-weight: bold;">✓</span>
                            <span>Custom subdomains for branded links</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--light);">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 48px;">One command is all it takes</h2>
            <div
                style="max-width: 600px; margin: 0 auto; background: white; padding: 32px; border-radius: 20px; border: 1px solid var(--border);">
                <p
                    style="font-family: 'JetBrains Mono', monospace; font-size: 16px; color: var(--dark); margin-bottom: 12px;">
                    <span style="color: var(--slate);">$</span> portex share ./dist <span
                        style="color: var(--orange);">--subdomain</span> my-preview
                </p>
                <div style="border-top: 1px solid var(--border); padding-top: 16px; color: var(--slate); font-size: 14px;">
                    Success! Your folder is now live at:<br>
                    <a href="#"
                        style="color: var(--orange); text-decoration: none; font-weight: 600;">https://my-preview.portex.space</a>
                </div>
            </div>
        </div>
    </section>
@endsection
