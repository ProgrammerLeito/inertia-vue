<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHservicioRequest extends FormRequest
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
            'hmarca_id' => 'required',
            'modelo' => 'required|string',
            'serie' => 'required|string',
            'tecnico' => '',
            'division' => 'required|string',
            'capacidad' => 'required|string',
            'plataforma' => 'required|string',
            'fecha' => 'required|date',
            'requiere' => 'required|string',
            'diagnostico' => 'required|string',
            'trabajos' => 'required|string',
            'foto' => 'nullable',
            'foto2' => 'nullable',
            'foto3' => 'nullable',
            'servicio_id' => [
                'required',
                Rule::exists('servicios', 'id'),
            ],
        ];
    }
}
