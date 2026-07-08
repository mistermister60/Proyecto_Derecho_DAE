<?php

namespace App\Services;

use App\Exceptions\AccountInactiveException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\RateLimitExceededException;
use App\Http\DTOs\AuthResponse;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * AuthService — Servicio de autenticación con control de intentos y rate limiting.
 *
 * Gestiona el ciclo completo de inicio y cierre de sesión del sistema DAE.
 * Incorpora protección contra fuerza bruta mediante rate limiting con caché,
 * validación de credenciales contra el modelo Usuario, verificación de estado
 * de la cuenta y generación de tokens de acceso efímeros.
 *
 * Cada intento fallido incrementa un contador en caché; al superar el límite
 * configurado se bloquea temporalmente el acceso desde esa dirección de email.
 */
class AuthService
{
    /**
     * Prefijo para la clave de caché que almacena los intentos de login.
     *
     * @var string
     */
    private const RATE_LIMIT_KEY_PREFIX = 'login_attempts:';

    /**
     * Número máximo de intentos fallidos permitidos antes de aplicar el rate limit.
     *
     * @var int
     */
    private const RATE_LIMIT_MAX_ATTEMPTS = 5;

    /**
     * Duración en segundos (300 = 5 minutos) que permanece el bloqueo por rate limit.
     *
     * @var int
     */
    private const RATE_LIMIT_EXPIRY = 300; // 5 minutes

    /**
     * Duración en segundos (3600 = 1 hora) de validez del token generado.
     *
     * @var int
     */
    private const TOKEN_EXPIRY = 3600; // 1 hour

    /**
     * Intentar iniciar sesión con las credenciales proporcionadas.
     *
     * Ejecuta en orden: verificación de rate limit (por email e IP),
     * búsqueda del usuario, validación de contraseña, validación de
     * estado de cuenta, login, generación de token y limpieza del
     * contador de intentos.
     *
     * @param  string  $email  Correo electrónico del usuario.
     * @param  string  $password  Contraseña en texto plano.
     * @return AuthResponse DTO con token, expiración, datos del usuario, roles y permisos.
     *
     * @throws RateLimitExceededException Si se superaron los intentos permitidos.
     * @throws InvalidCredentialsException Si el email o la contraseña son incorrectos.
     * @throws AccountInactiveException Si la cuenta del usuario está desactivada.
     */
    public function attemptLogin(string $email, string $password): AuthResponse
    {
        $ip = request()->ip();

        $this->checkRateLimit($email, $ip);

        $usuario = $this->findUserByEmail($email);
        $this->validateCredentials($usuario, $password, $email, $ip);
        $this->validateAccountStatus($usuario);

        Auth::login($usuario);

        $token = $this->generateToken($usuario);
        $this->recordSuccessfulLogin($email, $ip);

        return $this->createAuthResponse($usuario, $token);
    }

    /**
     * Cerrar sesión y revocar todos los tokens del usuario.
     *
     * Elimina la sesión de Laravel y todos los tokens Sanctum asociados
     * al usuario, forzando una reautenticación completa en el próximo acceso.
     *
     * @param  string  $userId  ID del usuario que cierra sesión.
     */
    public function logout(string $userId): void
    {
        $usuario = Usuario::findOrFail($userId);
        Auth::logout();
        $usuario->tokens()->delete();
    }

    /**
     * Verificar que no se haya excedido el límite de intentos de login.
     *
     * Consulta la caché con la clave por email y con la clave combinada
     * email+IP. Si cualquiera de los dos contadores iguala o supera el
     * máximo permitido, se bloquea el acceso.
     *
     * @param  string  $email  Correo electrónico contra el que se verifica el rate limit.
     * @param  string  $ip  Dirección IP del cliente.
     *
     * @throws RateLimitExceededException Si los intentos alcanzaron el límite máximo.
     */
    private function checkRateLimit(string $email, string $ip): void
    {
        $key = $this->getRateLimitKey($email);
        $attempts = Cache::get($key, 0);

        if ($attempts >= self::RATE_LIMIT_MAX_ATTEMPTS) {
            throw new RateLimitExceededException;
        }

        $keyWithIp = $this->getRateLimitKey($email, $ip);
        $attemptsWithIp = Cache::get($keyWithIp, 0);

        if ($attemptsWithIp >= self::RATE_LIMIT_MAX_ATTEMPTS) {
            throw new RateLimitExceededException;
        }
    }

