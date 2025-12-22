package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"strings"

	"portex/agent/pkg/config"
	"portex/agent/pkg/forwarder"

	"github.com/spf13/cobra"
)

var rootCmd = &cobra.Command{
	Use:   "portex",
	Short: "Portex - Expose your local services to the internet",
	Long:  `Portex is a secure tunnel client that exposes your local services to the internet.`,
}

// Auth command variables
var (
	apiKey    string
	apiSecret string
)

var authCmd = &cobra.Command{
	Use:   "auth",
	Short: "Link this agent to your Portex dashboard",
	Long:  `Automatically log in and link this agent to your Portex account in the dashboard.`,
	RunE: func(cmd *cobra.Command, args []string) error {
		// Load or register agent first
		cfg, err := config.Load("")
		if err != nil {
			fmt.Println("⚙️  No configuration found. Creating global identity...")
			reg, err := registerNewAgent()
			if err != nil {
				return fmt.Errorf("failed to register agent: %w", err)
			}
			cfg, _ = config.Load("") // Reload
			_ = reg                  // reg used for messages
		}

		loginURL := fmt.Sprintf("%s/panel/agents/claim?api_key=%s&api_secret=%s", cfg.Server.URL, cfg.Server.APIKey, cfg.Server.APISecret)

		fmt.Println("🔗 Opening browser for automatic login...")
		fmt.Printf("   If the browser doesn't open, please visit:\n   %s\n", loginURL)

		// Open browser
		var openErr error
		switch os.Getenv("GOOS") {
		case "windows":
			openErr = exec.Command("rundll32", "url.dll,FileProtocolHandler", loginURL).Start()
		case "darwin":
			openErr = exec.Command("open", loginURL).Start()
		default: // linux, bsd, etc
			openErr = exec.Command("xdg-open", loginURL).Start()
		}

		if openErr != nil {
			fmt.Printf("⚠️  Could not open browser: %v\n", openErr)
		}

		return nil
	},
}

// Start command variables
var (
	port      int
	subdomain string
)

type TunnelResponse struct {
	Tunnel struct {
		ID        string `json:"id"`
		Name      string `json:"name"`
		Subdomain string `json:"subdomain"`
		LocalPort int    `json:"local_port"`
		Protocol  string `json:"protocol"`
		PublicURL string `json:"public_url"`
		Status    string `json:"status"`
	} `json:"tunnel"`
	Message string `json:"message"`
}

type AgentRegistrationResponse struct {
	AgentID   string `json:"agent_id"`
	AgentName string `json:"agent_name"`
	APIKey    string `json:"api_key"`
	APISecret string `json:"api_secret"`
	Message   string `json:"message"`
}

func registerNewAgent() (*AgentRegistrationResponse, error) {
	fmt.Println("⚙️  First time running? Configuring your agent identity...")

	// Get server URL from environment or use default
	serverURL := os.Getenv("PORTEX_SERVER_URL")
	if serverURL == "" {
		// If we are developing locally, portex.space might not be the target
		// But for now we stick to a sensible default or env
		serverURL = "https://portex.space"
	}

	fmt.Printf("📡 Connecting to %s...\n", serverURL)

	req, _ := http.NewRequest("POST", serverURL+"/api/agent/register", nil)
	req.Header.Set("Accept", "application/json")
	resp, err := http.DefaultClient.Do(req)
	if err != nil {
		// If production fails and we haven't specified a URL, maybe try localhost as fallback
		if os.Getenv("PORTEX_SERVER_URL") == "" && serverURL != "http://localhost:8000" {
			serverURL = "http://localhost:8000"
			resp, err = http.Post(serverURL+"/api/agent/register", "application/json", nil)
		}

		if err != nil {
			return nil, fmt.Errorf("failed to reach Portex server: %w", err)
		}
	}
	defer resp.Body.Close()

	if resp.StatusCode != 201 {
		body, _ := io.ReadAll(resp.Body)
		return nil, fmt.Errorf("server rejected registration (status %d): %s", resp.StatusCode, string(body))
	}

	var regResp AgentRegistrationResponse
	if err := json.NewDecoder(resp.Body).Decode(&regResp); err != nil {
		return nil, fmt.Errorf("failed to decode response: %w", err)
	}

	// Determine WebSocket URL based on server URL
	wsURL := os.Getenv("PORTEX_WS_URL")
	if wsURL == "" {
		if strings.HasPrefix(serverURL, "https://") {
			wsURL = "wss://" + strings.TrimPrefix(serverURL, "https://") + "/ws"
		} else {
			// Handle http://localhost:8000 -> ws://localhost:8080/ws correctly
			// For local dev with Reverb, it's usually 8080
			host := strings.TrimPrefix(serverURL, "http://")
			if strings.Contains(host, "localhost:8000") {
				wsURL = "ws://localhost:8080/ws"
			} else {
				wsURL = "ws://" + host + "/ws"
			}
		}
	}

	// Save config
	cfg := &config.Config{}
	cfg.Server.APIKey = regResp.APIKey
	cfg.Server.APISecret = regResp.APISecret
	cfg.Server.URL = serverURL
	cfg.Server.WSURL = wsURL

	if err := config.Save(cfg, ""); err != nil {
		return nil, fmt.Errorf("failed to save config: %w", err)
	}

	fmt.Printf("✓ Agent registered and ready!\n")
	return &regResp, nil
}

