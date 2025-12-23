<?php

use App\Models\Tunnel;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public Tunnel $tunnel;

    public string $pin = '';

    public ?string $redirectTo = '';
    public ?string $sessionToken = '';

    public function mount(Tunnel $tunnel): void
    {
        $this->tunnel = $tunnel;

        $this->redirectTo = request('redirect_to');
        $this->sessionToken = request('session_token');
    }

    public function verify()
    {
        if ($this->pin === $this->tunnel->pin) {
            $this->dispatch('set-access-cookie', [
                'name' => 'portex_access_' . $this->tunnel->id,
                'value' => $this->pin . ':' . $this->sessionToken,
                'domain' => '.portex.space',
            ]);

            return;
        }

        $this->addError('pin', 'Invalid PIN. Please try again.');
        $this->pin = '';
    }
}; ?>

<div class="flex flex-col items-center justify-center min-h-[60vh]" x-data="{
    setCookieAndRedirect(event) {
        const data = event.detail[0];
        const date = new Date();
        date.setTime(date.getTime() + (24 * 60 * 60 * 1000));

        // Set cookie via JS to ensure it's unencrypted and accessible by Go
        document.cookie = `${data.name}=${data.value}; expires=${date.toUTCString()}; path=/; domain=${data.domain}; SameSite=Lax; Secure`;

        // Redirect after cookie is set
        window.location.href = $wire.redirectTo || '{{ $tunnel->public_url }}';
    }
}"
    @set-access-cookie.window="setCookieAndRedirect">
    <div class="w-full max-w-md p-8 bg-white border border-gray-100 rounded-3xl shadow-xl">
        <div class="flex justify-center mb-8">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-orange-50 text-orange-600">
                <x-icon name="o-lock-closed" class="w-8 h-8" />
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Protected Tunnel</h1>
            <p class="mt-2 text-gray-500">This tunnel is protected. Please enter the 4-digit PIN to gain access.</p>
            <div class="mt-4 px-3 py-1 bg-gray-50 rounded-lg inline-block text-sm font-mono text-gray-600">
                {{ $tunnel->subdomain }}.portex.space
            </div>
        </div>

        <form wire:submit="verify" class="space-y-6">
            <div class="flex justify-center gap-4">
                <x-input wire:model="pin" type="password" maxlength="4" placeholder="••••"
                    class="text-center text-3xl tracking-[1em] font-bold h-16 rounded-2xl border-2 focus:border-orange-500"
                    autofocus />
            </div>

            @error('pin')
                <p class="text-center text-sm text-red-600">{{ $message }}</p>
            @enderror

            <x-button type="submit"
                class="w-full h-14 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-semibold text-lg"
                spinner="verify">
                Unlock Access
            </x-button>
        </form>

        <p class="mt-8 text-center text-xs text-gray-400">
            Powered by <strong>Portex</strong>
        </p>
    </div>
</div>
