<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePropietarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre'         => 'required|string|max:100',
            'apellidos'      => 'required|string|max:100',
            'dni'            => 'nullable|string|size:8',
            'telefono'       => 'nullable|string|max:15',
            'email'          => 'nullable|email|max:150',
            'direccion'      => 'nullable|string|max:255',
            'notas'          => 'nullable|string',
            // Dinámicos socio-conductor
            'es_conductor'           => 'nullable|boolean',
            'tipo_licencia'          => 'required_if:es_conductor,1|nullable|string|max:10',
            'licencia_vence'         => 'required_if:es_conductor,1|nullable|date',
            'conductor_estado'       => 'required_if:es_conductor,1|nullable|string|in:activo,suspendido,inactivo',
        ];
    }
}
