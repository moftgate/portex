#!/bin/bash
set -e

SERVER="root@91.98.227.163"
PROJECT_DIR="/Users/aras/projects/portex"

echo "🚀 Portex Production Deployment"
echo "================================"
echo ""

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}Step 1: Building Go Server...${NC}"
cd "$PROJECT_DIR/server"
GOOS=linux GOARCH=amd64 go build -o portex-server cmd/server/main.go
echo -e "${GREEN}✓ Go server built${NC}"

echo ""
echo -e "${BLUE}Step 2: Uploading files to server...${NC}"

# Upload Go server
scp portex-server $SERVER:/tmp/
scp portex-server.service $SERVER:/tmp/
scp nginx-portex.conf $SERVER:/tmp/
echo -e "${GREEN}✓ Go server files uploaded${NC}"

# Upload Laravel backend
echo -e "${YELLOW}Uploading Laravel backend (this may take a while)...${NC}"
cd "$PROJECT_DIR"
rsync -avz --exclude 'node_modules' --exclude 'vendor' \
  --exclude '.git' --exclude 'storage/logs/*' --exclude 'server' \
  ./ $SERVER:/var/www/portex/
echo -e "${GREEN}✓ Laravel backend uploaded${NC}"

echo ""
echo -e "${BLUE}Step 3: Installing on server...${NC}"

ssh $SERVER << 'ENDSSH'
set -e

echo "📦 Installing Go Server..."
mkdir -p /opt/portex/server
mv /tmp/portex-server /opt/portex/server/
chmod +x /opt/portex/server/portex-server

# Create .env.production
cat > /opt/portex/server/.env.production << 'EOF'
BACKEND_URL=http://localhost:8000
SERVER_API_KEY=e4bd53f46783383b2e6b73b9aeebbc3664ac45b2f494a3c3ac0d61720b8762ed
HTTP_PORT=8080
HTTPS_PORT=8443
EOF

# Install systemd service
mv /tmp/portex-server.service /etc/systemd/system/
systemctl daemon-reload
systemctl enable portex-server

echo "🌐 Configuring Nginx..."
cp /tmp/nginx-portex.conf /etc/nginx/sites-available/portex.space
ln -sf /etc/nginx/sites-available/portex.space /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

echo "📦 Installing Laravel dependencies..."
cd /var/www/portex
composer install --no-dev --optimize-autoloader
npm install
npm run build

echo "⚙️  Configuring Laravel..."
# Note: .env should be manually configured first!
if [ ! -f .env ]; then
    echo "⚠️  WARNING: .env file not found. Copy .env.example and configure it!"
    cp .env.example .env
fi

php artisan key:generate --force || true
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔒 Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "🚀 Starting services..."
systemctl start portex-server
nginx -t && systemctl reload nginx

echo ""
echo "✅ Deployment complete!"
echo ""
echo "Next steps:"
echo "1. Configure /var/www/portex/.env with database credentials"
echo "2. Run: php artisan migrate --force"
echo "3. Install SSL: certbot certonly --manual --preferred-challenges dns -d portex.space -d *.portex.space"
echo "4. Check status: systemctl status portex-server"
ENDSSH

echo ""
echo -e "${GREEN}✅ Deployment completed!${NC}"
echo ""
echo "🔗 Next: Update your local agent config to use production:"
echo "   nano ~/.portex/config.yaml"
echo ""
echo "   Change:"
echo "   url: https://portex.space"
echo "   ws_url: wss://portex.space/ws"
