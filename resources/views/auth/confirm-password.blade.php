<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.querySelector('form[action="{{ route('password.confirm') }}"]') || document.querySelector('form');
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
