package tunnel

import "net/http"

func (h *ProxyHandler) renderLocalServiceError(w http.ResponseWriter, subdomain string) {
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	w.WriteHeader(http.StatusBadGateway)

	html := `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Service Unreachable - Portex</title>
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
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
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
            background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
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
            color: #D97706;
            font-weight: 600;
            font-size: 18px;
            background: #FEF3C7;
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
            background: #fffbeb;
            border-radius: 16px;
            padding: 24px;
            margin: 32px 0;
            text-align: left;
            border: 2px solid #FDE68A;
        }
        
        .reasons h3 {
            font-size: 14px;
            font-weight: 600;
            color: #92400E;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .reasons ul {
            list-style: none;
        }
        
        .reasons li {
            font-size: 14px;
            color: #78350F;
            padding: 8px 0;
            padding-left: 28px;
            position: relative;
        }
        
        .reasons li:before {
            content: "⚠";
            position: absolute;
            left: 8px;
            font-size: 16px;
        }
        
        .cta {
            display: inline-block;
            background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        
        .cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
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
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        
        <h1>Local Service Unreachable</h1>
        
        <div class="subdomain">` + subdomain + `.` + h.domain + `</div>
        
        <p>The tunnel is active, but the local service is not responding. Please check your local application.</p>
        
        <div class="reasons">
            <h3>Common Causes</h3>
            <ul>
                <li>The local application is not running</li>
                <li>Wrong port number configured</li>
                <li>Firewall blocking local connections</li>
                <li>Application crashed or stopped</li>
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
