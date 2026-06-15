<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Http\Requests\StoreEmpresaRequest;
use App\Http\Requests\UpdateEmpresaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    /**
     * Dashboard Maestro para German (SUPER_ADMIN)
     */
    public function dashboard()
    {
        return view('superadmin.index');
    }

    /**
     * Listado de todas las empresas.
     */
    public function index()
    {
        $empresas = Empresa::orderBy('created_at', 'desc')->get();
        return view('superadmin.empresas.index', compact('empresas'));
    }

    /**
     * Formulario para crear empresa.
     */
    public function create()
    {
        return view('superadmin.empresas.create');
    }

    /**
     * Guardar nueva empresa en la base de datos.
     */
    public function store(StoreEmpresaRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        Empresa::create($data);

        return redirect()->route('superadmin.empresas.index')
            ->with('success', 'Empresa registrada correctamente en el sistema.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Empresa $empresa)
    {
        return view('superadmin.empresas.edit', compact('empresa'));
    }

    /**
     * Actualizar los datos de la empresa.
     */
    public function update(UpdateEmpresaRequest $request, Empresa $empresa)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            // Borrar logo anterior si existe
            if ($empresa->logo_path) {
                Storage::disk('public')->delete($empresa->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa->update($data);

        return redirect()->route('superadmin.empresas.index')
            ->with('success', 'Información de la empresa actualizada.');
    }

    /**
     * Interruptor de encendido/apagado del servicio (activa).
     */
    public function toggleStatus(Empresa $empresa)
    {
        $empresa->update([
            'activa' => !$empresa->activa
        ]);

        $status = $empresa->activa ? 'activada (Acceso Permitido)' : 'suspendida (Acceso Denegado)';
        
        return back()->with('success', "La empresa {$empresa->nombre} ha sido {$status}.");
    }

    /**
     * Eliminación (Soft Delete si lo tienes configurado en el modelo).
     */
    public function destroy(Empresa $empresa)
    {
        // Validar si tiene usuarios antes de borrar (opcional)
        if ($empresa->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una empresa con usuarios activos.');
        }

        $empresa->delete();

        return redirect()->route('superadmin.empresas.index')
            ->with('success', 'La empresa ha sido eliminada del sistema.');
    }
}