var startCmd = &cobra.Command{
	Use:   "start",
	Short: "Start a tunnel",
	Long:  `Start a tunnel to expose your local service to the internet.`,
	RunE: func(cmd *cobra.Command, args []string) error {
		if port == 0 {
			return fmt.Errorf("--port is required")
		}

		fmt.Println("🚀 Starting Portex tunnel...")
		fmt.Printf("   Local port: %d\n", port)

		// Try to load config
		cfg, err := config.Load("")
		if err != nil {
			// Silently try to register
			reg, err := registerNewAgent()
			if err != nil {
				return fmt.Errorf("could not start without configuration: %w\nPlease check if the Portex server is running", err)
			}
			cfg = &config.Config{}
			cfg.Server.APIKey = reg.APIKey
			cfg.Server.APISecret = reg.APISecret
			cfg.Server.URL = os.Getenv("PORTEX_SERVER_URL")
			if cfg.Server.URL == "" {
				cfg.Server.URL = "https://portex.space"
			}
			// WS URL is handled inside the registerNewAgent or we can reload
			cfg, _ = config.Load("")
		}

		// Authenticate with server
		authReq := map[string]string{
			"api_key":    cfg.Server.APIKey,
			"api_secret": cfg.Server.APISecret,
		}
		authBody, _ := json.Marshal(authReq)

		req, _ := http.NewRequest("POST", cfg.Server.URL+"/api/agent/auth", bytes.NewBuffer(authBody))
		req.Header.Set("Content-Type", "application/json")
		req.Header.Set("Accept", "application/json")
		resp, err := http.DefaultClient.Do(req)
		if err != nil {
			return fmt.Errorf("failed to authenticate: %w", err)
		}

		// If credentials are invalid, re-register and try one more time
		if resp.StatusCode == 401 {
			resp.Body.Close()
			fmt.Println("⚠️  Stored credentials invalid, automatically re-registering...")

			regResp, err := registerNewAgent()
			if err != nil {
				return err
			}

			// Retry with new credentials
			cfg.Server.APIKey = regResp.APIKey
			cfg.Server.APISecret = regResp.APISecret

			authReq = map[string]string{
				"api_key":    cfg.Server.APIKey,
				"api_secret": cfg.Server.APISecret,
			}
			authBody, _ = json.Marshal(authReq)
			resp, err = http.Post(cfg.Server.URL+"/api/agent/auth", "application/json", bytes.NewBuffer(authBody))
			if err != nil {
				return fmt.Errorf("failed to authenticate after re-registration: %w", err)
			}
		}
		defer resp.Body.Close()

		if resp.StatusCode != 200 {
			body, _ := io.ReadAll(resp.Body)
			return fmt.Errorf("authentication failed (status %d): %s", resp.StatusCode, string(body))
		}

		fmt.Println("✓ Authenticated with server")

		// Create tunnel
		tunnelReq := map[string]interface{}{
			"local_port": port,
		}
		if subdomain != "" {
			tunnelReq["subdomain"] = subdomain
		}
		tunnelBody, _ := json.Marshal(tunnelReq)

		req, _ = http.NewRequest("POST", cfg.Server.URL+"/api/agent/tunnels", bytes.NewBuffer(tunnelBody))
		req.Header.Set("Content-Type", "application/json")
		req.Header.Set("Accept", "application/json")
		req.Header.Set("Authorization", fmt.Sprintf("Bearer %s:%s", cfg.Server.APIKey, cfg.Server.APISecret))

		resp, err = http.DefaultClient.Do(req)
		if err != nil {
			return fmt.Errorf("failed to create tunnel: %w", err)
		}
		defer resp.Body.Close()

		if resp.StatusCode != 201 {
			body, _ := io.ReadAll(resp.Body)
			return fmt.Errorf("failed to create tunnel: %s", string(body))
		}

		var tunnelResp TunnelResponse
		if err := json.NewDecoder(resp.Body).Decode(&tunnelResp); err != nil {
			return fmt.Errorf("failed to decode response: %w", err)
		}

		fmt.Println("✓ Tunnel created successfully!")
		fmt.Println()
		// NetBird style info box
		fmt.Println("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━")
		fmt.Printf("  Tunnel Name:   %s\n", tunnelResp.Tunnel.Name)
		fmt.Printf("  Public URL:    %s\n", tunnelResp.Tunnel.PublicURL)
		fmt.Printf("  Local Port:    %d\n", tunnelResp.Tunnel.LocalPort)
		fmt.Printf("  Protocol:      %s\n", tunnelResp.Tunnel.Protocol)
		fmt.Printf("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━")
		fmt.Println()

		// Start forwarder
		fmt.Println("🔗 Connecting to server relay...")
		forwarderInst := forwarder.New(
			port,
			cfg.Server.WSURL,
			tunnelResp.Tunnel.Subdomain,
			tunnelResp.Tunnel.ID,
		)

		if err := forwarderInst.Start(); err != nil {
			return fmt.Errorf("failed to start forwarder: %w", err)
		}

		fmt.Println()
		fmt.Println("✅ Tunnel is now active and forwarding traffic!")
		fmt.Println("   Press Ctrl+C to stop")
		fmt.Println()

		// Keep running
		select {}
	},
}

