<?php

namespace App\Http\Controllers;

use App\Models\Procurador;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
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

    public function create()
    {
        $roles = Rol::all();
        $procuradores = Procurador::where('procurador_estado', 'activo')->orderBy('procurador_nombre')->get();

        return view('usuarios.create', compact('roles', 'procuradores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rol_id' => 'required|exists:roles,rol_id',
            'procurador_id' => 'nullable|exists:procuradores,procurador_id',
            'usuario_nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email',
            'contrasena' => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()],
        ]);

        $validated['usuario_estado'] = 'activo';
        $validated['contrasena'] = Hash::make($validated['contrasena']); // 🔐 Encriptada

        Usuario::create($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado exitosamente.');
    }

    public function show(string $id)
    {
        $usuario = Usuario::with(['rol', 'procurador'])
            ->where('usuario_id', $id)
            ->firstOrFail();

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();
        $roles = Rol::all();
        $procuradores = Procurador::where('procurador_estado', 'activo')->orderBy('procurador_nombre')->get();

        return view('usuarios.edit', compact('usuario', 'roles', 'procuradores'));
    }

    public function update(Request $request, string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();

        $rules = [
            'rol_id' => 'required|exists:roles,rol_id',
            'procurador_id' => 'nullable|exists:procuradores,procurador_id',
            'usuario_nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:usuarios,email,'.$usuario->usuario_id.',usuario_id',
        ];

        // Solo validar y actualizar contraseña si se envía una nueva
        if ($request->filled('contrasena')) {
            $rules['contrasena'] = [\Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()];
        }

        $validated = $request->validate($rules);

        if ($request->filled('contrasena')) {
            $validated['contrasena'] = Hash::make($request->contrasena);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.show', $id)
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();
        $usuario->update(['usuario_estado' => 'inactivo']);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario desactivado exitosamente. El registro se conserva en el sistema.');
    }

    public function activar(string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();
        $usuario->update(['usuario_estado' => 'activo']);

        return redirect()->route('usuarios.show', $id)
            ->with('success', 'Usuario reactivado exitosamente.');
    }
}
