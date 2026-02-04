@extends('components.layouts.docs')

@section('title', 'Installation Guide')

@section('content')
    <h1>Installation</h1>
    <p>The Portex agent is a standalone binary. You can install it using our automated scripts which handle OS detection,
        architecture matching, and path configuration.</p>

    <h2>Automatic Installation</h2>

    <div class="code-header">macOS & Linux</div>
    <div class="code-block">
        curl -fsSL https://portex.space/install.sh | bash
    </div>
    <p>This script downloads the correct binary for your system (Intel/Apple Silicon or x64 Linux) and moves it to
        <code>/usr/local/bin</code>.
    </p>

    <div class="code-header">Windows (PowerShell Admin)</div>
    <div class="code-block">
        iwr https://portex.space/install.ps1 | iex
    </div>
    <p>This will install Portex to <code>%USERPROFILE%\.portex\bin</code> and add it to your System PATH.</p>

    <h2>Manual Downloads</h2>
    <p>If you prefer to manage the binary yourself, you can download them directly from the homepage "Install" section for
        your specific platform.</p>

    <h2>Verification</h2>
    <p>Once installed, verify the installation by checking the version:</p>
    <div class="code-block">
        portex version
        <span style="color: #64748b; font-weight: normal; margin-top: 8px; display: block; opacity: 0.7;"># Output: Portex
            version 0.7.1</span>
    </div>

    <div class="callout">
        <span>Upgrade note</span>
        To upgrade Portex, simply re-run the installation script. It will overwrite the old binary with the latest stable
        version.
    </div>
@endsection
