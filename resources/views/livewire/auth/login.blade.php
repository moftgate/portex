<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new #[Layout('components.layouts.guest', ['title' => 'Login'])] class extends Component {
    use Toast;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $this->remember)) {
            request()->session()->regenerate();

            if (session()->has('claim_agent_api_key')) {
                return redirect()->route('agents.claim', [
                    'api_key' => session()->pull('claim_agent_api_key'),
                    'api_secret' => session()->pull('claim_agent_api_secret'),
                ]);
            }

            return redirect()->intended('/panel');
        }

        $this->error('Invalid credentials', position: 'toast-bottom');
    }
}; ?>

<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold mb-1" style="color: var(--color-neutral);">
            Sign in
        </h1>
        <p class="text-sm text-gray-600">
            Enter your credentials to access your account
        </p>
    </div>

    <!-- Form -->
    <form wire:submit="login" class="space-y-4">
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium mb-1.5" style="color: var(--color-neutral);">
                Email
            </label>
            <input type="email" id="email" wire:model="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                style="--tw-ring-color: var(--color-secondary);" placeholder="you@example.com" required />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium mb-1.5" style="color: var(--color-neutral);">
                Password
            </label>
            <input type="password" id="password" wire:model="password"
                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                style="--tw-ring-color: var(--color-secondary);" placeholder="Enter your password" required />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input type="checkbox" id="remember" wire:model="remember"
                class="w-4 h-4 border-gray-300 rounded focus:ring-2"
                style="color: var(--color-primary); --tw-ring-color: var(--color-primary);" />
            <label for="remember" class="ml-2 text-sm" style="color: var(--color-neutral);">
                Remember me
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full px-4 py-2.5 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
            style="background-color: var(--color-primary); --tw-ring-color: var(--color-primary);"
            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'" wire:loading.attr="disabled">
            <span wire:loading.remove>Sign in</span>
            <span wire:loading>Signing in...</span>
        </button>
    </form>

    <!-- Divider -->
    {{--<div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="px-2 bg-white text-gray-500">
                Don't have an account?
            </span>
        </div>
    </div>

    <!-- Register Link -->
    <a href="/register" class="block w-full text-center px-4 py-2.5 border font-medium rounded-md transition-colors"
        style="border-color: var(--color-secondary); color: var(--color-secondary);"
        onmouseover="this.style.backgroundColor='rgba(37, 99, 235, 0.05)'"
        onmouseout="this.style.backgroundColor='transparent'">
        Create an account
    </a>--}}
</div>