    /**
     * Buscar un usuario por su dirección de correo electrónico.
     *
     * @param  string  $email  Correo electrónico a buscar.
     * @return Usuario|null Modelo del usuario si existe, null en caso contrario.
     */
    private function findUserByEmail(string $email): ?Usuario
    {
        return Usuario::where('email', $email)->first();
    }

    /**
     * Validar que las credenciales coincidan con un usuario existente.
     *
     * Si el usuario no existe o la contraseña no coincide, registra un
     * intento fallido en caché (con email e IP) y lanza una excepción
     * genérica para no revelar qué campo es incorrecto (seguridad por
     * oscuridad).
     *
     * @param  Usuario|null  $usuario  Modelo del usuario encontrado o null.
     * @param  string  $password  Contraseña en texto plano a verificar.
     * @param  string  $email  Correo usado para registrar el intento fallido.
     * @param  string  $ip  Dirección IP del cliente.
     *
     * @throws InvalidCredentialsException Si el usuario no existe o la contraseña es incorrecta.
     */
    private function validateCredentials(?Usuario $usuario, string $password, string $email, string $ip): void
    {
        if (! $usuario || ! $this->isValidPassword($usuario->contrasena, $password)) {
            $this->recordFailedLoginAttempt($email, $ip);
            throw new InvalidCredentialsException;
        }
    }

