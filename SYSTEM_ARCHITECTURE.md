# Portex - Tunnel Sistemi Nasıl Çalışır?

## 📋 İçindekiler

1. [Genel Bakış](#genel-bakış)
2. [Sistem Mimarisi](#sistem-mimarisi)
3. [Bileşenler](#bileşenler)
4. [Traffic Flow](#traffic-flow)
5. [WebSocket Protokolü](#websocket-protokolü)
6. [Domain Yapılandırması](#domain-yapılandırması)
7. [Deployment](#deployment)

---

## 🎯 Genel Bakış

Portex, ngrok ve bore gibi, yerel (localhost) uygulamalarınızı internet üzerinden erişilebilir hale getiren bir tunnel servisidir.

### Temel Konsept

```
localhost:3000  →  Agent  →  Server  →  https://abc123.portex.io
```

Kullanıcı `portex start --port 3000` komutunu çalıştırdığında:
1. Agent, server'a WebSocket ile bağlanır
2. Benzersiz bir subdomain alır (örn: `abc123.portex.io`)
3. Bu subdomain'e gelen tüm HTTP istekleri agent'a iletilir
4. Agent, istekleri `localhost:3000`'e forward eder
5. Yanıtlar aynı yoldan geri döner

---

## 🏗️ Sistem Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                         INTERNET                                 │
│                  (Kullanıcılar buradan erişir)                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ HTTPS Request
                              │ GET https://abc123.portex.io/api/users
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    DNS: *.portex.io                              │
│              Wildcard A Record → Server IP                       │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   PORTEX SERVER (Go)                             │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  1. HTTP Proxy Handler (Port 80/443)                   │     │
│  │     - Host header'dan subdomain çıkar                  │     │
│  │     - Subdomain → Agent mapping bul                    │     │
│  │     - İsteği WebSocket'e dönüştür                      │     │
│  └────────────────────────────────────────────────────────┘     │
│                              │                                   │
│                              │ WebSocket Message                 │
│                              ▼                                   │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  2. Tunnel Manager                                     │     │
│  │     - Agent connections map                            │     │
│  │     - Request/Response routing                         │     │
│  │     - Message queue management                         │     │
│  └────────────────────────────────────────────────────────┘     │
│                              │                                   │
│                              │ WebSocket                         │
│                              ▼                                   │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  3. WebSocket Server (Port 8080)                       │     │
│  │     - Agent authentication                             │     │
│  │     - Persistent connections                           │     │
│  │     - Bidirectional messaging                          │     │
│  └────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ WebSocket Connection
                              │ wss://portex.io/ws?subdomain=abc123
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   PORTEX AGENT (Go CLI)                          │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  1. WebSocket Client                                   │     │
│  │     - Server'a kalıcı bağlantı                         │     │
│  │     - HTTP request mesajları dinler                    │     │
│  │     - Response mesajları gönderir                      │     │
│  └────────────────────────────────────────────────────────┘     │
│                              │                                   │
│                              │ HTTP Request                      │
│                              ▼                                   │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  2. Forwarder                                          │     │
│  │     - WebSocket mesajını HTTP'ye dönüştür              │     │
│  │     - localhost'a istek at                             │     │
│  │     - Yanıtı WebSocket mesajına dönüştür              │     │
│  └────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ HTTP Request
                              │ GET http://localhost:3000/api/users
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│              LOCAL APPLICATION (localhost:3000)                  │
│                  (Kullanıcının uygulaması)                       │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔧 Bileşenler

### 1. Laravel Backend (API & Dashboard)

**Görevleri:**
- Kullanıcı yönetimi (authentication)
- Agent kayıt ve yönetimi
- Tunnel kayıt ve yönetimi
- Web dashboard (UI)

**Endpoint'ler:**
```
POST   /api/agent/auth          - Agent authentication
POST   /api/agent/tunnels       - Tunnel oluşturma
GET    /api/agent/tunnels       - Tunnel listesi
POST   /api/agent/heartbeat     - Agent heartbeat
```

**Database Schema:**
```sql
users
  - id (uuid)
  - name
  - email
  - password

agents
  - id (uuid)
  - user_id (uuid)
  - name
  - api_key
  - api_secret (hashed)
  - status (online/offline)
  - last_seen_at

tunnels
  - id (uuid)
  - user_id (uuid)
  - agent_id (uuid)
  - name
  - subdomain (unique)
  - local_port
  - protocol (http/https/tcp)
  - status (active/inactive)
  - public_url
```

### 2. Go Server (Tunnel Server)

**Görevleri:**
- HTTP proxy (subdomain routing)
- WebSocket server (agent connections)
- Request/Response forwarding

**Dosya Yapısı:**
```
server/
├── cmd/
│   └── server/
│       └── main.go              # Entry point
├── pkg/
│   ├── tunnel/
│   │   ├── manager.go           # Agent connection management
│   │   └── proxy.go             # HTTP proxy handler
│   ├── api/
│   │   └── client.go            # Backend API client
│   └── config/
│       └── config.go            # Configuration
└── go.mod
```

**Key Components:**

#### TunnelManager
```go
type TunnelManager struct {
    agents map[string]*AgentConnection  // subdomain -> agent
    mu     sync.RWMutex
}
```
- Agent bağlantılarını yönetir
- Subdomain → Agent mapping
- Thread-safe operations

#### AgentConnection
```go
type AgentConnection struct {
    TunnelID    string
    Subdomain   string
    Conn        *websocket.Conn
    Send        chan Message
    PendingReqs map[string]chan HTTPResponse
}
```
- Her agent için bir connection
- Send channel: Server → Agent mesajları
- PendingReqs: Request ID → Response channel mapping

#### ProxyHandler
```go
type ProxyHandler struct {
    manager *TunnelManager
    domain  string
}
```
- HTTP isteklerini yakalar
- Subdomain'i extract eder
- Agent'a forward eder

### 3. Go Agent (CLI)

**Görevleri:**
- Server'a WebSocket bağlantısı
- HTTP isteklerini localhost'a forward etme
- Yanıtları server'a geri gönderme

**Dosya Yapısı:**
```
agent/
├── cmd/
│   └── agent/
│       └── main.go              # CLI commands
├── pkg/
│   ├── config/
│   │   └── config.go            # Config management
│   └── forwarder/
│       └── forwarder.go         # Traffic forwarding
└── go.mod
```

**Commands:**
```bash
portex auth --api-key KEY --api-secret SECRET
portex start --port 3000 [--subdomain custom]
```

---

## 🔄 Traffic Flow (Detaylı)

### Senaryo: Kullanıcı `https://abc123.portex.io/api/users` adresine istek atıyor

#### 1️⃣ DNS Lookup
```
Kullanıcı → DNS Server
Query: abc123.portex.io nedir?

DNS Server → Kullanıcı
Answer: *.portex.io → 1.2.3.4 (Server IP)
```

#### 2️⃣ HTTP Request
```http
GET /api/users HTTP/1.1
Host: abc123.portex.io
User-Agent: Mozilla/5.0
Accept: application/json
```

#### 3️⃣ Server: Subdomain Extraction
```go
// server/pkg/tunnel/proxy.go
func (h *ProxyHandler) extractSubdomain(host string) string {
    // "abc123.portex.io" → "abc123"
    host = strings.TrimSuffix(host, "."+h.domain)
    return host
}
```

#### 4️⃣ Server: Agent Lookup
```go
agent := h.manager.GetAgent("abc123")
if agent == nil {
    return "Tunnel not found or offline"
}
```

#### 5️⃣ Server → Agent: WebSocket Message
```json
{
  "type": "http_request",
  "request_id": "550e8400-e29b-41d4-a716-446655440000",
  "data": {
    "method": "GET",
    "path": "/api/users",
    "headers": {
      "Host": "abc123.portex.io",
      "User-Agent": "Mozilla/5.0",
      "Accept": "application/json"
    },
    "body": null
  }
}
```

#### 6️⃣ Agent: Request Handling
```go
// agent/pkg/forwarder/forwarder.go
func (f *Forwarder) handleHTTPRequest(requestID string, data json.RawMessage) {
    // 1. Parse request
    var req HTTPRequest
    json.Unmarshal(data, &req)
    
    // 2. Forward to localhost
    localURL := fmt.Sprintf("http://localhost:%d%s", f.LocalPort, req.Path)
    httpReq, _ := http.NewRequest(req.Method, localURL, bytes.NewReader(req.Body))
    
    // 3. Copy headers
    for key, value := range req.Headers {
        httpReq.Header.Set(key, value)
    }
    
    // 4. Send request
    resp, _ := http.DefaultClient.Do(httpReq)
    
    // 5. Send response back
    f.sendHTTPResponse(requestID, resp)
}
```

#### 7️⃣ Agent → Server: WebSocket Response
```json
{
  "type": "http_response",
  "request_id": "550e8400-e29b-41d4-a716-446655440000",
  "data": {
    "status_code": 200,
    "headers": {
      "Content-Type": "application/json",
      "Content-Length": "42"
    },
    "body": "[{\"id\":1,\"name\":\"John\"},{\"id\":2,\"name\":\"Jane\"}]"
  }
}
```

#### 8️⃣ Server → User: HTTP Response
```http
HTTP/1.1 200 OK
Content-Type: application/json
Content-Length: 42

[{"id":1,"name":"John"},{"id":2,"name":"Jane"}]
```

### Timing Diagram

```
User          Server         Agent         localhost:3000
 │              │              │                 │
 │─GET /api────>│              │                 │
 │              │              │                 │
 │              │─WS Request──>│                 │
 │              │              │                 │
 │              │              │─HTTP GET───────>│
 │              │              │                 │
 │              │              │<─HTTP 200───────│
 │              │              │                 │
 │              │<─WS Response─│                 │
 │              │              │                 │
 │<─HTTP 200────│              │                 │
 │              │              │                 │
```

---

## 📡 WebSocket Protokolü

### Message Types

```go
const (
    MessageTypeHTTPRequest  = "http_request"
    MessageTypeHTTPResponse = "http_response"
    MessageTypePing         = "ping"
    MessageTypePong         = "pong"
)
```

### Message Format

```go
type Message struct {
    Type      string          `json:"type"`
    RequestID string          `json:"request_id,omitempty"`
    Data      json.RawMessage `json:"data,omitempty"`
}
```

### Connection Flow

```
Agent                                Server
  │                                    │
  │──WS Connect: /ws?subdomain=abc123─>│
  │                                    │
  │<─────────WS Upgrade (101)──────────│
  │                                    │
  │                                    │ ✓ Connected
  │                                    │
  │                                    │ HTTP Request geldi
  │<────http_request (req_id: 123)────│
  │                                    │
  │  (localhost'a forward et)          │
  │                                    │
  │────http_response (req_id: 123)───>│
  │                                    │
  │                                    │ (HTTP Response döndür)
  │                                    │
  │<──────────ping─────────────────────│
  │                                    │
  │──────────pong─────────────────────>│
  │                                    │
```

### Request/Response Matching

Server, her HTTP isteği için benzersiz bir `request_id` oluşturur:

```go
requestID := uuid.New().String()

// Request gönder
agent.Send <- Message{
    Type:      MessageTypeHTTPRequest,
    RequestID: requestID,
    Data:      requestData,
}

// Response bekle
respChan := make(chan HTTPResponse, 1)
agent.PendingReqs[requestID] = respChan
response := <-respChan  // Block until response
```

---

## 🌐 Domain Yapılandırması

### Development (Localhost)

Test için Host header kullanıyoruz:

```bash
curl -H "Host: abc123.portex.io" http://localhost:8080/
```

### Production Setup

#### 1. Domain Satın Al

- `portex.io` domain'ini bir registrar'dan satın al
- Örnek: Namecheap, GoDaddy, Cloudflare

#### 2. DNS Kayıtları

```dns
# A Record - Ana domain
portex.io.          A    1.2.3.4

# Wildcard A Record - Tüm subdomain'ler
*.portex.io.        A    1.2.3.4

# AAAA Record (IPv6 - opsiyonel)
*.portex.io.        AAAA 2001:db8::1
```

**Wildcard DNS Nasıl Çalışır?**

```
abc123.portex.io  → 1.2.3.4  ✓
xyz789.portex.io  → 1.2.3.4  ✓
test.portex.io    → 1.2.3.4  ✓
any.portex.io     → 1.2.3.4  ✓
```

Tüm subdomain'ler aynı IP'ye gider!

#### 3. SSL/TLS (HTTPS)

**Option A: Let's Encrypt (Otomatik)**

```go
import "golang.org/x/crypto/acme/autocert"

certManager := autocert.Manager{
    Prompt:     autocert.AcceptTOS,
    HostPolicy: autocert.HostWhitelist("portex.io", "*.portex.io"),
    Cache:      autocert.DirCache("/var/www/.cache"),
}

server := &http.Server{
    Addr: ":443",
    TLSConfig: &tls.Config{
        GetCertificate: certManager.GetCertificate,
    },
}

// HTTP → HTTPS redirect
go http.ListenAndServe(":80", certManager.HTTPHandler(nil))

// HTTPS server
server.ListenAndServeTLS("", "")
```

**Option B: Cloudflare (Proxy)**

1. Domain'i Cloudflare'e ekle
2. DNS kayıtlarını Cloudflare'e taşı
3. SSL/TLS → Full (strict)
4. Cloudflare otomatik SSL sağlar

**Avantajları:**
- Otomatik SSL
- DDoS protection
- CDN
- Rate limiting

---

## 🚀 Deployment

### Server Deployment (VPS)

#### 1. VPS Hazırlığı

```bash
# Ubuntu 22.04 LTS
ssh root@your-server-ip

# Go kurulumu
wget https://go.dev/dl/go1.21.0.linux-amd64.tar.gz
tar -C /usr/local -xzf go1.21.0.linux-amd64.tar.gz
echo 'export PATH=$PATH:/usr/local/go/bin' >> ~/.bashrc
source ~/.bashrc

# Portex kullanıcısı oluştur
useradd -m -s /bin/bash portex
mkdir -p /opt/portex
chown portex:portex /opt/portex
```

#### 2. Server Build & Deploy

```bash
# Local'de build
cd server
GOOS=linux GOARCH=amd64 go build -o portex-server cmd/server/main.go

# Server'a kopyala
scp portex-server portex@your-server-ip:/opt/portex/

# Config dosyası
scp config.yaml portex@your-server-ip:/opt/portex/
```

#### 3. Systemd Service

```bash
sudo cat > /etc/systemd/system/portex.service << 'EOF'
[Unit]
Description=Portex Tunnel Server
After=network.target

[Service]
Type=simple
User=portex
WorkingDirectory=/opt/portex
ExecStart=/opt/portex/portex-server
Restart=always
RestartSec=5

# Security
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=strict
ProtectHome=true
ReadWritePaths=/opt/portex

[Install]
WantedBy=multi-user.target
EOF

# Enable & start
sudo systemctl daemon-reload
sudo systemctl enable portex
sudo systemctl start portex

# Check status
sudo systemctl status portex
sudo journalctl -u portex -f
```

#### 4. Nginx Reverse Proxy (Opsiyonel)

```nginx
# /etc/nginx/sites-available/portex
server {
    listen 80;
    server_name *.portex.io portex.io;

    location / {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /ws {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
    }
}
```

### Docker Deployment

#### Dockerfile

```dockerfile
FROM golang:1.21-alpine AS builder

WORKDIR /app
COPY go.mod go.sum ./
RUN go mod download

COPY . .
RUN CGO_ENABLED=0 GOOS=linux go build -o portex-server cmd/server/main.go

FROM alpine:latest
RUN apk --no-cache add ca-certificates

WORKDIR /root/
COPY --from=builder /app/portex-server .

EXPOSE 80 8080

CMD ["./portex-server"]
```

#### docker-compose.yml

```yaml
version: '3.8'

services:
  portex-server:
    build: .
    ports:
      - "80:80"
      - "443:443"
      - "8080:8080"
    environment:
      - HTTP_PORT=80
      - WS_PORT=8080
      - BACKEND_URL=https://api.portex.io
      - SERVER_API_KEY=${SERVER_API_KEY}
      - DOMAIN=portex.io
    restart: unless-stopped
    volumes:
      - ./certs:/certs
```

```bash
# Deploy
docker-compose up -d

# Logs
docker-compose logs -f

# Update
docker-compose pull
docker-compose up -d
```

---

## 📊 Monitoring & Logging

### Health Check

```bash
curl http://localhost:8080/health
# {"status":"ok","time":"2025-12-21T20:00:00Z"}
```

### Metrics (Prometheus)

```go
// server/pkg/metrics/metrics.go
var (
    activeConnections = prometheus.NewGauge(prometheus.GaugeOpts{
        Name: "portex_active_connections",
        Help: "Number of active agent connections",
    })
    
    requestsTotal = prometheus.NewCounterVec(prometheus.CounterOpts{
        Name: "portex_requests_total",
        Help: "Total number of proxied requests",
    }, []string{"subdomain", "status"})
)
```

### Logging

```go
log.Printf("Agent registered: subdomain=%s tunnel_id=%s", subdomain, tunnelID)
log.Printf("Proxied %s %s → %s (status: %d)", method, path, subdomain, statusCode)
log.Printf("Agent disconnected: subdomain=%s", subdomain)
```

---

## 🔒 Security

### 1. Agent Authentication

```go
// API Key + Secret
Authorization: Bearer {api_key}:{api_secret}
```

### 2. Rate Limiting

```go
// Per subdomain
limiter := rate.NewLimiter(100, 1000) // 100 req/s, burst 1000
```

### 3. Request Validation

```go
// Max body size
http.MaxBytesReader(w, r.Body, 10*1024*1024) // 10MB
```

### 4. CORS

```go
w.Header().Set("Access-Control-Allow-Origin", "*")
w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE")
```

---

## 📈 Scalability

### Horizontal Scaling

```
                    Load Balancer
                         │
        ┌────────────────┼────────────────┐
        │                │                │
    Server 1         Server 2        Server 3
        │                │                │
        └────────────────┴────────────────┘
                         │
                    Redis (Shared State)
```

### Redis for Shared State

```go
// Store agent → server mapping
redis.Set("agent:abc123", "server1", 0)

// Lookup
server := redis.Get("agent:abc123")
// Forward to correct server
```

---

## 🎓 Özet

Portex sistemi 3 ana bileşenden oluşur:

1. **Laravel Backend**: Kullanıcı, agent ve tunnel yönetimi
2. **Go Server**: HTTP proxy + WebSocket server
3. **Go Agent**: CLI tool, localhost forwarding

**Temel Akış:**
```
Internet → DNS (*.portex.io) → Server → WebSocket → Agent → localhost
```

**Anahtar Teknolojiler:**
- WebSocket (persistent bidirectional communication)
- Wildcard DNS (dynamic subdomain routing)
- HTTP Proxy (request forwarding)
- UUID (unique request tracking)

**Production Checklist:**
- ✅ Domain satın al
- ✅ Wildcard DNS ayarla
- ✅ VPS/Cloud server
- ✅ SSL/TLS (Let's Encrypt)
- ✅ Systemd service
- ✅ Monitoring & logging
- ✅ Rate limiting
- ✅ Backup & recovery

---

**Daha fazla bilgi için:**
- [ngrok Documentation](https://ngrok.com/docs)
- [WebSocket RFC](https://tools.ietf.org/html/rfc6455)
- [Let's Encrypt](https://letsencrypt.org/)
