<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class HservicioRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'n_servicio' => 'nullable',
            'hmarca_id' => 'nullable',
            'instrumento' => 'nullable',
            'rango' => 'nullable',
            'medida_bastago' => 'nullable',
            'medida_bastago2' => 'nullable',
            'codigo' => 'nullable',
            'material' => 'nullable',
            'modelo' => 'nullable',
            'serie' => 'nullable',
            'division' => 'nullable',
            'medida_division' => 'nullable',
            'capacidad' => 'nullable',
            'medida_capacidad' => 'nullable',
            'cliente_id' => 'nullable',
            'plataforma' => 'nullable',
            'medida_plataforma' => 'nullable',
            'fecha' => 'nullable|date',
            'requiere' => 'nullable',
            'diagnostico' => 'nullable',
            'trabajos' => 'nullable',
            'tecnico' => 'nullable',
            'encargado' => 'nullable',
            'foto' => 'nullable',
            'foto2' => 'nullable',
            'foto3' => 'nullable',
        ];
    }
}