    /**
     * Verificar que la contraseña en texto plano coincida con el hash almacenado.
     *
     * Utiliza el driver de hash configurado en Laravel (bcrypt por defecto)
     * para la comparación segura sin revelar el hash original.
     *
     * @param  string  $hashedPassword  Hash almacenado del usuario.
     * @param  string  $plainPassword  Contraseña en texto plano ingresada.
     * @return bool True si coinciden, false en caso contrario.
     */
    private function isValidPassword(string $hashedPassword, string $plainPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword);
    }

    /**
     * Validar que la cuenta del usuario esté activa.
     *
     * El sistema DAE permite desactivar cuentas (ej. baja de abogado,
     * suspensión temporal). Si el estado es distinto de "activo" se
     * deniega el acceso aunque las credenciales sean correctas.
     *
     * @param  Usuario  $usuario  Modelo del usuario autenticado.
     *
     * @throws AccountInactiveException Si el usuario no está en estado "activo".
     */
    private function validateAccountStatus(Usuario $usuario): void
    {
        if ($usuario->usuario_estado !== 'activo') {
            throw new AccountInactiveException;
        }
    }

    /**
     * Generar un token de acceso aleatorio y persistirlo en la tabla de tokens.
     *
     * Crea un string aleatorio de 60 caracteres, lo hashea con SHA-256
     * para almacenarlo de forma segura y devuelve el token en texto plano
     * para que el cliente lo utilice en peticiones subsecuentes.
     *
     * @param  Usuario  $usuario  Modelo del usuario al que pertenece el token.
     * @return string Token en texto plano para el cliente.
     */
    private function generateToken(Usuario $usuario): string
    {
        $token = Str::random(60);
        $usuario->tokens()->create([
            'name' => 'auth_token',
            'token' => hash('sha256', $token),
            'abilities' => ['*'],
        ]);

        return $token;
    }

    /**
     * Registrar un inicio de sesión exitoso limpiando el rate limit.
     *
     * Al ocurrir un login exitoso se elimina el contador de intentos
     * fallidos asociado al email y a la combinación email+IP,
     * permitiendo que futuros intentos comiencen desde cero.
     *
     * @param  string  $email  Correo del usuario que inició sesión exitosamente.
     * @param  string  $ip  Dirección IP del cliente.
     */
    private function recordSuccessfulLogin(string $email, string $ip): void
    {
        $this->clearRateLimit($email, $ip);
    }

    /**
     * Incrementar el contador de intentos fallidos en caché.
     *
     * Cada vez que falla la autenticación se suma 1 al contador almacenado
     * en caché con expiración de 5 minutos. Incrementa tanto la clave por
     * email como la combinada email+IP para proteger contra ataques
     * distribuidos desde múltiples direcciones IP.
     *
     * @param  string  $email  Correo del usuario que falló la autenticación.
     * @param  string  $ip  Dirección IP del cliente.
     */
    private function recordFailedLoginAttempt(string $email, string $ip): void
    {
        $key = $this->getRateLimitKey($email);
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, self::RATE_LIMIT_EXPIRY);

        $keyWithIp = $this->getRateLimitKey($email, $ip);
        $attemptsWithIp = Cache::get($keyWithIp, 0) + 1;
        Cache::put($keyWithIp, $attemptsWithIp, self::RATE_LIMIT_EXPIRY);
    }

    /**
     * Eliminar el contador de rate limit para un email e IP.
     *
     * Se invoca tras un login exitoso para que el usuario no arrastre
     * intentos fallidos previos en futuras autenticaciones.
     * Limpia tanto la clave por email como la combinada email+IP.
     *
     * @param  string  $email  Correo del usuario cuyo rate limit se limpia.
     * @param  string  $ip  Dirección IP del cliente.
     */
    private function clearRateLimit(string $email, string $ip): void
    {
        $key = $this->getRateLimitKey($email);
        Cache::forget($key);

        $keyWithIp = $this->getRateLimitKey($email, $ip);
        Cache::forget($keyWithIp);
    }

    /**
     * Obtener la clave de caché completa para el rate limit de un email e IP.
     *
     * Si se proporciona una IP, genera la clave "login_attempts:email:ip".
     * Si no, genera solo "login_attempts:email" como fallback.
     *
     * @param  string  $email  Correo del usuario.
     * @param  string|null  $ip  Dirección IP del cliente (opcional).
     * @return string Clave completa para la caché.
     */
    private function getRateLimitKey(string $email, ?string $ip = null): string
    {
        return $ip !== null
            ? self::RATE_LIMIT_KEY_PREFIX.$email.':'.$ip
            : self::RATE_LIMIT_KEY_PREFIX.$email;
    }

    /**
     * Construir el DTO de respuesta de autenticación.
     *
     * Compone un AuthResponse con el token generado, el tiempo de expiración,
     * los datos públicos del usuario, y sus permisos y roles asociados
     * para que el frontend pueda renderizar la UI según el perfil.
     *
     * @param  Usuario  $usuario  Modelo del usuario autenticado.
     * @param  string  $token  Token de acceso en texto plano.
     * @return AuthResponse DTO estructurado para la respuesta JSON.
     */
    private function createAuthResponse(Usuario $usuario, string $token): AuthResponse
    {
        return new AuthResponse(
            token: $token,
            expiresIn: self::TOKEN_EXPIRY,
            user: [
                'id' => $usuario->usuario_id,
                'nombre' => $usuario->usuario_nombre,
                'email' => $usuario->email,
                'estado' => $usuario->usuario_estado,
            ],
            permissions: $this->getUserPermissions($usuario),
            roles: $this->getUserRoles($usuario),
        );
    }

    /**
     * Obtener la lista de permisos asociados al rol del usuario.
     *
     * Los permisos se almacenan como JSON en el modelo del rol.
     * Si el usuario no tiene rol asignado se devuelve un array vacío.
     *
     * @param  Usuario  $usuario  Modelo del usuario autenticado.
     * @return array<string> Lista de permisos planos.
     */
    private function getUserPermissions(Usuario $usuario): array
    {
        return $usuario->rol ? $usuario->rol->permisos ?? [] : [];
    }

    /**
     * Obtener el nombre del rol asignado al usuario.
     *
     * @param  Usuario  $usuario  Modelo del usuario autenticado.
     * @return array<string> Array con un único elemento: el nombre del rol.
     */
    private function getUserRoles(Usuario $usuario): array
    {
        return $usuario->rol ? [$usuario->rol->rol_nombre] : [];
    }
}
