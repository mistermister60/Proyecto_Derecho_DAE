# Proyecto Derecho DAE — Guía para Agentes IA

Sistema de gestión para Consultorio Jurídico DAE (USAP). Laravel 13 + PHP 8.5 + Tailwind v4 + Alpine.js v3.

## Stack

| Paquete | Versión |
|---------|---------|
| PHP | 8.5 |
| laravel/framework | 13 |
| laravel/sanctum | 4 |
| laravel/breeze | 2 |
| laravel/pint | 1 |
| phpunit/phpunit | 12 |
| alpinejs | 3 |
| tailwindcss | 4 |

## Skills del proyecto

Hay skills especializados en `.agents/skills/` — léelos antes de trabajar en su dominio:
- `.agents/skills/laravel-best-practices/SKILL.md` — patrones Laravel, Eloquent, N+1, caché
- `.agents/skills/tailwindcss-development/SKILL.md` — Tailwind v4, responsive, dark mode

## Comandos esenciales

```bash
# Tests
php artisan test --compact
php artisan test --compact --filter=NombreTest
php artisan test --compact tests/Feature/RutaTest.php

# Linter
vendor/bin/pint --format agent

# Artisan
php artisan route:list --except-vendor
php artisan config:show app.name
php artisan make:model --help
php artisan make:test --phpunit NombreTest

# Frontend
npm run build    # build producción (Vite manifest)
npm run dev      # dev server
```

## Convenciones de código

### PHP
- `{ }` siempre, incluso para bodies de una línea
- Constructor property promotion: `public function __construct(public Servicio $s) {}`
- Tipos explícitos en parámetros y retorno: `function foo(User $u): bool`
- PHPDoc blocks, NO comentarios inline
- Pint formatea automáticamente

### Laravel
- Usar `php artisan make:*` para crear clases, modelos, controladores, etc.
- Pasar `--no-interaction` a todos los comandos
- Rutas con nombre y helper `route()` para URLs
- Preferir Eloquent API Resources para APIs
- Factory + Faker para tests: `$this->faker->word()` o `fake()->randomDigit()`

### Tests
- PHPUnit (NO Pest). `php artisan make:test --phpunit`
- Feature tests (no unit) salvo excepciones
- Cada cambio debe incluir test o actualizar uno existente
- No borrar tests sin aprobación

### Frontend
- Alpine.js para interactividad (atributos `x-data`, `x-show`, etc.)
- Tailwind v4 utility classes (sin `@apply`)
- View Transitions API para navegación MPA

## Arquitectura

- Autenticación: login → 2FA (OTP por email) → cambio contraseña obligatorio (primer inicio)
- Director omite 2FA automáticamente
- Middleware pipeline: `['auth', 'otp', 'password.changed']`
- Roles: Director (rol_id=1), Procurador (rol_id=2)
- `tests/TestCase.php` tiene helper `actingAsAuthenticated($user)` para simular auth + 2FA

## Problemas comunes

- **Vite manifest**: Error "Unable to locate file in Vite manifest" → ejecutar `npm run build`
- **CSP**: El middleware `SecurityHeadersMiddleware` aplica CSP global. Si algo no carga, revisar las directivas `script-src` y `style-src`
- **Página en blanco**: Revisar que `view-transition-ready` se agregue en el JS después de `DOMContentLoaded`

## Despliegue

- Usar [Laravel Cloud](https://cloud.laravel.com/)
- Correr `npm run build` antes de deploy
