package tunnel

import (
	"io"
	"log"
	"net/http"
	"portex/server/pkg/api"
	"strings"
	"time"

	"github.com/google/uuid"
)

type ProxyHandler struct {
	manager   *TunnelManager
	apiClient *api.Client
	domain    string
}

func NewProxyHandler(manager *TunnelManager, apiClient *api.Client, domain string) *ProxyHandler {
	return &ProxyHandler{
		manager:   manager,
		apiClient: apiClient,
		domain:    domain,
	}
}

func (h *ProxyHandler) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	start := time.Now()

	// Extract subdomain from Host header
	host := r.Host
	subdomain := h.extractSubdomain(host)

	if subdomain == "" {
		http.Error(w, "Invalid subdomain", http.StatusBadRequest)
		return
	}

	// Get agent for this subdomain
	agent := h.manager.GetAgent(subdomain)
	if agent == nil {
		h.renderOfflinePage(w, subdomain)
		return
	}

	// Read request body
	body, err := io.ReadAll(r.Body)
	if err != nil {
		http.Error(w, "Failed to read request body", http.StatusInternalServerError)
		return
	}
	defer r.Body.Close()

	// Convert headers to map
	headers := make(map[string]string)
	for key, values := range r.Header {
		if len(values) > 0 {
			headers[key] = values[0]
		}
	}

	// Create HTTP request message
	httpReq := HTTPRequest{
		Method:  r.Method,
		Path:    r.URL.Path + "?" + r.URL.RawQuery,
		Headers: headers,
		Body:    body,
	}

	// Generate request ID
	requestID := uuid.New().String()

	// Send to agent and wait for response
	resp, err := agent.SendHTTPRequest(requestID, httpReq)
	if err != nil {
		http.Error(w, "Failed to forward request to agent", http.StatusBadGateway)
		return
	}

	// Write response headers
	for key, value := range resp.Headers {
		w.Header().Set(key, value)
	}

	// Write status code
	w.WriteHeader(resp.StatusCode)

	// Write response body
	w.Write(resp.Body)

	duration := time.Since(start)

	log.Printf("Proxied %s %s -> %s (status: %d, time: %v)", r.Method, r.URL.Path, subdomain, resp.StatusCode, duration)

	// Async log to backend
	go func() {
		err := h.apiClient.LogRequest(
			agent.TunnelID,
			r.Method,
			r.URL.Path,
			resp.StatusCode,
			int(duration.Milliseconds()),
			strings.Split(r.RemoteAddr, ":")[0],
			r.UserAgent(),
		)
		if err != nil {
			log.Printf("Failed to log request to backend: %v", err)
		}
	}()
}

func (h *ProxyHandler) extractSubdomain(host string) string {
	// Remove port if present
	if idx := strings.Index(host, ":"); idx != -1 {
		host = host[:idx]
	}

	// Check if it ends with our domain
	if !strings.HasSuffix(host, "."+h.domain) {
		return ""
	}

	// Extract subdomain
	subdomain := strings.TrimSuffix(host, "."+h.domain)
	return subdomain
}
