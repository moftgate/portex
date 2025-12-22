# 🚀 Portex Production Deployment Guide

## Ön Gereksinimler

- Ubuntu/Debian Linux sunucu (91.98.227.163)
- Root veya sudo erişimi
- Domain: portex.space (DNS kayıtları hazır ✅)

## 1. Sunucu Hazırlığı

```bash
# Sistemi güncelle
sudo apt update && sudo apt upgrade -y

# Gerekli paketleri kur
sudo apt install -y nginx certbot python3-certbot-nginx \
    postgresql-15 redis-server git curl wget

# PHP 8.4 kur
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.4-fpm php8.4-cli php8.4-pgsql \
    php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip \
    php8.4-bcmath php8.4-redis php8.4-intl

# Composer kur
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js kur (Laravel Vite için)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

## 2. SSL Sertifikası Al

```bash
# Wildcard sertifika için DNS challenge
sudo certbot certonly --manual --preferred-challenges dns \
  -d portex.space -d *.portex.space

# Certbot'un söylediği TXT kaydını DNS'e ekle:
# _acme-challenge.portex.space TXT "random-string-here"

# DNS propagation bekle (1-2 dakika)
# Sonra Enter'a bas
```

## 3. Laravel Backend Deploy

```bash
# Proje dizini oluştur
sudo mkdir -p /var/www/portex
sudo chown -R $USER:www-data /var/www/portex

# Kodu kopyala (local'den)
cd /var/www/portex
git clone <your-repo> .
# VEYA
rsync -avz --exclude 'node_modules' --exclude 'vendor' \
  /Users/aras/projects/portex/ user@91.98.227.163:/var/www/portex/

# Dependencies kur
composer install --no-dev --optimize-autoloader
npm install && npm run build

# .env dosyasını düzenle
cp .env.example .env
nano .env
```

### .env Ayarları (Production)

```env
APP_NAME=Portex
APP_ENV=production
APP_DEBUG=false
APP_URL=https://portex.space

TUNNEL_DOMAIN=portex.space

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=portex
DB_USERNAME=portex
DB_PASSWORD=<güçlü-şifre>

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SESSION_DOMAIN=.portex.space
SANCTUM_STATEFUL_DOMAINS=portex.space,*.portex.space
```

```bash
# Laravel setup
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# İzinleri ayarla
sudo chown -R www-data:www-data /var/www/portex/storage
sudo chown -R www-data:www-data /var/www/portex/bootstrap/cache
sudo chmod -R 775 /var/www/portex/storage
sudo chmod -R 775 /var/www/portex/bootstrap/cache
```

## 4. Go Server Deploy

```bash
# Server dizinine git
cd /Users/aras/projects/portex/server

# Deploy script'i çalıştır (local'den)
./deploy.sh

# VEYA manuel:
GOOS=linux GOARCH=amd64 go build -o portex-server cmd/server/main.go
scp portex-server user@91.98.227.163:/opt/portex/server/
```

### Sunucuda:

```bash
# .env.production oluştur
sudo nano /opt/portex/server/.env.production
```

```env
BACKEND_URL=https://portex.space
SERVER_API_KEY=<laravel-backend-api-key>
HTTP_PORT=8080
HTTPS_PORT=8443
```

```bash
# Systemd service kur
sudo cp /opt/portex/server/portex-server.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable portex-server
sudo systemctl start portex-server
sudo systemctl status portex-server
```

## 5. Nginx Konfigürasyonu

```bash
# Config dosyasını kopyala
sudo cp /path/to/nginx-portex.conf /etc/nginx/sites-available/portex.space

# Symlink oluştur
sudo ln -s /etc/nginx/sites-available/portex.space /etc/nginx/sites-enabled/

# Default site'ı kaldır
sudo rm /etc/nginx/sites-enabled/default

# Test et
sudo nginx -t

# Reload
sudo systemctl reload nginx
```

## 6. PostgreSQL Database Oluştur

```bash
sudo -u postgres psql

CREATE DATABASE portex;
CREATE USER portex WITH PASSWORD 'güçlü-şifre';
GRANT ALL PRIVILEGES ON DATABASE portex TO portex;
\q
```

## 7. Firewall Ayarları

```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

## 8. Test Et

```bash
# Backend health check
curl https://portex.space/health

# Go server health check
curl http://localhost:8080/health

# Logs
sudo journalctl -u portex-server -f
sudo tail -f /var/log/nginx/portex-*.log
```

## 9. Agent Konfigürasyonu

Local agent'ları production'a bağlamak için:

```bash
# Agent config'i güncelle
nano ~/.portex/config.yaml
```

```yaml
server:
  url: https://portex.space
  api_key: "your-api-key"
  api_secret: "your-api-secret"
```

## 10. Monitoring & Maintenance

```bash
# Server status
sudo systemctl status portex-server

# Restart
sudo systemctl restart portex-server

# Logs
sudo journalctl -u portex-server --since "1 hour ago"

# Laravel logs
tail -f /var/www/portex/storage/logs/laravel.log

# SSL renewal (otomatik)
sudo certbot renew --dry-run
```

## Troubleshooting

### Go Server bağlanamıyor
```bash
# Port dinliyor mu?
sudo netstat -tlnp | grep 8080

# Firewall?
sudo ufw status

# Logs
sudo journalctl -u portex-server -n 50
```

### Laravel 500 hatası
```bash
# Permissions
sudo chown -R www-data:www-data /var/www/portex/storage

# Cache temizle
cd /var/www/portex
php artisan cache:clear
php artisan config:clear
```

### SSL sertifika hatası
```bash
# Sertifika geçerli mi?
sudo certbot certificates

# Yenile
sudo certbot renew --force-renewal
```

## 🎉 Tamamlandı!

Artık portex.space production'da çalışıyor!

- Dashboard: https://portex.space
- Tunnels: https://{subdomain}.portex.space
- API: https://portex.space/api
