<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuariosRequest;
use App\Http\Requests\UpdateUsuariosRequest;
use App\Models\Procurador;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Controlador para la gestión de usuarios del sistema.
 *
 * CRUD completo de usuarios con filtro por estado (activo/inactivo) y
 * búsqueda por nombre o email. La actualización de contraseña es condicional:
 * solo se valida y procesa si se envía un nuevo valor. Usa la regla de
 * validación Password de Laravel para reforzar seguridad.
 */
class UsuariosController extends Controller
{
    /**
     * Lista los usuarios con paginación, búsqueda y filtro por estado.
     *
     * Incluye relaciones con rol y procurador. Filtra por estado 'activo'
     * o 'inactivo' si el parámetro está presente. Busca por nombre o email.
     *
     * @param  Request  $request  Contiene parámetros opcionales 'search' y 'estado'
     * @return View Vista index con usuarios paginados
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $estado = $request->query('estado', 'activo');

        $usuarios = Usuario::with('rol', 'procurador')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('usuario_nombre', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($estado, ['activo', 'inactivo']), function ($query) use ($estado) {
                $query->where('usuario_estado', $estado);
            })
            ->orderBy('usuario_nombre')
            ->paginate(20);

        return view('usuarios.index', compact('usuarios', 'estado'));
    }

    /**
     * Muestra el formulario de creación de un nuevo usuario.
     *
     * Carga el catálogo de roles y la lista de procuradores activos
     * para los campos select del formulario.
     *
     * @return View Vista create con roles y procuradores
     */
    public function create()
    {
        $roles = Rol::all();
        $procuradores = Procurador::where('procurador_estado', 'activo')->orderBy('procurador_nombre')->get();

        return view('usuarios.create', compact('roles', 'procuradores'));
    }

    /**
     * Registra un nuevo usuario en el sistema.
     *
     * Valida los datos incluyendo la regla Password (mínimo 8 caracteres,
     * mayúsculas, minúsculas y números). Encripta la contraseña con Hash::make()
     * y asigna estado 'activo' por defecto.
     *
     * @param  Request  $request  Datos del usuario con confirmación de contraseña
     * @return RedirectResponse Redirección al índice con mensaje
     */
    public function store(StoreUsuariosRequest $request)
    {
        $validated = $request->validated();

        $validated['usuario_estado'] = 'activo';
        $validated['contrasena'] = Hash::make($validated['contrasena']); // 🔐 Encriptada

        Usuario::create($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un usuario con sus relaciones.
     *
     * Realiza eager loading de rol y procurador asociado.
     *
     * @param  string  $id  ID numérico del usuario
     * @return View Vista show con usuario y relaciones
     *
     * @throws ModelNotFoundException Si el ID no existe
     */
    public function show(string $id)
    {
        $usuario = Usuario::with(['rol', 'procurador'])
            ->where('usuario_id', $id)
            ->firstOrFail();

        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Muestra el formulario de edición de un usuario.
     *
     * Carga el catálogo de roles y procuradores activos para los selects.
     *
     * @param  string  $id  ID numérico del usuario
     * @return View Vista edit con datos del usuario, roles y procuradores
     *
     * @throws ModelNotFoundException Si el ID no existe
     */
    public function edit(string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();
        $roles = Rol::all();
        $procuradores = Procurador::where('procurador_estado', 'activo')->orderBy('procurador_nombre')->get();

        return view('usuarios.edit', compact('usuario', 'roles', 'procuradores'));
    }

    /**
     * Actualiza los datos de un usuario existente.
     *
     * La validación de contraseña es condicional: solo se aplica la regla
     * Password si el campo 'contrasena' está presente. Si se envía nueva
     * contraseña, se encripta antes de actualizar.
     *
     * @param  Request  $request  Datos actualizados del usuario
     * @param  string  $id  ID numérico del usuario
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el ID no existe
     */
    public function update(UpdateUsuariosRequest $request, string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();

        $validated = $request->validated();

        if ($request->filled('contrasena')) {
            $validated['contrasena'] = Hash::make($request->contrasena);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.show', $id)
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Desactiva un usuario (eliminación lógica).
     *
     * Cambia el estado del usuario a 'inactivo'. El registro se conserva
     * en la base de datos para integridad histórica.
     *
     * @param  string  $id  ID numérico del usuario
     * @return RedirectResponse Redirección al índice con mensaje
     *
     * @throws ModelNotFoundException Si el ID no existe
     */
    public function destroy(string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();
        $usuario->update(['usuario_estado' => 'inactivo']);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario desactivado exitosamente. El registro se conserva en el sistema.');
    }

    /**
     * Reactiva un usuario previamente desactivado.
     *
     * Cambia el estado del usuario a 'activo'.
     *
     * @param  string  $id  ID numérico del usuario
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el ID no existe
     */
    public function activar(string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();
        $usuario->update(['usuario_estado' => 'activo']);

        return redirect()->route('usuarios.show', $id)
            ->with('success', 'Usuario reactivado exitosamente.');
    }

    /**
     * Restablece la contraseña de un usuario por el Administrador (Director).
     *
     * Genera una contraseña temporal segura, la asigna al usuario y activa
     * la bandera 'debe_cambiar_contrasena' para que el usuario sea forzado
     * a cambiarla en su primer inicio de sesión (flujo OTP + cambio obligatorio).
     * Conserva toda la información del usuario (rol, procurador, datos personales).
     *
     * @param  string  $id  ID numérico del usuario
     * @return RedirectResponse Redirección a vista show con la contraseña temporal
     *
     * @throws ModelNotFoundException Si el ID no existe
     */
    public function resetPassword(string $id): RedirectResponse
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();

        // Generar contraseña temporal segura (12 caracteres, mayúsculas, minúsculas, números, símbolos)
        $tempPassword = Str::random(12);
        // Asegurar que cumpla con la política: mayúscula, minúscula, número, símbolo
        $tempPassword = 'P@ss' . Str::random(8) . rand(100, 999);

        $usuario->contrasena = Hash::make($tempPassword);
        $usuario->debe_cambiar_contrasena = true; // Forzar cambio en primer login
        $usuario->save();

        return redirect()->route('usuarios.show', $id)
            ->with('success', 'Contraseña restablecida por el administrador.')
            ->with('temp_password', $tempPassword); // Mostrar en la vista para que el admin la copie
    }
}
