package tunnel

import (
	"fmt"
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

	// Check for PIN protection
	if agent.Pin != "" {
		// Check for access cookie
		cookieName := "portex_access_" + agent.TunnelID
		cookie, err := r.Cookie(cookieName)

		// Expected value is "pin:session_token"
		expectedValue := agent.Pin + ":" + agent.SessionToken

		// If no cookie or cookie value doesn't match PIN+Session, redirect to entry page
		if err != nil || cookie.Value != expectedValue {
			// Redirect to PIN entry page on the main domain
			pinUrl := fmt.Sprintf("https://%s/tunnels/pin/%s?session_token=%s&redirect_to=%s",
				h.domain,
				agent.TunnelID,
				agent.SessionToken,
				"https://"+r.Host+r.URL.Path)
			http.Redirect(w, r, pinUrl, http.StatusFound)
			return
		}
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

	// Check if local service is unreachable
	if resp.StatusCode == http.StatusBadGateway &&
		string(resp.Body) == "Failed to connect to local service" {
		h.renderLocalServiceError(w, subdomain)
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

	//log.Printf("Proxied %s %s -> %s (status: %d, time: %v)", r.Method, r.URL.Path, subdomain, resp.StatusCode, duration)

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
			headers,        // request_headers
			body,           // request_body
			resp.Headers,   // response_headers
			resp.Body,      // response_body
			len(body),      // bytes_uploaded
			len(resp.Body), // bytes_downloaded
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

func (h *ProxyHandler) renderOfflinePage(w http.ResponseWriter, subdomain string) {
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	w.WriteHeader(http.StatusServiceUnavailable)

	html := `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunnel Offline - Portex</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #F97316 0%, #FB923C 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.3);
        }

        .icon svg {
            width: 40px;
            height: 40px;
            stroke: white;
            stroke-width: 2;
            fill: none;
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .subdomain {
            font-family: 'SF Mono', 'Monaco', 'Courier New', monospace;
            color: #F97316;
            font-weight: 600;
            font-size: 18px;
            background: #FFF7ED;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 24px;
        }

        p {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .reasons {
            background: #f9fafb;
            border-radius: 16px;
            padding: 24px;
            margin: 32px 0;
            text-align: left;
        }

        .reasons h3 {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .reasons ul {
            list-style: none;
        }

        .reasons li {
            font-size: 14px;
            color: #6b7280;
            padding: 8px 0;
            padding-left: 28px;
            position: relative;
        }

        .reasons li:before {
            content: "•";
            position: absolute;
            left: 12px;
            color: #F97316;
            font-weight: bold;
            font-size: 18px;
        }

        .cta {
            display: inline-block;
            background: linear-gradient(135deg, #F97316 0%, #FB923C 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
        }

        .footer {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            font-size: 13px;
            color: #9ca3af;
        }

        @media (max-width: 640px) {
            .container {
                padding: 40px 24px;
            }

            h1 {
                font-size: 24px;
            }

            .subdomain {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1>Tunnel Offline</h1>

        <div class="subdomain">` + subdomain + `.` + h.domain + `</div>

        <p>This tunnel is currently not available. The agent may be offline or the tunnel has been stopped.</p>

        <div class="reasons">
            <h3>Possible Reasons</h3>
            <ul>
                <li>The Portex agent is not running</li>
                <li>The tunnel was stopped or deleted</li>
                <li>Network connectivity issues</li>
                <li>The local service is not accessible</li>
            </ul>
        </div>

        <a href="https://` + h.domain + `" class="cta">Go to Dashboard</a>

        <div class="footer">
            <p>Powered by <strong>Portex</strong> · Secure Tunnel Service</p>
        </div>
    </div>
</body>
</html>`

	w.Write([]byte(html))
}
