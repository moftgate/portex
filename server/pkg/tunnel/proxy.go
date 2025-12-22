package tunnel

import (
	"io"
	"log"
	"net/http"
	"strings"

	"github.com/google/uuid"
)

type ProxyHandler struct {
	manager *TunnelManager
	domain  string
}

func NewProxyHandler(manager *TunnelManager, domain string) *ProxyHandler {
	return &ProxyHandler{
		manager: manager,
		domain:  domain,
	}
}

func (h *ProxyHandler) ServeHTTP(w http.ResponseWriter, r *http.Request) {
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
		http.Error(w, "Tunnel not found or offline", http.StatusNotFound)
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

	log.Printf("Proxied %s %s -> %s (status: %d)", r.Method, r.URL.Path, subdomain, resp.StatusCode)
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
