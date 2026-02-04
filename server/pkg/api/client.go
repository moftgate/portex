package api

import (
	"bytes"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"net/http"
	"time"
)

type Client struct {
	baseURL string
	apiKey  string
	client  *http.Client
}

type Tunnel struct {
	ID           string   `json:"id"`
	Subdomain    string   `json:"subdomain"`
	CustomDomain string   `json:"custom_domain"`
	Protocol     string   `json:"protocol"`
	AgentID      string   `json:"agent_id"`
	AuthEnabled  bool     `json:"auth_enabled"`
	AuthUsername string   `json:"auth_username"`
	AuthPassword string   `json:"auth_password"`
	Pin          string   `json:"pin"`
	AllowedIPs   []string `json:"allowed_ips"`
}

func NewClient(baseURL, apiKey string) *Client {
	return &Client{
		baseURL: baseURL,
		apiKey:  apiKey,
		client: &http.Client{
			Timeout: 10 * time.Second,
		},
	}
}

func (c *Client) GetTunnel(tunnelID string) (*Tunnel, error) {
	req, err := http.NewRequest("GET", c.baseURL+"/api/server/tunnel/"+tunnelID, nil)
	if err != nil {
		return nil, err
	}

	req.Header.Set("Authorization", "Bearer "+c.apiKey)
	req.Header.Set("Content-Type", "application/json")

	resp, err := c.client.Do(req)
	if err != nil {
		return nil, err
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return nil, fmt.Errorf("failed to get tunnel: status %d", resp.StatusCode)
	}

	var result struct {
		Tunnel Tunnel `json:"tunnel"`
	}

	if err := json.NewDecoder(resp.Body).Decode(&result); err != nil {
		return nil, err
	}

	return &result.Tunnel, nil
}

func (c *Client) GetActiveTunnels() ([]Tunnel, error) {
	req, err := http.NewRequest("GET", c.baseURL+"/api/server/tunnels", nil)
	if err != nil {
		return nil, err
	}

	req.Header.Set("Authorization", "Bearer "+c.apiKey)
	req.Header.Set("Content-Type", "application/json")

	resp, err := c.client.Do(req)
	if err != nil {
		return nil, err
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return nil, fmt.Errorf("failed to get tunnels: status %d", resp.StatusCode)
	}

	var result struct {
		Tunnels []Tunnel `json:"tunnels"`
	}

	if err := json.NewDecoder(resp.Body).Decode(&result); err != nil {
		return nil, err
	}

	return result.Tunnels, nil
}

func (c *Client) LogRequest(
	tunnelID, method, path string,
	statusCode, responseTimeMs int,
	ipAddress, userAgent string,
	requestHeaders map[string]string,
	requestBody []byte,
	responseHeaders map[string]string,
	responseBody []byte,
	bytesUploaded, bytesDownloaded int,
) error {
	payload := map[string]interface{}{
		"method":           method,
		"path":             path,
		"status_code":      statusCode,
		"response_time_ms": responseTimeMs,
		"ip_address":       ipAddress,
		"user_agent":       userAgent,
		"request_headers":  requestHeaders,
		"request_body":     base64.StdEncoding.EncodeToString(requestBody),
		"response_headers": responseHeaders,
		"response_body":    base64.StdEncoding.EncodeToString(responseBody),
		"bytes_uploaded":   bytesUploaded,
		"bytes_downloaded": bytesDownloaded,
	}

	body, err := json.Marshal(payload)
	if err != nil {
		return err
	}

	req, err := http.NewRequest("POST", fmt.Sprintf("%s/api/server/tunnel/%s/request", c.baseURL, tunnelID), bytes.NewBuffer(body))
	if err != nil {
		return err
	}

	req.Header.Set("Authorization", "Bearer "+c.apiKey)
	req.Header.Set("Content-Type", "application/json")

	resp, err := c.client.Do(req)
	if err != nil {
		return err
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("failed to log request: status %d", resp.StatusCode)
	}

	return nil
}

func (c *Client) UpdateTunnelStatus(tunnelID, status string) error {
	payload := map[string]string{
		"status": status,
	}
	body, _ := json.Marshal(payload)

	req, err := http.NewRequest("PUT", fmt.Sprintf("%s/api/server/tunnel/%s/status", c.baseURL, tunnelID), bytes.NewBuffer(body))
	if err != nil {
		return err
	}

	req.Header.Set("Authorization", "Bearer "+c.apiKey)
	req.Header.Set("Content-Type", "application/json")

	resp, err := c.client.Do(req)
	if err != nil {
		return err
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("failed to update tunnel status: status %d", resp.StatusCode)
	}

	return nil
}

func (c *Client) LogBrowserLog(tunnelID, logType, message, url string) error {
	payload := map[string]interface{}{
		"type":    logType,
		"message": message,
		"url":     url,
	}

	body, err := json.Marshal(payload)
	if err != nil {
		return err
	}

	req, err := http.NewRequest("POST", fmt.Sprintf("%s/api/server/tunnel/%s/browser-log", c.baseURL, tunnelID), bytes.NewBuffer(body))
	if err != nil {
		return err
	}

	req.Header.Set("Authorization", "Bearer "+c.apiKey)
	req.Header.Set("Content-Type", "application/json")

	resp, err := c.client.Do(req)
	if err != nil {
		return err
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		return fmt.Errorf("failed to log browser log: status %d", resp.StatusCode)
	}

	return nil
}
