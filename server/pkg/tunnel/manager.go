package tunnel

import (
	"crypto/rand"
	"encoding/hex"
	"encoding/json"
	"log"
	"net/http"
	"sync"

	"github.com/gorilla/websocket"
)

var upgrader = websocket.Upgrader{
	CheckOrigin: func(r *http.Request) bool {
		return true // Allow all origins for now
	},
}

// Message types
const (
	MessageTypeHTTPRequest  = "http_request"
	MessageTypeHTTPResponse = "http_response"
	MessageTypePing         = "ping"
	MessageTypePong         = "pong"
)

type Message struct {
	Type      string          `json:"type"`
	RequestID string          `json:"request_id,omitempty"`
	Data      json.RawMessage `json:"data,omitempty"`
}

type HTTPRequest struct {
	Method  string            `json:"method"`
	Path    string            `json:"path"`
	Headers map[string]string `json:"headers"`
	Body    []byte            `json:"body,omitempty"`
}

type HTTPResponse struct {
	StatusCode int               `json:"status_code"`
	Headers    map[string]string `json:"headers"`
	Body       []byte            `json:"body,omitempty"`
}

type AgentConnection struct {
	TunnelID     string
	Subdomain    string
	Conn         *websocket.Conn
	Send         chan Message
	PendingReqs  map[string]chan HTTPResponse
	Pin          string
	SessionToken string
	mu           sync.RWMutex
}

type TunnelManager struct {
	agents map[string]*AgentConnection // subdomain -> agent
	mu     sync.RWMutex
}

func NewTunnelManager() *TunnelManager {
	return &TunnelManager{
		agents: make(map[string]*AgentConnection),
	}
}

func (tm *TunnelManager) RegisterAgent(subdomain, tunnelID, pin string, conn *websocket.Conn) *AgentConnection {
	// Generate a random session token for this specific connection
	sessionToken := generateRandomToken(16)

	agent := &AgentConnection{
		TunnelID:     tunnelID,
		Subdomain:    subdomain,
		Pin:          pin,
		SessionToken: sessionToken,
		Conn:         conn,
		Send:         make(chan Message, 256),
		PendingReqs:  make(map[string]chan HTTPResponse),
	}

	tm.mu.Lock()
	tm.agents[subdomain] = agent
	tm.mu.Unlock()

	//log.Printf("Agent registered for subdomain: %s (tunnel: %s, session: %s)", subdomain, tunnelID, sessionToken)
	return agent
}

func generateRandomToken(n int) string {
	b := make([]byte, n)
	if _, err := rand.Read(b); err != nil {
		return ""
	}
	return hex.EncodeToString(b)
}

func (tm *TunnelManager) UnregisterAgent(subdomain string) {
	tm.mu.Lock()
	if agent, ok := tm.agents[subdomain]; ok {
		close(agent.Send)
		delete(tm.agents, subdomain)
	}
	tm.mu.Unlock()
	log.Printf("Agent unregistered for subdomain: %s", subdomain)
}

func (tm *TunnelManager) GetAgent(subdomain string) *AgentConnection {
	tm.mu.RLock()
	defer tm.mu.RUnlock()
	return tm.agents[subdomain]
}

func (agent *AgentConnection) ReadPump() {
	defer func() {
		agent.Conn.Close()
	}()

	for {
		var msg Message
		err := agent.Conn.ReadJSON(&msg)
		if err != nil {
			if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
				log.Printf("WebSocket error: %v", err)
			}
			break
		}

		switch msg.Type {
		case MessageTypeHTTPResponse:
			var resp HTTPResponse
			if err := json.Unmarshal(msg.Data, &resp); err != nil {
				log.Printf("Failed to unmarshal HTTP response: %v", err)
				continue
			}

			agent.mu.RLock()
			respChan, ok := agent.PendingReqs[msg.RequestID]
			agent.mu.RUnlock()

			if ok {
				respChan <- resp
			}

		case MessageTypePong:
			// Handle pong
		}
	}
}

func (agent *AgentConnection) WritePump() {
	defer func() {
		agent.Conn.Close()
	}()

	for msg := range agent.Send {
		err := agent.Conn.WriteJSON(msg)
		if err != nil {
			log.Printf("Failed to write message: %v", err)
			return
		}
	}
}

func (agent *AgentConnection) SendHTTPRequest(requestID string, req HTTPRequest) (HTTPResponse, error) {
	respChan := make(chan HTTPResponse, 1)

	agent.mu.Lock()
	agent.PendingReqs[requestID] = respChan
	agent.mu.Unlock()

	defer func() {
		agent.mu.Lock()
		delete(agent.PendingReqs, requestID)
		agent.mu.Unlock()
	}()

	data, _ := json.Marshal(req)
	msg := Message{
		Type:      MessageTypeHTTPRequest,
		RequestID: requestID,
		Data:      data,
	}

	agent.Send <- msg

	// Wait for response (with timeout would be better)
	resp := <-respChan
	return resp, nil
}
