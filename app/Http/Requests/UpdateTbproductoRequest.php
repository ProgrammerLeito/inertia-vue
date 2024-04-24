<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTbproductoRequest extends FormRequest
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
            'tbcategoria_id' => 'required',
            'tbsubcategoria_id' => 'required',
            'tbmarca_id' => 'required',
            'modelo' => 'required',
            'medida' => 'required',
            'moneda' => 'required',
            'precio' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'codigo' => 'required',
            'estado' => 'required',
            'capacidades' => 'required',
            'especificaciones' => 'required',
            'foto' => 'required',
        ];
    }
}
