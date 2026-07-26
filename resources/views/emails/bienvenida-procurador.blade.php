@component('mail::message')
# Bienvenido al Consultorio Jurídico DAE

Hola **{{ $nombre }}**,

Has sido registrado como **Procurador** en el Sistema de Gestión del Consultorio Jurídico DAE (USAP). 

## Tus credenciales de acceso

| Campo | Valor |
|-------|-------|
| **Email** | {{ $email }} |
| **Contraseña temporal** | `{{ $tempPassword }}` |
| **URL de acceso** | [{{ $loginUrl }}]({{ $loginUrl }}) |

## Pasos para tu primer ingreso

1. Haz clic en el enlace de acceso arriba o visita: **{{ $loginUrl }}**
2. Ingresa tu email y la contraseña temporal
3. Recibirás un **código OTP de 6 dígitos por correo** (autenticación de dos factores)
4. Ingresa el código OTP
5. **Deberás cambiar tu contraseña obligatoriamente** (la temporal expira en el primer uso)

## Importante

- La contraseña temporal **solo funciona una vez**
- Tu nueva contraseña debe tener: **mínimo 8 caracteres, mayúscula, minúscula, número y símbolo**
- Guarda tus nuevas credenciales en un lugar seguro

## Soporte

Si tienes problemas para acceder, contacta al **Director del Consultorio Jurídico**.

---

**Consultorio Jurídico DAE**  
Universidad de San Pedro Sula (USAP)  
Sistema de Gestión de Casos

@endcomponent