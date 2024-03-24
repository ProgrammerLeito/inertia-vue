<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
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
            'insumo' => ['required', 'string', 'max:250'],
            'marca' => ['required', 'string', 'max:250'],
            'modelo' => ['required', 'string', 'max:250'],
            'cantidad' => ['required', 'numeric'],
            'unidad_medida' => ['required', 'string', 'max:250'],
            'fecha' => ['required'],
            'comprador' => ['required', 'string', 'max:250'],
            'comentario' => ['required', 'string', 'max:250'],
            'stock' => ['required', 'numeric'],
            'precio' => ['required', 'string', 'max:250'],
            'ultima_entrada' => ['required', 'numeric'],
            'imagen_producto' => ['required'],
            'category_id' => ['required'],
        ];
    }
}
