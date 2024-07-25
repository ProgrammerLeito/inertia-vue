<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TbproductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return Auth::check();
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
            'precio_min' => '',
            'precio_max' => '',
            'precio_list' => '',
            'stock' => 'required|integer|min:0',
            'codigo' => 'required|unique:tbproductos,codigo',
            'capacidades' => '',
            'especificaciones' => '',
            'foto' => 'nullable',
            'garantia' => '',
        ];
    }
    public function messages(): array
    {
        return [
            'codigo.unique' => 'El código debe ser único.', // Mensaje personalizado para la validación unique del campo 'codigo'
        ];
    }
}
