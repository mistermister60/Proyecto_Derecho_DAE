# setup.ps1
# Script de Provisionamiento y Orquestación - Proyecto Derecho DAE
# Este script asegura que las herramientas existan, instala dependencias y limpia el servidor.

$ErrorActionPreference = "Stop"

function Write-Step([string]$Message) {
    Write-Host "`n🚀 $Message" -ForegroundColor Cyan
}

function Write-Success([string]$Message) {
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Write-ErrorMsg([string]$Message) {
    Write-Host "❌ $Message" -ForegroundColor Red
}

Write-Host "===============================================================" -ForegroundColor Magenta
Write-Host "   SISTEMA DE AUTO-CONFIGURACIÓN PROYECTO DERECHO DAE" -ForegroundColor Magenta
Write-Host "===============================================================" -ForegroundColor Magenta

try {
    # --- FASE 1: VERIFICACIÓN Y AUTO-DESCARGA DE HERRAMIENTAS ---
    Write-Step "Verificando herramientas base..."

    # Verificar PHP
    if (!(Get-Command php -ErrorAction SilentlyContinue)) {
        throw "PHP no está instalado o no está en el PATH. Por favor, inicia Laragon y asegúrate de que PHP esté activo."
    }
    Write-Success "PHP detectado."

    # Verificar NPM
    if (!(Get-Command npm -ErrorAction SilentlyContinue)) {
        throw "NPM (Node.js) no está instalado. Por favor, instala Node.js para poder compilar los assets."
    }
    Write-Success "NPM detectado."

    # Verificar y Auto-descargar Composer
    if (!(Get-Command composer -ErrorAction SilentlyContinue)) {
        Write-Host "Composer no detectado. Intentando descarga automática..." -ForegroundColor Yellow
        Invoke-WebRequest -Uri "https://getcomposer.org/installer" -OutFile "composer-setup.php"
        php composer-setup.php
        Remove-Item "composer-setup.php"
        Write-Success "Composer (composer.phar) descargado exitosamente."
        $ComposerCmd = "php composer.phar"
    } else {
        Write-Success "Composer detectado."
        $ComposerCmd = "composer"
    }

    # --- FASE 2: LIMPIEZA DEL SERVIDOR ---
    Write-Step "Limpiando el servidor y cachés..."
    # Usamos try/catch interno para que si una caché está vacía no detenga todo el script
    try { 
        php artisan optimize:clear
        php artisan cache:clear
        php artisan config:clear
        php artisan view:clear
        php artisan route:clear
    } catch { Write-Host "Aviso: Algunas cachés ya estaban limpias." -ForegroundColor Gray }
    
    if (Test-Path "storage/logs/*.log") {
        Remove-Item "storage/logs/*.log"
        Write-Host "Logs antiguos eliminados." -ForegroundColor Gray
    }

    # --- FASE 3: AUTO-DESCARGA DE DEPENDENCIAS (EL CORAZÓN DEL SISTEMA) ---
    Write-Step "Sincronizando dependencias de PHP (Composer)..."
    Invoke-Expression "$ComposerCmd install --no-interaction --prefer-dist"

    Write-Step "Sincronizando dependencias de JS (NPM)..."
    npm install

    # --- FASE 4: COMPILACIÓN Y BASE DE DATOS ---
    Write-Step "Compilando assets finales..."
    npm run build

    Write-Step "Actualizando base de datos..."
    php artisan migrate --force

    # --- FASE 5: OPTIMIZACIÓN FINAL ---
    Write-Step "Optimizando el sistema para arranque rápido..."
    php artisan optimize

    Write-Host "`n===============================================================" -ForegroundColor Magenta
    Write-Success "EL SISTEMA ESTÁ LISTO Y LIMPIO"
    Write-Host "Puedes iniciar el servidor y navegar en la aplicación." -ForegroundColor Cyan
    Write-Host "===============================================================" -ForegroundColor Magenta

} catch {
    Write-ErrorMsg "FALLO CRÍTICO EN LA CONFIGURACIÓN"
    Write-Host "Detalle: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
