<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::withCount('casos')
            ->orderBy('usuario_nombre') // Quitamos orden por apellido porque ya no existe
            ->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rol_id'           => 'required|exists:roles,rol_id',
            'procurador_id'    => 'nullable|exists:procuradores,procurador_id',
            'usuario_nombre'   => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:usuarios,email',
            'contrasena'       => 'required|string|max:255',
        ]);

        $validated['usuario_estado'] = 'activo';

        // Recuerda encriptar la contraseña si tu modelo no lo hace automáticamente:
        // $validated['contrasena'] = bcrypt($validated['contrasena']);

        Usuario::create($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado exitosamente.');
    }

    // Buscamos por 'usuario_id' ya que eliminamos el 'usuario_dni'
    public function show(string $id)
    {
        $usuario = Usuario::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador'])
            ->where('usuario_id', $id)
            ->firstOrFail();

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();

        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, string $id)
    {
        $usuario = Usuario::where('usuario_id', $id)->firstOrFail();

        $validated = $request->validate([
            'rol_id'           => 'required|exists:roles,rol_id',
            'procurador_id'    => 'nullable|exists:procuradores,procurador_id',
            'usuario_nombre'   => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:usuarios,email,' . $usuario->usuario_id . ',usuario_id',
        ]);

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