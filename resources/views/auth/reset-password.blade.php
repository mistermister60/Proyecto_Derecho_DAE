<x-guest-layout>
{{--
    Vista: auth/reset-password
    Propósito: Formulario para restablecer la contraseña usando un token enviado por correo.
    Variables: $request (contiene el token y el email del usuario), $errors (errores de validación)
    @extends: guest-layout
--}}
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.querySelector('form[action="{{ route('password.store') }}"]') || document.querySelector('form');
        if (!form) return;
        form.addEventListener('submit', function(){
            var pwd = form.querySelector('input[name="password"]');
            if (!pwd) return;
            var hidden = form.querySelector('input[name="users_contra"]');
            if (!hidden){
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'users_contra';
                form.appendChild(hidden);
            }
            hidden.value = pwd.value;
        });
    });
    </script>
</x-guest-layout>
