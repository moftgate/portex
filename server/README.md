# Portex Server - Quick Start

## ✅ Kurulum Tamamlandı!

Go server artık `.env.production` dosyasını okuyabiliyor ve Laravel backend ile iletişim kurabiliyor.

## 🔑 Önemli Bilgiler

### API Key
```
PORTEX_SERVER_API_KEY=e4bd53f46783383b2e6b73b9aeebbc3664ac45b2f494a3c3ac0d61720b8762ed
```

Bu key hem Laravel `.env` hem de `server/.env.production` dosyalarında aynı olmalı!

## 🚀 Local Test

```bash
cd server

# Build
go build -o portex-server cmd/server/main.go

# Run
./portex-server
```

Çıktı:
```
2025/12/22 15:19:09 Connecting to backend at http://localhost:8000
2025/12/22 15:19:09 Successfully connected to backend. Found 0 active tunnels
2025/12/22 15:19:09 Starting Portex Server on port 8080
2025/12/22 15:19:09 WebSocket endpoint: ws://localhost:8080/ws
2025/12/22 15:19:09 Health check: http://localhost:8080/health
```

Health check:
```bash
curl http://localhost:8080/health
# {"status":"ok","time":"2025-12-22T15:19:09+03:00"}
```

## 📦 Production Deployment

### Sunucuda (91.98.227.163):

```bash
# 1. .env.production oluştur
sudo nano /opt/portex/server/.env.production
```

```env
BACKEND_URL=https://portex.space
SERVER_API_KEY=e4bd53f46783383b2e6b73b9aeebbc3664ac45b2f494a3c3ac0d61720b8762ed
HTTP_PORT=8080
HTTPS_PORT=8443
```

```bash
# 2. Service başlat
sudo systemctl start portex-server
sudo systemctl status portex-server

# 3. Logs
sudo journalctl -u portex-server -f
```

## 🔧 Troubleshooting

### "Connection refused" hatası
- Laravel backend çalışıyor mu? `php artisan serve`
- Port doğru mu? `.env.production` kontrol et

### "401 Unauthorized" hatası
- API key'ler eşleşiyor mu?
  - Laravel: `grep PORTEX_SERVER_API_KEY .env`
  - Go Server: `cat server/.env.production`
- Config cache temizle: `php artisan config:clear`

### "Address already in use" hatası
```bash
# Port 8080'i kullanan process'i bul
lsof -ti:8080

# Durdur
kill $(lsof -ti:8080)
```

## 📝 Environment Files

### Laravel (.env)
```env
PORTEX_SERVER_API_KEY=e4bd53f46783383b2e6b73b9aeebbc3664ac45b2f494a3c3ac0d61720b8762ed
PORTEX_TUNNEL_DOMAIN=portex.space
PORTEX_SERVER_URL=http://localhost:8080
PORTEX_WEBSOCKET_URL=ws://localhost:8080/ws
```

### Go Server (server/.env.production)
```env
BACKEND_URL=http://localhost:8000  # Production: https://portex.space
SERVER_API_KEY=e4bd53f46783383b2e6b73b9aeebbc3664ac45b2f494a3c3ac0d61720b8762ed
HTTP_PORT=8080
HTTPS_PORT=8443
```

## 🎯 Next Steps

1. ✅ Go server `.env` desteği eklendi
2. ✅ API key yapılandırıldı
3. ✅ Local test başarılı
4. 🔜 Production deployment (DEPLOYMENT.md'ye bakın)
5. 🔜 SSL sertifikası kurulumu
6. 🔜 Nginx reverse proxy
