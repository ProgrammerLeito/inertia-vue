<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'insumo' => ['required', 'string', 'max:250', Rule::unique(table: 'productos', column: 'insumo')->ignore(id: request('productos'), idColumn: 'id')],
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
    public function messages():array
    {
        return [
            'insumo.unique' => __('Este producto ya existe'),
            'marca.required' => __('La marca es obligatoria'),
            'modelo.required' => __('El modelo es obligatorio'),
            'cantidad.required' => __('La cantidad es obligatoria'),
            'unidad_medida.required' => __('La unidad de medida es obligatoria'),
            'fecha.required' => __('La fecha es obligatoria'),
            'comprador.required' => __('El comprador es obligatorio'),
            'comentario.required' => __('El comentario es obligatorio'),
            'imagen_producto.required' => __('La imagen es obligatoria'),
            'category_id.required' => __('La categoria es obligatoria'),
        ];
    }
}
