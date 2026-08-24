@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 450px; font-family: Arial, sans-serif;">
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="background: #fef3c7; color: #92400e; width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;">🔒</div>
            <h2 style="color: #1e3a8a; margin-bottom: 10px;">Confirmar Contraseña</h2>
            <p style="color: #6b7280; font-size: 14px;">Por seguridad, confirma tu contraseña antes de continuar.</p>
        </div>

        @if ($errors->any())
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.confirm.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px; text-align: left;">
                <label for="password" style="display: block; color: #374151; font-size: 14px; font-weight: bold; margin-bottom: 6px;">Contraseña Actual</label>
                <x-password-input name="password" id="password" required autocomplete="current-password"
                       style="width: 100%; padding: 12px 44px 12px 12px; font-size: 15px; border: 2px solid #e5e7eb; border-radius: 8px; box-sizing: border-box;" />
            </div>

            <button type="submit" style="width: 100%; background: #1e3a8a; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">
                Confirmar
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('dashboard') }}" style="background: none; border: none; color: #6b7280; font-size: 13px; cursor: pointer; text-decoration: underline;">
                Cancelar y volver al dashboard
            </a>
        </div>
    </div>
</div>
@endsection