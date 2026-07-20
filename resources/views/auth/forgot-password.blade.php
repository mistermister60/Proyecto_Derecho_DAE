@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; font-family: Arial, sans-serif;">
        <h2 style="color: #1e3a8a; margin-bottom: 10px;">¿Olvidaste tu contraseña?</h2>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 25px;">Ingresa tu correo institucional y te enviaremos un enlace para restablecerla.</p>

        @if (session('status'))
            <div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px; text-align: left;">
                <label for="email" style="display: block; color: #374151; font-size: 14px; font-weight: bold; margin-bottom: 6px;">Correo Electrónico Institucional</label>
                <input type="email" name="email" id="email" required value="{{ old('email' )}}"
                       style="width: 100%; padding: 12px; font-size: 15px; border: 2px solid #e5e7eb; border-radius: 8px; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; background: #1e3a8a; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">
                Enviar Enlace de Restablecimiento
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('login') }}" style="background: none; border: none; color: #6b7280; font-size: 13px; cursor: pointer; text-decoration: underline;">
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>
@endsection