var logoutCmd = &cobra.Command{
	Use:   "logout",
	Short: "Remove stored credentials",
	Long:  `Remove the stored API credentials and reset the agent.`,
	RunE: func(cmd *cobra.Command, args []string) error {
		home, err := os.UserHomeDir()
		if err != nil {
			return fmt.Errorf("failed to get home directory: %w", err)
		}

		configPath := filepath.Join(home, ".portex", "config.yaml")

		if _, err := os.Stat(configPath); os.IsNotExist(err) {
			fmt.Println("✓ No configuration found")
			return nil
		}

		if err := os.Remove(configPath); err != nil {
			return fmt.Errorf("failed to remove config: %w", err)
		}

		fmt.Println("✓ Logged out. Configuration removed.")
		return nil
	},
}

func init() {
	// Auth command flags
	authCmd.Flags().StringVar(&apiKey, "api-key", "", "API key from Portex dashboard")
	authCmd.Flags().StringVar(&apiSecret, "api-secret", "", "API secret from Portex dashboard")
	authCmd.MarkFlagRequired("api-key")
	authCmd.MarkFlagRequired("api-secret")

	// Start command flags
	startCmd.Flags().IntVarP(&port, "port", "p", 0, "Local port to forward")
	startCmd.Flags().StringVarP(&subdomain, "subdomain", "s", "", "Custom subdomain (optional)")
	startCmd.MarkFlagRequired("port")

	// Add commands to root
	rootCmd.AddCommand(authCmd)
	rootCmd.AddCommand(startCmd)
	rootCmd.AddCommand(logoutCmd)
}

func main() {
	if err := rootCmd.Execute(); err != nil {
		fmt.Fprintln(os.Stderr, err)
		os.Exit(1)
	}
}
