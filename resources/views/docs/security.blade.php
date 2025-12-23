@extends('docs.layout')

@section('title', 'Security & Authentication')

@section('content')
    <h1>Security Principles</h1>
    <p>Portex is built with security as a priority. We ensure that your local environment remains isolated while providing
        the connectivity you need.</p>

    <h2>End-to-End Encryption</h2>
    <p>All traffic passing through Portex tunnels is encrypted using TLS. Your data is protected from the moment it leaves
        your agent until it reaches the visitor's browser.</p>

    <h2>PIN Protection</h2>
    <p>One of our unique features is the ability to lock a tunnel with a PIN. When you use the <code>--pin</code> flag, the
        Portex Gateway will intercept all requests and present a challenge page.</p>

    <div class="code-block">
        portex start --port 3000 --pin 1234
    </div>

    <p>This is especially useful for:</p>
    <ul>
        <li>Sharing sensitive work-in-progress with clients.</li>
        <li>Protecting internal staging environments.</li>
        <li>Preventing search engine crawlers from indexing your dev site.</li>
    </ul>

    <h2>Agent Authentication</h2>
    <p>While Portex works without an account, logging in allows you to:</p>
    <ul>
        <li>Keep your custom subdomains reserved.</li>
        <li>View historical request logs in the dashboard.</li>
        <li>Manage multiple agents from a single interface.</li>
    </ul>

    <div class="callout">
        <span>Trust</span>
        We do not store your local data. We only facilitate the passage of traffic through the encrypted tunnel.
    </div>
@endsection
