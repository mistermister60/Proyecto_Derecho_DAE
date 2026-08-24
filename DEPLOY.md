# DEPLOY.md — Despliegue en Producción (Hostinger VPS)

Guía para llevar el sistema a producción en el VPS de Hostinger.
El proyecto se sirve desde `/www/wwwroot/Proyecto_Derecho_DAE/` y la rama
de producción es **`Deployment`** (en el VPS el branch local es `main` y
hace tracking de `origin/Deployment`).

## 1. Acceso al servidor

```bash
ssh root@2.25.96.76
cd /www/wwwroot/Proyecto_Derecho_DAE
```

## 2. Desplegar nuevos cambios

```bash
git pull origin Deployment      # trae lo último
composer install --optimize-autoloader --no-dev
php artisan migrate --force     # aplica migraciones (ej. es_superadmin)
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build                   # compila Tailwind/Alpine (manifest Vite)
php artisan optimize:clear
php artisan up
```

> El VPS tenía cambios sin commitear en varios archivos. Antes de `git pull`
> resuelve con `git stash` (o `git checkout .`) para evitar conflictos.

## 3. Poblar procuradores (solo primera vez)

Crea 24 procuradores temporales con contraseña `Password123`:

```bash
php artisan db:seed --class=ProcuradoresTemporalesSeeder
```

## 4. Enviar correos de bienvenida a procuradores

```bash
# Ver qué se enviaría (dry-run, no manda nada)
php artisan procuradores:enviar-bienvenida --dry-run

# Enviar de verdad
php artisan procuradores:enviar-bienvenida

# Solo uno específico (por DNI o correo)
php artisan procuradores:enviar-bienvenida --dni=2180266@usap.edu

# Forzar envío aunque ya completaron perfil
php artisan procuradores:enviar-bienvenida --force
```

> **IMPORTANTE**: el `.env` del VPS debe tener `MAIL_MAILER=smtp` con
> credenciales reales de envío. Si está en `log`, los correos no saldrán.

## 5. Variables de entorno críticas (.env producción)

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://tu-dominio`
- `MAIL_MAILER=smtp` + `MAIL_HOST/PORT/USERNAME/PASSWORD/ENCRYPTION`
- `SANCTUM_*` y claves de sesión/config correctas.

## 6. Verificar

```bash
php artisan route:list --except-vendor
php artisan config:show app.name
```
