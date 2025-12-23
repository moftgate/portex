<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left: Logo & Nav -->
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <svg class="w-6 h-6" style="color: var(--color-primary);" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-lg font-semibold" style="color: var(--color-neutral);">Portex</span>
                </a>

                <!-- Navigation Links -->
                @auth
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            style="{{ request()->routeIs('dashboard') ? 'background-color: var(--color-primary);' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('tunnels.index') }}"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('tunnels.*') ? 'text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            style="{{ request()->routeIs('tunnels.*') ? 'background-color: var(--color-primary);' : '' }}">
                            Tunnels
                        </a>
                        <a href="{{ route('agents.index') }}"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('agents.*') ? 'text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            style="{{ request()->routeIs('agents.*') ? 'background-color: var(--color-primary);' : '' }}">
                            Agents
                        </a>
                        <a href="{{ route('activity.index') }}"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('activity.*') ? 'text-white' : 'text-gray-700 hover:bg-gray-100' }}"
                            style="{{ request()->routeIs('activity.*') ? 'background-color: var(--color-primary);' : '' }}">
                            Activity
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Right: User Menu -->
            <div class="flex items-center gap-4">
                @auth
                    <!-- User Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
                                style="background-color: var(--color-secondary);">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:block">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg border border-gray-200 shadow-lg py-1 z-50"
                            style="display: none;">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <div class="text-sm font-medium" style="color: var(--color-neutral);">
                                    {{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-medium text-white rounded-xl shadow-sm hover:opacity-90 transition-all"
                        style="background-color: var(--color-primary);">Login</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="md:hidden border-t border-gray-200">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-700' }}"
                style="{{ request()->routeIs('dashboard') ? 'background-color: var(--color-primary);' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('tunnels.index') }}"
                class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tunnels.*') ? 'text-white' : 'text-gray-700' }}"
                style="{{ request()->routeIs('tunnels.*') ? 'background-color: var(--color-primary);' : '' }}">
                Tunnels
            </a>
            <a href="{{ route('agents.index') }}"
                class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('agents.*') ? 'text-white' : 'text-gray-700' }}"
                style="{{ request()->routeIs('agents.*') ? 'background-color: var(--color-primary);' : '' }}">
                Agents
            </a>
            <a href="{{ route('activity.index') }}"
                class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('activity.*') ? 'text-white' : 'text-gray-700' }}"
                style="{{ request()->routeIs('activity.*') ? 'background-color: var(--color-primary);' : '' }}">
                Activity
            </a>
        </div>
    </div>
</nav>
