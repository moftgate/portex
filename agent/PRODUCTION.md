# 🚀 Portex Agent - Production Ready!

## ✅ Agent Hazır!

Agent artık production sunucusuna bağlanmaya hazır. Otomatik olarak:
- HTTPS kullanıyor (`https://portex.space`)
- WebSocket Secure kullanıyor (`wss://portex.space/ws`)
- Environment variable'lardan config okuyor

## 📝 Kullanım

### Production'a Bağlan
```bash
PORTEX_SERVER_URL=https://portex.space ./portex start --port 8000
```

### Local Development
```bash
./portex start --port 8000  # Default: localhost
```

## 🔧 Sunucu Tarafı Gereksinimler

Agent'ın çalışması için sunucuda şunlar olmalı:

### 1. ✅ Go Server Çalışıyor Olmalı
```bash
# Sunucuda kontrol et
systemctl status portex-server

# Çalışmıyorsa başlat
systemctl start portex-server
```

### 2. ✅ Nginx WebSocket Config
`/etc/nginx/sites-available/portex.space` dosyasında `/ws` endpoint'i olmalı:

```nginx
location /ws {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    # ... diğer headerlar
}
```

### 3. ✅ SSL Sertifikası
Wildcard SSL sertifikası kurulu olmalı:
```bash
certbot certonly --manual --preferred-challenges dns \
  -d portex.space -d *.portex.space
```

## 🐛 Troubleshooting

### "websocket: bad handshake"
**Sebep:** Sunucuda Nginx WebSocket config'i yok veya yanlış

**Çözüm:**
```bash
# Sunucuda
sudo cp /path/to/nginx-portex.conf /etc/nginx/sites-available/portex.space
sudo nginx -t
sudo systemctl reload nginx
```

### "connection refused"
**Sebep:** Go server çalışmıyor

**Çözüm:**
```bash
# Sunucuda
sudo systemctl start portex-server
sudo journalctl -u portex-server -f
```

### "401 Unauthorized"
**Sebep:** API key yanlış veya agent kaydı yok

**Çözüm:**
```bash
# Local'de
./portex logout
PORTEX_SERVER_URL=https://portex.space ./portex start --port 8000
```

## 📦 Deployment Checklist

Sunucuya deploy etmek için:

- [ ] SSL sertifikası kuruldu
- [ ] Nginx config güncellendi (`nginx-portex.conf`)
- [ ] Go server deploy edildi (`/opt/portex/server/portex-server`)
- [ ] Go server çalışıyor (`systemctl status portex-server`)
- [ ] Laravel backend çalışıyor
- [ ] DNS kayıtları doğru (portex.space → 91.98.227.163)

## 🎯 Hızlı Test

```bash
# 1. Sunucu health check
curl https://portex.space/health

# 2. WebSocket test (wscat gerekli)
wscat -c wss://portex.space/ws

# 3. Agent başlat
PORTEX_SERVER_URL=https://portex.space ./portex start --port 8000
```

## 📚 Daha Fazla Bilgi

- Full deployment guide: `../DEPLOYMENT.md`
- Server setup: `../server/README.md`
- Nginx config: `../server/nginx-portex.conf`
