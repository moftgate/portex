package config

import (
	"os"
)

type Config struct {
	BackendURL    string
	ServerAPIKey  string
	HTTPPort      string
	HTTPSPort     string
	WebSocketPort string
	TLSCertFile   string
	TLSKeyFile    string
}

func Load() *Config {
	return &Config{
		BackendURL:    getEnv("BACKEND_URL", "http://localhost:8000"),
		ServerAPIKey:  getEnv("SERVER_API_KEY", ""),
		HTTPPort:      getEnv("HTTP_PORT", "8080"),
		HTTPSPort:     getEnv("HTTPS_PORT", "8443"),
		WebSocketPort: getEnv("WEBSOCKET_PORT", "8080"),
		TLSCertFile:   getEnv("TLS_CERT_FILE", ""),
		TLSKeyFile:    getEnv("TLS_KEY_FILE", ""),
	}
}

func getEnv(key, defaultValue string) string {
	if value := os.Getenv(key); value != "" {
		return value
	}
	return defaultValue
}
