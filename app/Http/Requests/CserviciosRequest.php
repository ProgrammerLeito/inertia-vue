<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CserviciosRequest extends FormRequest
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
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'capacidad' => 'required|string',
            'serie' => 'nullable|string',
            'division' => 'required|string',
            'plataforma' => 'required|string',
            'requiere' => 'required|string',
            'fecha' => 'required|date',
            'descripcion' => '',
            'tselecionado' => '',
            'cselecionado' => '',
            'oselecionado' => '',
            'dselecionado' => '',
            'rselecionado' => '',
            'codesunat' => '',
            'precio' => 'required|numeric',
            'moneda' => 'required|string',
            'fpago' => 'required|string',
            'garantia' => 'required|string',
            'dentrega' => 'required|integer',
            'foto' => '',
            'foto1' => '',
            'total' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'igv' => 'required|numeric',
            'plantilla_id' => 'required|integer',
            'n_informe' => 'required',
            'razonSocial' => 'required',
        ];
    }
}
