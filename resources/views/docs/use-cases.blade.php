@extends('components.layouts.docs')

@section('title', 'Use Cases & Examples')

@section('content')
    <h1>Use Cases</h1>
    <p>Portex is designed to solve real-world development challenges. Here are some common scenarios where Portex shines.
    </p>

    <h2>1. Webhook Development & Testing</h2>
    <p>Building integrations with Stripe, GitHub, or Twilio? These services need a public URL to send webhooks. Instead of
        deploying to staging every time you make a change, use Portex:</p>
    <div class="code-block">
        # Start your local server
        npm run dev

        # Expose it with Portex
        portex start --port 3000 --subdomain stripe-test
    </div>
    <p>Now configure your webhook URL as <code>https://stripe-test.portex.space/webhooks</code> and see requests in
        real-time.</p>

    <h2>2. Client Demos & Prototypes</h2>
    <p>Need to show a work-in-progress to a client? Don't waste time deploying to a staging server:</p>
    <div class="code-block">
        portex start --port 8080 --pin 1234
    </div>
    <p>Share the URL with your client along with the PIN. They can access your local environment securely without you
        exposing it to the entire internet.</p>

    <h2>3. Mobile App Development</h2>
    <p>Testing your backend API on a physical device? Portex generates a QR code you can scan:</p>
    <div class="code-block">
        portex start --port 5000
    </div>
    <p>Scan the QR code with your phone and your mobile app can now talk to your local API over HTTPS.</p>

    <h2>4. Static Site Sharing</h2>
    <p>Built a static site with Vite, Next.js, or Hugo? Share it instantly without configuring a web server:</p>
    <div class="code-block">
        npm run build
        portex share ./dist
    </div>
    <p>Portex starts a lightweight HTTP server and tunnels it automatically.</p>

    <h2>5. IoT & Embedded Development</h2>
    <p>Testing IoT devices that need to communicate with a cloud service? Point them to your Portex tunnel and debug
        locally:</p>
    <div class="code-block">
        portex start --port 8000 --subdomain iot-gateway
    </div>
    <p>Your devices can now send data to <code>https://iot-gateway.portex.space</code> while you iterate on your local code.
    </p>

    <h2>6. API Testing & Documentation</h2>
    <p>Writing API documentation with tools like Postman or Insomnia? Use Portex to expose your local API and share the
        collection with your team:</p>
    <div class="code-block">
        portex start --port 4000 --subdomain api-docs
    </div>

    <div class="callout">
        <span>Pro Tip</span>
        Combine Portex with tools like ngrok-alternative request inspection to debug complex integrations faster.
    </div>
@endsection
