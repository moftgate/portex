<?php

use Livewire\Volt\Component;

new class extends Component {
    public function upgradeToPremium()
    {
        $user = auth()->user();

        // In a real app, you'd redirect to Stripe/Paddle here
        // For this demo, let's just make them premium
        $user->update([
            'tier' => 'premium',
            'daily_usage_limit_seconds' => PHP_INT_MAX, // Or stay 3600 but check tier
        ]);

        $this->success('Welcome to Premium! Your limits have been removed.', redirectTo: route('dashboard'));
    }
}; ?>

<div>
    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                Ready to go <span class="text-blue-600">Premium</span>?
            </h1>
            <p class="mt-4 text-xl text-gray-600">
                Unlock unlimited tunnels, custom subdomains, and 24/7 support.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 items-center">
            <!-- Free Tier -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 opacity-75">
                <h3 class="text-lg font-semibold text-gray-900">Free Tier</h3>
                <p class="mt-4 text-gray-600">Perfect for personal projects and quick testing.</p>
                <div class="mt-6 flex items-baseline">
                    <span class="text-4xl font-extrabold text-gray-900">$0</span>
                    <span class="ml-1 text-xl text-gray-500">/mo</span>
                </div>

                <ul class="mt-8 space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-600">1 Hour Daily Usage</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-600">Random Subdomains</span>
                    </li>
                    <li class="flex items-start gap-3 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="text-sm">Custom Domains</span>
                    </li>
                </ul>

                <button disabled
                    class="mt-10 block w-full bg-gray-100 text-gray-400 font-semibold py-3 px-4 rounded-xl text-center">
                    Current Plan
                </button>
            </div>

            <!-- Premium Tier -->
            <div
                class="bg-white rounded-2xl shadow-xl border-2 border-blue-500 p-8 transform scale-105 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 bg-blue-500 text-white text-[10px] uppercase font-bold px-3 py-1 rounded-bl-lg">
                    Recommended
                </div>

                <h3 class="text-lg font-semibold text-gray-900">Premium Plan</h3>
                <p class="mt-4 text-gray-600">The ultimate tool for developers and small teams.</p>
                <div class="mt-6 flex items-baseline">
                    <span class="text-4xl font-extrabold text-gray-900">$9</span>
                    <span class="ml-1 text-xl text-gray-500">/mo</span>
                </div>

                <ul class="mt-8 space-y-4">
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-900 font-medium">Unlimited Tunnel Time</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-900 font-medium">Custom Subdomains</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-900 font-medium">SSL Certificates</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-gray-900 font-medium">Priority Support</span>
                    </li>
                </ul>

                <button wire:click="upgradeToPremium"
                    class="mt-10 block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl text-center shadow-lg transform transition active:scale-95">
                    Upgrade to Premium
                </button>
            </div>
        </div>

        <!-- FAQ or Features -->
        <div class="mt-24 grid md:grid-cols-3 gap-8 text-center">
            <div>
                <div class="mx-auto w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900">Secure by Design</h4>
                <p class="mt-2 text-sm text-gray-500">End-to-end encryption for all your tunnel traffic.</p>
            </div>
            <div>
                <div class="mx-auto w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900">Blazing Fast</h4>
                <p class="mt-2 text-sm text-gray-500">Optimized routing for the lowest possible latency.</p>
            </div>
            <div>
                <div class="mx-auto w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                    </svg>
                </div>
                <h4 class="font-semibold text-gray-900">Global Network</h4>
                <p class="mt-2 text-sm text-gray-500">Tunnels available from any region in the world.</p>
            </div>
        </div>
    </div>
</div>
