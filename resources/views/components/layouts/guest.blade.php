<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Portex') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-primary: #F97316;
            /* NetBird Orange */
            --color-secondary: #2563EB;
            /* NetBird Blue */
            --color-neutral: #374151;
            /* NetBird Gray */
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>

    <script defer src="https://cloud.umami.is/script.js"
            data-website-id="269e123b-a36d-485b-bec5-378554421aa9"></script>
</head>

<body class="antialiased bg-gray-50">
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 mb-6">
                <svg class="w-8 h-8" style="color: var(--color-primary);" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-2xl font-semibold" style="color: var(--color-neutral);">Portex</span>
            </div>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-lg border border-gray-200 p-8">
            {{ $slot }}
        </div>
    </div>
</div>
</body>

</html>
