package main

import (
	"fmt"
	"log"
	"net/http"
	"portex/server/pkg/api"
	"portex/server/pkg/config"
	"portex/server/pkg/tunnel"
	"time"

	"github.com/gorilla/websocket"
	"github.com/joho/godotenv"
)

var upgrader = websocket.Upgrader{
	CheckOrigin: func(r *http.Request) bool {
		return true
	},
}

func main() {
	// Load .env file (try .env.production first, then .env)
	if err := godotenv.Load(".env.production"); err != nil {
		if err := godotenv.Load(); err != nil {
			log.Println("No .env file found, using environment variables")
		}
	}

	// Load configuration
	cfg := config.Load()

	// Initialize API client
	apiClient := api.NewClient(cfg.BackendURL, cfg.ServerAPIKey)

	// Initialize tunnel manager
	tunnelManager := tunnel.NewTunnelManager()

	// Test connection to backend
	log.Println("Connecting to backend at", cfg.BackendURL)
	tunnels, err := apiClient.GetActiveTunnels()
	if err != nil {
		log.Printf("Warning: Could not fetch tunnels from backend: %v", err)
	} else {
		log.Printf("Successfully connected to backend. Found %d active tunnels", len(tunnels))
	}

	// WebSocket endpoint for agents
	http.HandleFunc("/ws", func(w http.ResponseWriter, r *http.Request) {
		// Get tunnel info from query params
		subdomain := r.URL.Query().Get("subdomain")
		tunnelID := r.URL.Query().Get("tunnel_id")

		if subdomain == "" || tunnelID == "" {
			http.Error(w, "Missing subdomain or tunnel_id", http.StatusBadRequest)
			return
		}

		// Upgrade to WebSocket
		conn, err := upgrader.Upgrade(w, r, nil)
		if err != nil {
			log.Printf("Failed to upgrade connection: %v", err)
			return
		}

		// Fetch tunnel details from API to get the PIN and other info
		t, err := apiClient.GetTunnel(tunnelID)
		pin := ""
		if err == nil && t != nil {
			pin = t.Pin
		} else {
			log.Printf("Warning: Failed to fetch tunnel details for %s: %v", tunnelID, err)
		}

		// Register agent
		agent := tunnelManager.RegisterAgent(subdomain, tunnelID, pin, conn)

		// Start write pump in goroutine
		go agent.WritePump()

		// Run read pump in this goroutine (blocks until disconnect)
		agent.ReadPump()

		// Cleanup after disconnect
		tunnelManager.UnregisterAgent(subdomain)
	})

	// HTTP proxy for tunnel traffic
	proxyHandler := tunnel.NewProxyHandler(tunnelManager, apiClient, "portex.space")
	http.Handle("/", proxyHandler)

	// Health check
	http.HandleFunc("/health", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		fmt.Fprintf(w, `{"status":"ok","time":"%s"}`, time.Now().Format(time.RFC3339))
	})

	log.Printf("Starting Portex Server on port %s", cfg.HTTPPort)
	log.Printf("WebSocket endpoint: ws://localhost:%s/ws", cfg.HTTPPort)
	log.Printf("Health check: http://localhost:%s/health", cfg.HTTPPort)

	if err := http.ListenAndServe(":"+cfg.HTTPPort, nil); err != nil {
		log.Fatal("Server failed to start:", err)
	}
}
