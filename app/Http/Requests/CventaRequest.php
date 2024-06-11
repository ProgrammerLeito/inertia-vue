<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CventaRequest extends FormRequest
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
            'cliente_id' => 'required',
            'tenor_id' => 'required',
            'fecha' => 'required|date',
            'moneda' => 'required|string',
            'garantia' => 'required|string',
            'forma_pago' => 'required|string',
            'dias_entrega' => 'required|integer',
            'subtotal' => 'required|numeric',
            'igv' => 'required|numeric',
            'total' => 'required|numeric',

            'modelo' => 'nullable|string',
            'foto' => 'nullable|string',
            'especificaciones' => 'nullable|string',
            'marca' => 'nullable|string',
            'capacidades' => 'nullable|string',
            'precio' => 'nullable|string',
            'precio_max' => 'nullable|string',
            'cantidad' => 'nullable|string',
            'importe' => 'nullable|string',
            'n_cotizacion' => '',
            'codesunat' => '',
            'estado' => '',
            'tecnico' => '',
        ];
    }
}
