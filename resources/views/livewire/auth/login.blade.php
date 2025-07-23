<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('Credenciales incorrectas'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - ElizaLte</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-blue-100 to-white">
    <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-xl px-8 py-10 flex flex-col items-center text-center">
        <div class="text-4xl font-extrabold tracking-wide text-blue-600 mb-2">ElizaLte</div>
        <div class="text-base text-slate-500 mb-6">Inicia sesión en tu cuenta</div>
        <div class="flex justify-center mb-8">
            <svg class="w-20 h-20" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="60" cy="60" r="56" fill="#3b82f6" fill-opacity="0.08" />
                <circle cx="60" cy="60" r="40" fill="#3b82f6" fill-opacity="0.15" />
                <circle cx="60" cy="60" r="24" fill="#3b82f6" fill-opacity="0.25" />
                <circle cx="60" cy="60" r="10" fill="#3b82f6" />
            </svg>
        </div>
        <x-auth-session-status class="text-center mb-4" :status="session('status')" />
        <form wire:submit="login" class="flex flex-col gap-6 w-full">
            <div class="flex flex-col items-start">
                <label for="email" class="mb-1 text-sm font-medium text-slate-700">Correo electrónico</label>
                <input id="email" name="email" type="email" wire:model="email" required autofocus autocomplete="email" placeholder="correo@ejemplo.com"
                    class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition" />
            </div>
            <div class="flex flex-col items-start">
                <label for="password" class="mb-1 text-sm font-medium text-slate-700">Contraseña</label>
                <input id="password" name="password" type="password" wire:model="password" required autocomplete="current-password" placeholder="Contraseña"
                    class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition" />
            </div>
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" wire:model="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                <label for="remember" class="ml-2 text-sm text-slate-600">Recuérdame</label>
            </div>
            <div class="flex justify-center w-full mt-2">
                <button type="submit"
                    class="w-full px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    Iniciar sesión
                </button>
            </div>
        </form>
        <div class="mt-8 text-xs text-slate-400 text-center w-full">
            Desarrollado por Abel Arana Cortez &middot; <a href="https://open9.cloud"
                class="underline hover:text-blue-600 transition" target="_blank">open9.cloud</a>
        </div>
    </div>
</body>

</html>
