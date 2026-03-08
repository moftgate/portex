# Portex - ngrok/bore/localtunnel Alternative

Portex is a self-hosted tunnel service that allows you to expose local services to the internet through a central server infrastructure.

## Features

- 🚀 **Fast & Lightweight**: Built with Go for high performance
- 🔒 **Secure**: End-to-end encryption with optional basic authentication
- 🎯 **Easy to Use**: Simple CLI for agents, beautiful web dashboard
- 📊 **Analytics**: Track requests, response times, and tunnel usage
- 🔄 **Auto-Reconnect**: Agents automatically reconnect on connection loss
- 🌐 **Custom Domains**: Support for custom subdomains and domains
- 🏠 **Host Rewriting**: Override local Host headers with `--host-header`

## Architecture

- **Laravel Backend**: User management, tunnel configuration, API, and dashboard
- **Go Server**: High-performance proxy and tunnel router
- **Go Agent**: Lightweight client for local port forwarding

## Quick Start

### Prerequisites

- PHP 8.2+
- PostgreSQL 15+
- Go 1.21+
- Node.js 18+

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/portex.git
cd portex
```

2. **Install Laravel dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Edit `.env` and set your PostgreSQL credentials:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=portex
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Start development servers**
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite
npm run dev

# Terminal 3: Go Server (coming soon)
cd server && go run main.go
```

## Usage

### Web Dashboard

1. Visit `http://localhost:8000`
2. Register an account
3. Create a new agent and save the API credentials
4. Create a tunnel and assign it to your agent

### Agent

1. **Install the agent** (coming soon)
```bash
# Download pre-built binary
curl -L https://portex.io/download/agent -o portex
chmod +x portex
```

2. **Authenticate**
```bash
portex auth --api-key YOUR_API_KEY --api-secret YOUR_API_SECRET
```

3. **Start a tunnel**
```bash
portex start --port 3000 --subdomain myapp
```

Your local service on port 3000 will be available at `https://myapp.portex.io`

## Configuration

### Environment Variables

```bash
# Portex Configuration
PORTEX_TUNNEL_DOMAIN=portex.local
PORTEX_SERVER_URL=http://localhost:8080
PORTEX_WEBSOCKET_URL=ws://localhost:8080/ws
PORTEX_SERVER_API_KEY=your_server_api_key
```

## Development Status

- ✅ Database Schema & Models
- ✅ Backend API & Services
- ✅ Frontend Dashboard (MaryUI + Livewire)
- 🚧 Go Server Component (in progress)
- 🚧 Go Agent Component (in progress)
- ⏳ Integration & Testing
- ⏳ Documentation

## Tech Stack

- **Backend**: Laravel 12, PostgreSQL 15
- **Frontend**: Livewire Volt, MaryUI, TailwindCSS
- **Server**: Go 1.21+
- **Agent**: Go 1.21+

## License

MIT License

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
