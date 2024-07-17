<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class hojaservicioRequest extends FormRequest
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
            'n_informe' => '',
            'servicio_a_realizar' => '',
            'razon_social' => '',
            'direccion' => '',
            'contacto' => '',
            'area_de_contacto' => '',
            'telefono_de_contacto' => '',
            'asesor_encargado' => '',
            'tecnico_encargado' => '',
            'hora_del_servicio' => 'nullable|date_format:H:i',
            'cantidad_de_instrumentos' => '',
            'datos_del_instrumento' => '',
            'trabajos_a_realizar' => '',
        ];
    }
}
