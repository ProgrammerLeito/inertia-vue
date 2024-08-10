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
            'n_cotizacion' => '',
            'cliente_id' => 'required',
            'numeroDocumento' => 'nullable',
            'direccion' => 'nullable',
            'codesunat' => 'nullable',
            'estado' => 'nullable',
            'tecnico' => 'nullable',
            'fecha' => 'nullable|date',
            'moneda' => 'nullable',
            'tipoCambio' => 'nullable',
            'forma_pago' => 'nullable',
            'dias_entrega' => 'nullable',
            'validez_cot' => 'nullable',
            'subtotal' => 'nullable',
            'igv' => 'nullable',
            'total' => 'nullable',
        ];
    }
}
