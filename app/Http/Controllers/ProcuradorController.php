<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcuradorRequest;
use App\Http\Requests\UpdateProcuradorRequest;
use App\Models\Procurador;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controlador para la gestión de procuradores.
 *
 * CRUD completo de procuradores con creación simultánea de usuario asociado.
 * La creación, desactivación y reactivación usan DB::transaction para
 * mantener consistencia entre las tablas procuradores y usuarios.
 * Solo accesible para usuarios con rol Director. La búsqueda de registros
 * se realiza por DNI (no por ID).
 */
class ProcuradorController extends Controller
{
    /**
     * Lista los procuradores con paginación y búsqueda.
     *
     * Incluye contador de casos asociados. Busca por DNI, teléfono, nombre,
     * apellido o email. Ordena por apellido y nombre.
     *
     * @param  Request  $request  Contiene el parámetro opcional 'search'
     * @return View Vista index con procuradores paginados
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $procuradores = Procurador::withCount('casos')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('procurador_dni', 'like', "%{$search}%")
                        ->orWhere('procurador_telefono', 'like', "%{$search}%")
                        ->orWhere('procurador_nombre', 'like', "%{$search}%")
                        ->orWhere('procurador_apellido', 'like', "%{$search}%")
                        ->orWhere('procurador_email', 'like', "%{$search}%");
                });
            })
            ->orderBy('procurador_apellido')
            ->orderBy('procurador_nombre')
            ->paginate(20);

        return view('procuradores.index', compact('procuradores'));
    }

    /**
     * Muestra el formulario de creación de un nuevo procurador.
     *
     * @return View Vista create del formulario
     */
    public function create()
    {
        return view('procuradores.create');
    }

    /**
     * Registra un nuevo procurador y su usuario asociado.
     *
     * Valida datos personales y profesionales. Dentro de una transacción
     * crea el procurador y simultáneamente genera un usuario con rol
     * 'Procurador', contraseña temporal 'Procurador.{dni}!' y estado activo.
     *
     * @param  Request  $request  Datos del procurador
     * @return RedirectResponse Redirección al índice con mensaje
     *
     * @throws \Throwable Si la transacción falla, revierte ambas inserciones
     */
    public function store(StoreProcuradorRequest $request)
    {
        $validated = $request->validated();

        $validated['procurador_estado'] = 'activo';

        DB::transaction(function () use ($validated) {
            $procurador = Procurador::create($validated);
            Usuario::create([
                'rol_id' => Rol::where('rol_nombre', 'Procurador')->value('rol_id'),
                'procurador_id' => $procurador->procurador_id,
                'usuario_nombre' => $validated['procurador_nombre'].' '.$validated['procurador_apellido'],
                'email' => $validated['procurador_correo'],
                'contrasena' => Hash::make($validated['procurador_nombre'] . substr($validated['procurador_dni'], -4)),
                'usuario_estado' => 'activo',
            ]);
        });

        return redirect()->route('procuradores.index')
            ->with('success', 'Procurador y usuario registrados exitosamente.');
    }

    /**
     * Muestra los detalles de un procurador con sus casos y usuario asociado.
     *
     * Realiza eager loading de casos (con estado, tipo de trámite y
     * procurador) y del usuario vinculado.
     *
     * @param  string  $identidad  Número de DNI del procurador
     * @return View Vista show con procurador y relaciones
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function show(string $identidad)
    {
        $procurador = Procurador::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador', 'usuario'])
            ->where('procurador_dni', $identidad)
            ->firstOrFail();

        return view('procuradores.show', compact('procurador'));
    }

    /**
     * Muestra el formulario de edición de un procurador.
     *
     * @param  string  $identidad  Número de DNI del procurador
     * @return View Vista edit con datos del procurador
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function edit(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();

        return view('procuradores.edit', compact('procurador'));
    }

    /**
     * Actualiza los datos de un procurador existente.
     *
     * Valida campos editables incluyendo unicidad de DNI, carnet y email
     * (excluyendo el registro actual). No modifica el usuario asociado.
     *
     * @param  Request  $request  Datos actualizados del procurador
     * @param  string  $identidad  Número de DNI del procurador
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function update(UpdateProcuradorRequest $request, string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();

        $validated = $request->validated();

        $procurador->update($validated);

        return redirect()->route('procuradores.show', $identidad)
            ->with('success', 'Procurador actualizado exitosamente.');
    }

    /**
     * Desactiva un procurador y su usuario asociado (eliminación lógica).
     *
     * Dentro de una transacción cambia el estado del procurador y, si existe,
     * del usuario vinculado a 'inactivo'. Los registros se conservan para
     * integridad histórica.
     *
     * @param  string  $identidad  Número de DNI del procurador
     * @return RedirectResponse Redirección al índice con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     * @throws \Throwable Si la transacción falla, revierte ambas actualizaciones
     */
    public function destroy(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();

        DB::transaction(function () use ($procurador) {
            $procurador->update(['procurador_estado' => 'inactivo']);
            if ($procurador->usuario) {
                $procurador->usuario->update(['usuario_estado' => 'inactivo']);
            }
        });

        return redirect()->route('procuradores.index')
            ->with('success', 'Procurador desactivado exitosamente. El registro se conserva en el sistema.');
    }

    /**
     * Reactiva un procurador y su usuario asociado.
     *
     * Dentro de una transacción cambia el estado del procurador y, si existe,
     * del usuario vinculado a 'activo'.
     *
     * @param  string  $identidad  Número de DNI del procurador
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     * @throws \Throwable Si la transacción falla, revierte ambas actualizaciones
     */
    public function activar(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();

        DB::transaction(function () use ($procurador) {
            $procurador->update(['procurador_estado' => 'activo']);

            if ($procurador->usuario) {
                $procurador->usuario->update(['usuario_estado' => 'activo']);
            }
        });

        return redirect()->route('procuradores.show', $identidad)
            ->with('success', 'Procurador reactivado exitosamente.');
    }
}
