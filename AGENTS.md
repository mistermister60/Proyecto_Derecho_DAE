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

## Principios de diseño

Este proyecto sigue principios SOLID y prácticas de código limpio:

| Principio | Aplicación |
|-----------|------------|
| **SRP** — Single Responsibility | Cada clase tiene una única responsabilidad. Los controladores delegan en servicios. |
| **OCP** — Open/Closed | Las extensiones se hacen creando nuevas clases, no modificando las existentes. |
| **LSP** — Liskov Substitution | Las subclases son sustituibles por sus padres sin romper el sistema. |
| **ISP** — Interface Segregation | Interfaces pequeñas y específicas (Form Requests, Services). |
| **DIP** — Dependency Inversion | Dependemos de abstracciones (interfaces/services), no de implementaciones concretas. |
| **DRY** — Don't Repeat Yourself | El usuario autenticado se obtiene vía `auth()->user()`, no consultando de nuevo. |
| **KISS** — Keep It Simple | `Auth::id()` basta; no `Auth::user()->id ?? Auth::id()`. |
| **YAGNI** — You Ain't Gonna Need It | No agregues código "por si acaso". Solo lo que se necesita ahora. |

## Fixes de seguridad aplicados

- **OTP por email**: Autenticación de dos factores mediante código de 6 dígitos enviado al correo institucional, con expiración de 15 minutos.
- **Rate limiting**: Protección contra fuerza bruta en login (5 intentos → bloqueo 5 min).
- **Sanctum tokens**: Tokens de acceso efímeros (1 hora) con hash SHA-256.
- **CSP**: Content Security Policy global vía `SecurityHeadersMiddleware`.
- **Cuentas inactivas**: Denegación de acceso si `usuario_estado !== 'activo'`.
- **Excepción genérica en login**: No se revela si el email o la contraseña son incorrectos.
- **Exclusión de `.env` del repositorio**: `APP_KEY` y credenciales nunca se commitean.

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
npm run build       # build producción (Vite manifest)
npm run dev         # dev server
npm run lint        # lint CSS/JS (si configurado)
npm audit           # auditoría de seguridad de dependencias

# Git / Seguridad
git secret scan     # escanea commits en busca de secretos (gitleaks)
git rm --cached     # remueve archivo del tracking sin borrarlo del disco

# Debug
php artisan tinker              # REPL interactivo
php artisan optimize:clear      # limpia cachés (config, route, view, events)
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
