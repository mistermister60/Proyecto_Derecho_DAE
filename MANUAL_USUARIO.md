# 📖 Manual de Usuario — Consultorio Jurídico DAE (USAP)

**Sistema de Gestión de Expedientes Judiciales**  
**Versión:** 1.0 | **Fecha:** Julio 2026  
**Desarrollado para:** Dirección de Asuntos Estudiantiles — Universidad de San Pedro Sula

---

## 📋 Tabla de Contenidos

1. [Introducción](#1-introducción)
2. [Acceso al Sistema](#2-acceso-al-sistema)
3. [Autenticación de Dos Factores (2FA)](#3-autenticación-de-dos-factores-2fa)
4. [Dashboard Principal](#4-dashboard-principal)
5. [Gestión de Casos](#5-gestión-de-casos)
6. [Gestión de Clientes](#6-gestión-de-clientes)
7. [Gestión de Demandados](#7-gestión-de-demandados)
8. [Gestión de Procuradores](#8-gestión-de-procuradores)
9. [Gestión de Usuarios](#9-gestión-de-usuarios)
10. [Agenda de Audiencias](#10-agenda-de-audiencias)
11. [Seguimientos y Documentos](#11-seguimientos-y-documentos)
12. [Entrevistas](#12-entrevistas)
13. [Reportes y PDFs](#13-reportes-y-pdfs)
14. [Perfil y Configuración](#14-perfil-y-configuración)
15. [Búsqueda Global](#15-búsqueda-global)
16. [Modo Oscuro / Claro](#16-modo-oscuro--claro)
17. [Instalación como App (PWA)](#17-instalación-como-app-pwa)
18. [Notificaciones Push](#18-notificaciones-push)
19. [Recuperación de Contraseña](#19-recuperación-de-contraseña)
20. [Atajos de Teclado](#20-atajos-de-teclado)
21. [Preguntas Frecuentes](#21-preguntas-frecuentes)

---

## 1. Introducción

### ¿Qué es este sistema?
El **Consultorio Jurídico DAE** es una aplicación web para la gestión integral de expedientes judiciales del Consultorio Jurídico de la Dirección de Asuntos Estudiantiles (DAE) de la Universidad de San Pedro Sula (USAP).

### Funcionalidades principales
- 📁 **Expedientes (Casos):** CRUD completo, reasignación, cierre con resolución
- 👥 **Clientes y Demandados:** Gestión de partes con datos personales, laborales y familiares
- ⚖️ **Procuradores:** Abogados del despacho con usuario asociado
- 📅 **Agenda:** Calendario de audiencias con filtrado por rol
- 📝 **Seguimientos:** Historial de actividades por caso
- 📄 **Documentos:** Adjuntos por expediente
- 🎤 **Entrevistas:** Registro de reuniones con clientes
- 📊 **Dashboard:** KPIs, gráficas de pipeline, tipo de trámite y resoluciones
- 🔍 **Búsqueda Global:** Typeahead en tiempo real sobre 8 entidades
- 🌙 **Modo Oscuro/Claro:** Persistente en localStorage
- 📱 **PWA:** Instalable, funciona offline, notificaciones push

### Roles de usuario
| Rol | Permisos |
|-----|----------|
| **Director** (rol_id=1) | Acceso total: crea casos, gestiona procuradores/usuarios, cierra/reasigna casos, ve todo |
| **Procurador** (rol_id=2) | Ve solo sus casos asignados, registra seguimientos/audiencias/documentos/entrevistas |

---

## 2. Acceso al Sistema

### URL de producción
```
https://consultorio-usap-production.laravel.cloud
```

### URL de desarrollo (Laragon)
```
http://proyecto-derecho-dae.test
```

### Pantalla de Login
![Login](docs/screenshots/01-login.png)

**Campos:**
- **Email:** Correo institucional (@usap.edu)
- **Contraseña:** Mínimo 8 caracteres, mayúscula, minúscula, número, símbolo

**Botones:**
- **Iniciar Sesión** — Valida credenciales
- **¿Olvidé mi contraseña?** — Enlace a recuperación

> **Nota:** No hay registro público. Los usuarios son creados por el Director.

---

## 3. Autenticación de Dos Factores (2FA)

### Flujo completo
```
1. Login con email + contraseña
        ↓
2. Si credenciales válidas → Se genera código OTP de 6 dígitos
        ↓
3. Código enviado al email institucional
        ↓
4. Pantalla "Verificar Código 2FA"
        ↓
5. Ingresar código → Acceso concedido
```

### Pantalla 2FA
![2FA](docs/screenshots/02-2fa.png)

**Detalles:**
- ✅ Código expira en **15 minutos**
- ✅ Máximo **5 intentos fallidos** → bloqueo 5 minutos
- ✅ El **Director** (`director@usap.edu`, correo ficticio) **omite 2FA** automáticamente; el resto de roles pasa por OTP
- ✅ Si es **primer login** → redirige a cambio obligatorio de contraseña

### Si no llega el código
1. Revisar carpeta **Spam/Correo no deseado**
2. Verificar que el email sea **@usap.edu**
3. Esperar 1-2 minutos (retraso de Brevo/SMTP)
4. Solicitar reenvío (botón "Reenviar código" — si implementado)

---

## 4. Dashboard Principal

### Acceso
Ruta: `/dashboard` (página principal tras login)

### Vista Director
![Dashboard Director](docs/screenshots/03-dashboard-director.png)

### Vista Procurador
![Dashboard Procurador](docs/screenshots/04-dashboard-procurador.png)

### KPIs (Tarjetas superiores)
| KPI | Descripción | Director | Procurador |
|-----|-------------|----------|------------|
| **Casos Activos** | Casos con estado 'activo' | Todos | Solo sus casos |
| **Cerrados** | Casos con estado 'cerrado' | Todos | Solo sus casos |
| **Total Casos** | Suma de todos los estados | Todos | Solo sus casos |
| **Nuevos Este Mes** | Creados en mes actual | Todos | Solo sus casos |
| **Audiencias Esta Semana** | Próximos 7 días | Todas | Solo sus audiencias |
| **Atrasados** | Estado 'Atrasado' y activos | Todos | Solo sus casos |

### Gráficas (Chart.js)
1. **Pipeline de Casos** — Barras por estado (orden `estado_orden`)
2. **Tipo de Trámite** — Dona por categoría
3. **Resoluciones** — Barras: Ganado, Perdido, Conciliado, Desistido, Desestimado

### Tabla: Carga por Procurador
- **Director:** Ve todos los procuradores con casos activos/totales
- **Procurador:** Ve solo su propia fila

### Próximas Audiencias
Lista de 5 audiencias más cercanas (hoy + 7 días)

---

## 5. Gestión de Casos

### Ruta
`/casos` — Lista principal

### Lista de Casos (Index)
![Casos Index](docs/screenshots/05-casos-index.png)

**Funciones:**
- 🔍 **Búsqueda:** Por expediente, cliente, DNI, juzgado
- 📄 **Paginación:** 20 por página
- 🎯 **Filtro Estado:** Activo / Cerrado / Inadmisible
- ➕ **Nuevo Caso** (solo Director) — Botón dorado en sidebar

**Columnas:**
| Columna | Descripción |
|---------|-------------|
| Expediente | Número único (formato: 0501-AÑO-CORRELATIVO) |
| Cliente | Nombre completo + DNI |
| Demandado | Nombre completo + DNI |
| Trámite | Tipo (Divorcio, Alimentos, etc.) |
| Estado | Badge coloreado (Pipeline / Cerrado / Inadmisible) |
| Procurador | Abogado asignado |
| Fecha | Fecha de interposición |
| Acciones | Ver / Editar / Cerrar / Reasignar / PDF |

### Crear Caso (Solo Director)
![Crear Caso](docs/screenshots/06-casos-create.png)

**Campos obligatorios:**
- **Número de Expediente** — Se autogenera si se deja vacío
- **Cliente** — Select con clientes activos
- **Demandado** — Select con demandados activos (opcional)
- **Tipo de Trámite** — Select obligatorio
- **Estado Inicial** — Select (default: Entrevista)
- **Procurador** — Select con procuradores activos
- **Parte Representada** — Texto libre
- **Juzgado** — Texto libre
- **Fecha Interpuesta** — Date picker
- **Relación de Hechos** — Textarea
- **Observaciones Director** — Textarea (solo Director ve/edita)
- **Admisible** — Checkbox (default: sí)

### Ver Caso (Show) — Expediente Completo
![Caso Show](docs/screenshots/07-casos-show.png)

**Secciones:**
1. **Datos del Expediente** — Todos los campos del caso
2. **Cliente** — Ficha completa + botón ver cliente
3. **Demandado** — Ficha completa (si existe)
4. **Procurador Asignado** — Datos + botón ver procurador
5. **Entrevistas** — Tabla cronológica + botón "Nueva Entrevista"
6. **Seguimientos** — Historial de actividades + botón "Nuevo Seguimiento"
7. **Audiencias** — Calendario + botón "Nueva Audiencia"
8. **Documentos** — Lista de archivos + botón "Subir Documento"
9. **Reasignaciones** — Historial de cambios de procurador
10. **Botones de Acción:** Editar / Cerrar / Reasignar / PDF Seguimiento

### Editar Caso
Similar a crear, pero precargado. Director puede cambiar **Admisible**.

### Cerrar Caso (Solo Director)
![Cerrar Caso](docs/screenshots/08-casos-cerrar.png)

**Datos de Resolución:**
- **Tipo:** Ganado / Perdido / Conciliado / Desistido / Desestimado
- **Fecha Resolución** — Date picker
- **Notas** — Textarea (opcional)

> Al cerrar: caso pasa a estado 'Cerrado', se guarda resolución, ya no aparece en activos.

### Reasignar Caso (Solo Director)
![Reasignar Caso](docs/screenshots/09-casos-reasignar.png)

**Proceso:**
1. Seleccionar **Procurador Destino** (excluye al actual)
2. Ingresar **Motivo** (obligatorio)
3. Confirmar → Se registra en tabla `reasignaciones` con fecha actual

### PDF Seguimiento
Botón "PDF Seguimiento" en vista show → Descarga `Seguimiento_{expediente}.pdf` con:
- Datos del caso
- Cliente / Demandado / Procurador
- Historial completo de seguimientos (más reciente primero)

---

## 6. Gestión de Clientes

### Ruta
`/clientes`

### Lista (Index)
![Clientes Index](docs/screenshots/10-clientes-index.png)

**Búsqueda:** Por DNI, teléfono, nombre, apellido  
**Filtro Estado:** Activo / Inactivo  
**Orden:** Apellido, Nombre

### Crear Cliente
![Crear Cliente](docs/screenshots/11-clientes-create.png)

**Campos:**
| Sección | Campos |
|---------|--------|
| **Datos Personales** | Nombre, Apellido, DNI, Estado Civil |
| **Contacto** | Teléfono, Dirección |
| **Familia** | Nº Hijos, Nombres de Hijos |
| **Laboral** | Profesión, Lugar Trabajo, Dirección Trabajo, Teléfono Trabajo, Salario Mensual |

> **Tip:** El campo "Nombre Completo" se divide automáticamente en Nombre (primera palabra) y Apellido (resto).

### Ver Cliente (Show)
Muestra ficha completa + **Casos Asociados** (tabla con estado, trámite, procurador)

### Editar Cliente
Precargado con datos actuales.

### Desactivar / Reactivar
- **Desactivar:** Cambia estado a 'inactivo' (eliminación lógica)
- **Reactivar:** Cambia estado a 'activo'
- El registro **nunca se borra** (integridad histórica)

---

## 7. Gestión de Demandados

### Ruta
`/demandados`

**Idéntico a Clientes** pero sin campos familiares (Nº Hijos, Nombres Hijos).

### Diferencias clave
- No tiene campo "Nombres de Hijos"
- Relación: Un demandado puede estar en múltiples casos

---

## 8. Gestión de Procuradores

### Ruta
`/procuradores` — **Solo Director**

### Lista (Index)
![Procuradores Index](docs/screenshots/12-procuradores-index.png)

**Búsqueda:** Por DNI, teléfono, nombre, apellido, email, carnet

### Crear Procurador (Solo Director)
![Crear Procurador](docs/screenshots/13-procuradores-create.png)

**Proceso automático:**
1. Completa formulario con datos del procurador
2. Sube **Foto** (opcional)
3. Al guardar:
   - Crea procurador en BD
   - **Crea usuario asociado** automáticamente
   - Genera contraseña temporal: `Procurador.{12chars}!`
   - Envía **email de bienvenida** con credenciales
   - Fuerza `debe_cambiar_contrasena = true`

**Campos:**
- Nombre, Apellido, DNI, Carnet (opcional)
- Fecha Nacimiento, Género
- Email, Teléfono, Dirección
- Foto (upload)

### Ver Procurador (Show)
Ficha completa + **Casos Asignados** + **Usuario Asociado**

### Editar Procurador
- Actualiza datos
- Nueva foto **reemplaza y borra la anterior** del storage

### Desactivar / Reactivar (Solo Director)
- Desactiva **procurador + usuario asociado** en transacción
- Reactiva ambos simultáneamente

### Constancia de Practicante (PDF)
Botón "Constancia" en vista show → Descarga `Constancia_{Nombre}.pdf` con:
- Datos del procurador
- Casos activos asignados
- Firma digital del Director

---

## 9. Gestión de Usuarios

### Ruta
`/usuarios` — **Solo Director**

### Lista (Index)
![Usuarios Index](docs/screenshots/14-usuarios-index.png)

**Filtros:** Búsqueda por nombre/email + Estado (Activo/Inactivo)

### Crear Usuario (Solo Director)
![Crear Usuario](docs/screenshots/15-usuarios-create.png)

**Campos:**
- Nombre completo
- Email (@usap.edu)
- Contraseña + Confirmación (regla: 8 chars, mayúscula, minúscula, número, símbolo)
- **Rol:** Director / Procurador
- **Procurador Asociado:** Select (solo si rol = Procurador)

**Si rol = Procurador:**
- Genera contraseña temporal
- Envía email de bienvenida
- Fuerza cambio en primer login

### Ver Usuario (Show)
Datos + Rol + Procurador asociado + Casos (si es procurador)

### Editar Usuario
- Actualiza nombre, email, rol, procurador
- **Contraseña:** Solo si se llena el campo (opcional)

### Desactivar / Reactivar
Eliminación lógica (estado 'inactivo' / 'activo')

### Resetear Contraseña (Solo Director)
Botón "Resetear Contraseña" en vista show:
1. Genera contraseña temporal segura
2. Asigna al usuario
3. Activa `debe_cambiar_contrasena = true`
4. **Muestra la contraseña en pantalla** para que el Director la copie y se la dé al usuario

---

## 10. Agenda de Audiencias

### Ruta
`/agenda`

### Vista Calendario
![Agenda](docs/screenshots/16-agenda.png)

**Características:**
- **Agrupación por Mes** (Y-m)
- **Próximas 10 Audiencias** (hoy + 7 días) en panel lateral
- **Filtro por Rol:**
  - Director: Ve TODAS las audiencias
  - Procurador: Solo audiencias de SUS casos

**Datos por Audiencia:**
- Tipo (Inicial, Pruebas, Sentencia, etc.)
- Fecha + Hora
- Juzgado
- Caso (expediente + cliente)
- Procurador asignado

### Crear Audiencia
Desde **Vista Caso (Show)** → Botón "Nueva Audiencia":
- Tipo (select)
- Fecha (date picker)
- Hora (time picker)
- Juzgado (texto)
- Observaciones (textarea)

### Eliminar Audiencia
Botón eliminar en fila → Confirmación SweetAlert2 → Borra registro

---

## 11. Seguimientos y Documentos

### Seguimientos (Historial de Actividad)
**Desde Vista Caso (Show)** → Botón "Nuevo Seguimiento"

![Nuevo Seguimiento](docs/screenshots/17-seguimiento-create.png)

**Campos:**
- **Tipo:** Audiencia / Escrito / Notificación / Diliguencia / Otro
- **Fecha** (date picker, default: hoy)
- **Descripción** (textarea, obligatorio)

> Se guarda con `usuario_id` del usuario actual automáticamente.

### Documentos (Adjuntos)
**Desde Vista Caso (Show)** → Botón "Subir Documento"

![Subir Documento](docs/screenshots/18-documento-upload.png)

**Campos:**
- **Archivo** (file upload: PDF, JPG, PNG, DOC, max 10MB)
- **Nombre** (auto: nombre del archivo)
- **Tipo** (select: Escrito / Prueba / Resolución / Otro)
- **Descripción** (textarea, opcional)

**Acciones en lista:**
- 📥 **Descargar** — Descarga el archivo original
- 🗑️ **Eliminar** — Borra registro + archivo del storage

---

## 12. Entrevistas

### Ruta
Desde **Vista Caso (Show)** → Botón "Nueva Entrevista"

### Crear Entrevista
![Nueva Entrevista](docs/screenshots/19-entrevista-create.png)

**Campos:**
- **Fecha** (date picker)
- **Relación de Hechos** (textarea, obligatorio) — Qué declaró el cliente
- **Observaciones** (textarea, opcional) — Notas del procurador

> Se asocia automáticamente al `procurador_id` del usuario actual.

### Ver / Eliminar
En la tabla de entrevistas del caso: ver detalles / eliminar con confirmación.

---

## 13. Reportes y PDFs

### PDF Seguimiento de Caso
**Desde Vista Caso (Show)** → Botón "PDF Seguimiento"
- Descarga: `Seguimiento_{EXPEDIENTE}.pdf`
- Contiene: Datos caso, cliente, demandado, procurador, TODOS los seguimientos cronológicos

### Constancia de Practicante
**Desde Vista Procurador (Show)** → Botón "Constancia" (Solo Director)
- Descarga: `Constancia_{NOMBRE_COMPLETO}.pdf`
- Contiene: Datos procurador, casos activos, firma Director

---

## 14. Perfil y Configuración

### Ruta
`/profile` — Accesible desde menú usuario (esquina superior derecha)

### Pestañas
1. **Información Personal** — Nombre, Email
2. **Contraseña** — Cambio voluntario (requiere contraseña actual)
3. **Eliminar Cuenta** — Desactivación lógica (requiere contraseña actual)

### Cambio de Contraseña
- Contraseña actual (obligatoria)
- Nueva contraseña + Confirmación (regla: 8 chars, mayúscula, minúscula, número, símbolo)

---

## 15. Búsqueda Global (Typeahead)

### Ubicación
Barra superior derecha (icono 🔍) — **Solo desktop (≥640px)**

### Funcionamiento
1. Escribe **mínimo 2 caracteres**
2. **Debounce 300ms** → Consulta `/api/search?q=...`
3. Resultados aparecen en dropdown categorizado

### Entidades buscadas (8)
| Tipo | Ícono | Busca en | Filtro Procurador |
|------|-------|----------|-------------------|
| **Caso** | 📁 | Expediente, Juzgado, Parte | ✅ Sí |
| **Cliente** | 👤 | Nombre, Apellido, DNI, Teléfono | ✅ Sí |
| **Demandado** | 👤 | Nombre, Apellido, DNI, Teléfono | ✅ Sí |
| **Procurador** | 👔 | Nombre, Apellido, DNI, Carnet, Email | ❌ No (solo Director ve) |
| **Audiencia** | 📅 | Tipo, Juzgado, Fecha | ✅ Sí |
| **Documento** | 📄 | Nombre, Descripción | ✅ Sí |
| **Entrevista** | 💬 | Hechos, Observaciones | ✅ Sí |
| **Seguimiento** | 🕐 | Descripción | ✅ Sí |

### Navegación
- **Mouse:** Hover resalta fila
- **Teclado:** ↑ ↓ para navegar, Enter para ir
- **Click:** Navega a vista show de la entidad

### Límite
**8 resultados por entidad** (máx 64 total) para mantener velocidad.

---

## 16. Modo Oscuro / Claro

### Botón
Esquina superior derecha (icono ☀️/🌙) — **Solo desktop**

### Comportamiento
- **Click:** Alterna modo
- **Persistencia:** `localStorage.setItem('darkMode', 'true/false')`
- **Anti-flash:** Script en `<head>` aplica clase `dark` ANTES de render
- **Meta theme-color:** Se actualiza dinámicamente (#1E3A5F claro / #0f172a oscuro)

### Variables CSS (Tailwind v4)
```css
:root {
  --color-sidebar-bg: #1E3A5F;
  --color-sidebar-text: #D1D5DB;
  --color-sidebar-hover-bg: #2563EB;
  --color-sidebar-active-bg: #2563EB;
  --color-sidebar-active-text: #FFFFFF;
  --color-topbar-bg: #FFFFFF;
  --color-topbar-border: #E5E7EB;
  --color-topbar-title: #111827;
  --color-topbar-icon: #6B7280;
  --color-topbar-icon-hover-bg: #F3F4F6;
  --color-input-bg: #FFFFFF;
  --color-input-border: #D1D5DB;
  --color-input-text: #111827;
  --color-dropdown-bg: #FFFFFF;
  --color-dropdown-border: #E5E7EB;
  --color-dropdown-divider: #F3F4F6;
  --color-dropdown-text: #111827;
  --color-dropdown-text-sec: #6B7280;
  --color-dropdown-hover: #F3F4F6;
  --color-search-result-bg: #F3F4F6;
  --color-search-section: #9CA3AF;
  --color-notif-badge: #EF4444;
  --color-flash-success-bg: #ECFDF5;
  --color-flash-success-border: #10B981;
  --color-flash-success-text: #065F46;
  --color-flash-error-bg: #FEF2F2;
  --color-flash-error-border: #EF4444;
  --color-flash-error-text: #DC2626;
}

.dark {
  --color-sidebar-bg: #0F172A;
  --color-sidebar-text: #9CA3AF;
  --color-sidebar-hover-bg: #1E3A5F;
  --color-sidebar-active-bg: #2563EB;
  --color-sidebar-active-text: #FFFFFF;
  --color-topbar-bg: #1E293B;
  --color-topbar-border: #334155;
  --color-topbar-title: #F1F5F9;
  --color-topbar-icon: #94A3B8;
  --color-topbar-icon-hover-bg: #334155;
  --color-input-bg: #1E293B;
  --color-input-border: #334155;
  --color-input-text: #F1F5F9;
  --color-dropdown-bg: #1E293B;
  --color-dropdown-border: #334155;
  --color-dropdown-divider: #334155;
  --color-dropdown-text: #F1F5F9;
  --color-dropdown-text-sec: #94A3B8;
  --color-dropdown-hover: #334155;
  --color-search-result-bg: #334155;
  --color-search-section: #64748B;
  --color-notif-badge: #EF4444;
  --color-flash-success-bg: #064E3B;
  --color-flash-success-border: #10B981;
  --color-flash-success-text: #A7F3D0;
  --color-flash-error-bg: #7F1D1D;
  --color-flash-error-border: #EF4444;
  --color-flash-error-text: #FCA5A5;
}
```

---

## 17. Instalación como App (PWA)

### Requisitos
- Navegador compatible: Chrome, Edge, Firefox, Safari (iOS 16.4+)
- HTTPS (producción) o localhost (desarrollo)

### Pasos
1. Abre el sistema en el navegador
2. Aparece **banner nativo** "Instalar Consultorio Jurídico USAP"
3. O usa botón **"Instalar App"** en footer (si visible)
3. Click **Instalar** → Se crea icono en escritorio/launcher
4. La app abre **sin barra de navegador** (standalone)

### Características PWA
- ✅ **Offline:** Service Worker cachea assets + páginas visitadas
- ✅ **Pantalla completa:** `display: standalone`
- ✅ **Iconos:** 12 tamaños (48x48 a 512x512) + Apple touch icons
- ✅ **Manifest:** `/manifest.json` con nombre, colores, shortcuts
- ✅ **Actualizaciones:** SW detecta nueva versión y notifica

### Archivos PWA
| Archivo | Propósito |
|---------|-----------|
| `public/sw.js` | Service Worker (cache, push, offline) |
| `public/manifest.json` | Manifiesto PWA |
| `public/offline.html` | Página offline personalizada |
| `public/icons/*.png` | 12 iconos + 3 Apple touch icons |

---

## 18. Notificaciones Push

### Activación
1. Click en **campana** (notificaciones) en topbar
2. Si no suscrito → Botón "Activar Notificaciones"
3. Navegador pide permiso → **Permitir**
4. Se registra suscripción en BD (endpoint + keys VAPID)

### Recepción
- **App en foreground:** Toast SweetAlert2 (esquina sup. der., 5s)
- **App en background/cerrada:** Notificación nativa del SO
- **Click en notificación:** Enfoca/abre la app

### Panel de Configuración
Ruta: `/api/notifications/settings` (accesible desde dropdown notificaciones)
- Estado: suscrito / no suscrito
- Botón para desuscribirse

### VAPID Keys
Configuradas en `config/pwa.php`:
- `vapid.public_key` — Para cliente (base64url)
- `vapid.private_key` — Para servidor (firma push)

---

## 19. Recuperación de Contraseña

### Flujo Auto-servicio
```
1. Login → "¿Olvidé mi contraseña?"
        ↓
2. Ingresa email institucional (@usap.edu)
        ↓
3. Rate limit: 3 intentos/hora por IP
        ↓
4. Se genera token seguro (60 chars) → hash SHA-256 en BD
        ↓
5. Email con enlace: /restablecer-contrasena/{token}
        ↓
6. Formulario: Nueva contraseña + Confirmación
        ↓
7. Valida token (expira en 60 min) + actualiza contraseña
        ↓
8. Redirige a Login con mensaje éxito
```

### Pantallas
| Paso | Ruta | Vista |
|------|------|-------|
| Solicitar | `/olvide-mi-contrasena` | `auth.forgot-password` |
| Restablecer | `/restablecer-contrasena/{token}` | `auth.reset-password` |

### Restricciones
- Solo emails **@usap.edu**
- Token expira en **60 minutos**
- Rate limit: **3 solicitudes/hora por IP**
- Contraseña nueva: **8 chars, mayúscula, minúscula, número, símbolo**

---

## 20. Atajos de Teclado

### Globales
| Atajo | Acción |
|-------|--------|
| `Ctrl + K` | Enfocar búsqueda global (si implementado) |
| `Escape` | Cerrar dropdowns / modales |
| `Tab` | Navegar entre elementos enfocables |

### Búsqueda Global (Typeahead)
| Tecla | Acción |
|-------|--------|
| `↑` / `↓` | Navegar resultados |
| `Enter` | Abrir resultado seleccionado |
| `Escape` | Cerrar dropdown |

### Formularios
| Tecla | Acción |
|-------|--------|
| `Enter` | Enviar formulario (si botón submit enfocado) |
| `Ctrl + S` | Guardar (si implementado) |

### SweetAlert2 (Confirmaciones)
| Tecla | Acción |
|-------|--------|
| `Enter` | Confirmar (botón resaltado) |
| `Escape` | Cancelar |

---

## 21. Preguntas Frecuentes (FAQ)

### Acceso y Login
**P: No puedo iniciar sesión, dice "Credenciales inválidas"**
R: Verifica email (@usap.edu) y contraseña. Si olvidaste la contraseña, usa "¿Olvidé mi contraseña?". Si tu cuenta está inactiva, contacta al Director.

**P: No me llega el código 2FA al email**
R: 1) Revisa Spam. 2) Verifica que sea @usap.edu. 3) Espera 2 min. 4) Si persiste, contacta a TI (revisar logs Brevo).

**P: Me sale "Demasiados intentos. Intente en X segundos"**
R: Rate limit de login (5 intentos → 5 min) o recuperación (3/hora). Espera el tiempo indicado.

### Casos
**P: No veo el botón "Nuevo Caso"**
R: Solo el **Director** puede crear casos. Los Procuradores solo ven sus casos asignados.

**P: No puedo editar el campo "Admisible"**
R: Solo el **Director** puede modificar la admisibilidad de un caso.

**P: ¿Qué pasa cuando cierro un caso?**
R: Pasa a estado 'Cerrado', se guarda la resolución (tipo, fecha, notas), y ya no aparece en listas de activos. Se conserva para histórico.

**P: ¿Puedo borrar un caso definitivamente?**
R: No. El sistema usa **desactivación lógica** (estado 'inactivo'). El registro se conserva para integridad histórica y auditoría.

### Clientes / Demandados / Procuradores
**P: ¿Por qué no puedo eliminar un cliente?**
R: Igual que casos: **eliminación lógica** (estado 'inactivo'). Usa "Desactivar" y "Reactivar".

**P: Al crear un Procurador, ¿se crea usuario automáticamente?**
R: **Sí.** Se crea usuario con rol Procurador, contraseña temporal, y se envía email de bienvenida con credenciales.

### Búsqueda
**P: La búsqueda global no encuentra nada**
R: 1) Mínimo 2 caracteres. 2) Si eres Procurador, solo busca en TUS casos. 3) Verifica ortografía.

**P: ¿Por qué no veo Procuradores en la búsqueda?**
R: Solo el **Director** ve procuradores en la búsqueda global.

### PWA y Notificaciones
**P: ¿Cómo instalo la app en el celular?**
R: Abre en Chrome/Edge → Menú (⋮) → "Instalar app" o "Añadir a pantalla de inicio".

**P: Las notificaciones push no funcionan**
R: 1) Verifica que diste permiso en el navegador. 2) Revisa panel de notificaciones (campana) → "Activar Notificaciones". 3) En iOS: requiere iOS 16.4+ y app instalada como PWA.

**P: ¿Funciona offline?**
R: **Sí.** El Service Worker cachea assets y páginas visitadas. Puedes navegar por páginas ya cargadas sin internet.

### Modo Oscuro
**P: El modo oscuro no se guarda**
R: Verifica que `localStorage` no esté bloqueado (modo incógnito, configuración de privacidad). El script en `<head>` lee `localStorage.getItem('darkMode')` al cargar.

---

## 📞 Soporte y Contacto

| Problema | Contacto |
|----------|----------|
| **Acceso / Credenciales** | Director del Consultorio |
| **Errores técnicos / Bugs** | Equipo de Desarrollo / TI USAP |
| **Email no llega (2FA, recuperación)** | Revisar Spam → TI USAP (logs Brevo) |
| **Sugerencias / Mejoras** | Director + Equipo Desarrollo |

### Logs del Sistema
- **Aplicación:** `storage/logs/laravel.log`
- **Auditoría:** `storage/logs/audit-YYYY-MM-DD.log`
- **Navegador:** `storage/logs/browser.log`

---

## 📝 Historial de Versiones

| Versión | Fecha | Cambios Principales |
|---------|-------|---------------------|
| 1.0 | Julio 2026 | Lanzamiento inicial: CRUDs completos, 2FA, Dashboard, PWA, Búsqueda Global, PDFs, Modo Oscuro |

---

## 📄 Licencia y Créditos

**Desarrollado para:** Consultorio Jurídico DAE — Universidad de San Pedro Sula  
**Stack:** Laravel 13, PHP 8.5, MySQL 8, Tailwind CSS v4, Alpine.js v3, Chart.js, SweetAlert2, DomPDF, Laravel Sanctum  
**Despliegue:** Laravel Cloud (auto-deploy desde GitHub)

---

*Fin del Manual de Usuario*