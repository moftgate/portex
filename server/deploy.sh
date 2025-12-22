#!/bin/bash
set -e

echo "🚀 Portex Server Deployment Script"
echo "===================================="

# Configuration
DEPLOY_USER="www-data"
DEPLOY_PATH="/opt/portex/server"
SERVICE_NAME="portex-server"

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}1. Installing dependencies...${NC}"
cd "$(dirname "$0")"
go mod tidy

echo -e "${BLUE}2. Building server binary...${NC}"
GOOS=linux GOARCH=amd64 go build -o portex-server cmd/server/main.go

echo -e "${BLUE}3. Creating deployment directory...${NC}"
sudo mkdir -p $DEPLOY_PATH
sudo chown $DEPLOY_USER:$DEPLOY_USER $DEPLOY_PATH

echo -e "${BLUE}3. Copying files...${NC}"
sudo cp portex-server $DEPLOY_PATH/
sudo cp .env.example $DEPLOY_PATH/.env.production.example

echo -e "${BLUE}4. Setting permissions...${NC}"
sudo chmod +x $DEPLOY_PATH/portex-server
sudo chown -R $DEPLOY_USER:$DEPLOY_USER $DEPLOY_PATH

echo -e "${BLUE}5. Installing systemd service...${NC}"
sudo cp portex-server.service /etc/systemd/system/
sudo systemctl daemon-reload

echo -e "${GREEN}✅ Deployment complete!${NC}"
echo ""
echo "Next steps:"
echo "1. Edit /opt/portex/server/.env.production with your configuration"
echo "2. sudo systemctl enable $SERVICE_NAME"
echo "3. sudo systemctl start $SERVICE_NAME"
echo "4. sudo systemctl status $SERVICE_NAME"
