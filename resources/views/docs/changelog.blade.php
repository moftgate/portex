@extends('components.layouts.docs')

@section('title', 'Changelog & Release Notes')

@section('content')
    <h1>Changelog</h1>
    <p>Stay up to date with the latest features, improvements, and bug fixes for Portex.</p>

    <div style="margin-top: 48px;">
        <div style="display: flex; gap: 24px;">
            <div style="min-width: 120px;">
                <span
                    style="background: var(--orange-dim); color: var(--orange); padding: 4px 12px; border-radius: 100px; font-weight: 700; font-size: 13px;">v0.7.1</span>
                <p style="font-size: 12px; color: var(--slate); margin-top: 8px;">Dec 31, 2025</p>
            </div>
            <div style="flex: 1; border-left: 2px solid var(--border); padding-left: 32px; padding-bottom: 48px;">
                <h3 style="margin-bottom: 16px;">The Windows & Visibility Release</h3>
                <p>Ensuring Portex runs flawlessly on all modern Windows versions{{-- alongside remote debugging features--}}.</p>
                <ul style="margin-left: 20px; list-style: disc; color: #334155;">
                    <li style="margin-bottom: 8px;">Improved <strong>Device ID Retrieval</strong> for Windows: Added multiple
                        fallback methods (REG query and PowerShell) to replace the deprecated <code>wmic</code> command.
                    </li>
                   {{-- <li style="margin-bottom: 8px;">Added <strong>Remote Browser Console Sync</strong>: Capture
                        <code>console.log</code>, <code>error</code>, and JS crashes from remote devices directly in your
                        dashboard.</li>
                    <li style="margin-bottom: 8px;">Real-time log streaming using edge-level script injection.</li>--}}
                    <li style="margin-bottom: 8px;">Fixed "executable file not found in %PATH%" error on Windows 10 (21H1+)
                        and Windows 11.</li>
                </ul>
            </div>
        </div>
        <div style="display: flex; gap: 24px;">
            <div style="min-width: 120px;">
                <span
                    style="background: var(--orange-dim); color: var(--orange); padding: 4px 12px; border-radius: 100px; font-weight: 700; font-size: 13px;">v0.6.5</span>
                <p style="font-size: 12px; color: var(--slate); margin-top: 8px;">Dec 29, 2024</p>
            </div>
            <div style="flex: 1; border-left: 2px solid var(--border); padding-left: 32px; padding-bottom: 48px;">
                <h3 style="margin-bottom: 16px;">The Security & Access Release</h3>
                <p>Focus on advanced tunnel security and network access control.</p>
                <ul style="margin-left: 20px; list-style: disc; color: #334155;">
                    <li style="margin-bottom: 8px;">Added <strong>IP Whitelisting</strong> support for both
                        <code>start</code> and <code>share</code> commands.
                    </li>
                    <li style="margin-bottom: 8px;">New <strong>Access Control</strong> tab in the tunnel dashboard.</li>
                    <li style="margin-bottom: 8px;">Support for multiple IP addresses (comma-separated) via CLI and
                        Dashboard.</li>
                    <li style="margin-bottom: 8px;">Integrated Real-time IP resolution for proxy headers (X-Forwarded-For
                        support).</li>
                    <li style="margin-bottom: 8px;">Enhanced Landing and Documentation pages with new security feature
                        highlights.</li>
                </ul>
            </div>
        </div>
        <div style="display: flex; gap: 24px;">
            <div style="min-width: 120px;">
                <span
                    style="background: var(--orange-dim); color: var(--orange); padding: 4px 12px; border-radius: 100px; font-weight: 700; font-size: 13px;">v0.6.0</span>
                <p style="font-size: 12px; color: var(--slate); margin-top: 8px;">Dec 24, 2024</p>
            </div>
            <div style="flex: 1; border-left: 2px solid var(--border); padding-left: 32px; padding-bottom: 48px;">
                <h3 style="margin-bottom: 16px;">The Documentation Release</h3>
                <p>Major focus on developer onboarding and technical transparency.</p>
                <ul style="margin-left: 20px; list-style: disc; color: #334155;">
                    <li style="margin-bottom: 8px;">Launched full <a href="{{ route('docs.index') }}">Documentation Hub</a>
                        with search and multi-page guides.</li>
                    <li style="margin-bottom: 8px;">Added <strong>How it Works</strong> section with infrastructure
                        architecture diagrams.</li>
                    <li style="margin-bottom: 8px;">New <strong>Use Cases</strong> guide for webhooks and mobile testing.
                    </li>
                    <li style="margin-bottom: 8px;">Integrated GitHub repositories directly into the site navigation.</li>
                    <li style="margin-bottom: 8px;">Refined overall aesthetic with "Apple-style" minimal layout.</li>
                </ul>
            </div>
        </div>

        <div style="display: flex; gap: 24px;">
            <div style="min-width: 120px;">
                <span
                    style="background: var(--light); color: var(--slate); padding: 4px 12px; border-radius: 100px; font-weight: 700; font-size: 13px;">v0.5.5</span>
                <p style="font-size: 12px; color: var(--slate); margin-top: 8px;">Dec 22, 2024</p>
            </div>
            <div style="flex: 1; border-left: 2px solid var(--border); padding-left: 32px; padding-bottom: 48px;">
                <h3 style="margin-bottom: 16px;">CLI Polish & Bug Fixes</h3>
                <ul style="margin-left: 20px; list-style: disc; color: #334155;">
                    <li style="margin-bottom: 8px;">Improved cross-platform browser opening for <code>portex login</code>.
                    </li>
                    <li style="margin-bottom: 8px;">Added <code>portex version</code> command to CLI.</li>
                    <li style="margin-bottom: 8px;">Fixed a bug where tunnel status remained "Active" after agent
                        disconnect.</li>
                    <li style="margin-bottom: 8px;">Optimized installer scripts for macOS and Windows.</li>
                </ul>
            </div>
        </div>

        <div style="display: flex; gap: 24px;">
            <div style="min-width: 120px;">
                <span
                    style="background: var(--light); color: var(--slate); padding: 4px 12px; border-radius: 100px; font-weight: 700; font-size: 13px;">v0.5.0</span>
                <p style="font-size: 12px; color: var(--slate); margin-top: 8px;">Dec 20, 2024</p>
            </div>
            <div style="flex: 1; border-left: 2px solid var(--border); padding-left: 32px; padding-bottom: 48px;">
                <h3 style="margin-bottom: 16px;">Core Features Beta</h3>
                <p>First public beta release with basic tunneling capabilities.</p>
                <ul style="margin-left: 20px; list-style: disc; color: #334155;">
                    <li style="margin-bottom: 8px;">Secure HTTP tunneling to localhost ports.</li>
                    <li style="margin-bottom: 8px;">Static file sharing with <code>portex share</code>.</li>
                    <li style="margin-bottom: 8px;">Real-time activity logs in the dashboard.</li>
                    <li style="margin-bottom: 8px;">4-digit PIN protection for tunnels.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="callout">
        <span>Updates</span>
        We release updates weekly. Follow our <a href="https://github.com/orgs/portex-space/repositories" target="_blank"
            style="color: var(--orange); font-weight: 600; text-decoration: none;">GitHub</a> for commit-level activity.
    </div>
@endsection
