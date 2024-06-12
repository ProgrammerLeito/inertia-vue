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
            'precio_min' => 'required|numeric|min:0',
            'precio_max' => 'required|numeric|min:0',
            'precio_list' => 'nullable|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'codigo' => 'required',
            'capacidades' => '',
            'especificaciones' => '',
            'foto' => 'nullable',
        ];
    }
}
