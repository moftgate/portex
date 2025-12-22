<x-layouts.guest title="Register">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold mb-1" style="color: var(--color-neutral);">
            Create account
        </h1>
        <p class="text-sm text-gray-600">
            Get started with Portex
        </p>
    </div>

    <!-- Form -->
    <form wire:submit="register" class="space-y-4">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium mb-1.5" style="color: var(--color-neutral);">
                Name
            </label>
            <input type="text" id="name" wire:model="name"
                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                style="--tw-ring-color: var(--color-secondary);" placeholder="John Doe" required />
        </div>

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
                style="--tw-ring-color: var(--color-secondary);" placeholder="Minimum 8 characters" required />
        </div>

        <!-- Password Confirmation -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1.5"
                style="color: var(--color-neutral);">
                Confirm password
            </label>
            <input type="password" id="password_confirmation" wire:model="password_confirmation"
                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                style="--tw-ring-color: var(--color-secondary);" placeholder="Re-enter your password" required />
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full px-4 py-2.5 text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
            style="background-color: var(--color-primary); --tw-ring-color: var(--color-primary);"
            onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'" wire:loading.attr="disabled">
            <span wire:loading.remove>Create account</span>
            <span wire:loading>Creating account...</span>
        </button>
    </form>

    <!-- Divider -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="px-2 bg-white text-gray-500">
                Already have an account?
            </span>
        </div>
    </div>

    <!-- Login Link -->
    <a href="/login" class="block w-full text-center px-4 py-2.5 border font-medium rounded-md transition-colors"
        style="border-color: var(--color-secondary); color: var(--color-secondary);"
        onmouseover="this.style.backgroundColor='rgba(37, 99, 235, 0.05)'"
        onmouseout="this.style.backgroundColor='transparent'">
        Sign in
    </a>
</x-layouts.guest>
