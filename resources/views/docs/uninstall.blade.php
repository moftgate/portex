@extends('components.layouts.docs')

@section('title', 'Uninstall Guide')

@section('content')
    <h1>Uninstalling Portex</h1>
    <p>If you need to remove Portex from your system, follow the steps below based on your operating system. We recommend
        logging out first to ensure your active credentials are removed from your machine.</p>

    <h2>1. Logout (Recommended)</h2>
    <p>Before deleting the binary, run the logout command to remove your stored API credentials and session data.</p>
    <div class="code-block">
        portex logout
    </div>

    <h2>2. Remove the Binary</h2>
    <p>Removing the Portex executable depends on how you installed it.</p>

    <div class="code-header">macOS & Linux</div>
    <div class="code-block">
        sudo rm /usr/local/bin/portex
    </div>

    <div class="code-header">Windows (PowerShell)</div>
    <div class="code-block">
        Remove-Item "$env:USERPROFILE\.portex\bin\portex.exe"
    </div>

    <h2>3. Clean up Configuration</h2>
    <p>Portex stores configuration and logs in a hidden directory in your home folder. You can remove this directory to
        completely wipe Portex data.</p>

    <div class="code-header">macOS & Linux</div>
    <div class="code-block">
        rm -rf ~/.portex
    </div>

    <div class="code-header">Windows (PowerShell)</div>
    <div class="code-block">
        Remove-Item -Recurse -Force "$env:USERPROFILE\.portex"
    </div>

    <div class="callout">
        <span>Thinking of leaving?</span>
        We'd love to hear why Portex didn't work for you. Feel free to open an issue on our GitHub or reach out to our team
        with feedback.
    </div>
@endsection
