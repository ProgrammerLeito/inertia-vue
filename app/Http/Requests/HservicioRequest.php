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
            'codigo' => 'nullable',
            'material' => 'nullable',
            'modelo' => 'nullable',
            'serie' => 'nullable',
            'div' => 'nullable',
            'capacidad' => 'nullable',
            'cliente_id' => 'nullable',
            'plataforma' => 'nullable',
            'fecha' => 'nullable|date',
            'requiere' => 'nullable',
            'diagnostico' => 'nullable',
            'trabajos' => 'nullable',
            'tecnico' => 'nullable',
            'foto' => 'nullable',
            'foto2' => 'nullable',
            'foto3' => 'nullable',
        ];
    }
}
