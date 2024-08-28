<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
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
            'numeroDocumento' => ['required', 'string', 'max:250', Rule::unique(table: 'clientes', column: 'numeroDocumento')->ignore(id: request('clientes'), idColumn: 'id')],
            'razonSocial' => 'required|string',
            'direccion' => 'required|string',
            'distrito' => 'required|string',
            'provincia' => 'required|string',
            'departamento' => 'required|string',
            'estado' => 'required|string',
            'ctg' => '',
            'asesor' => '',
            'fechafactura' => '',
            'codigofactura' => '',
            'cli_direccion2' => 'required',
            'cli_observacion' => 'required|string',
            'tbprovincia_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'numeroDocumento.unique' => 'El número de documento ya ha sido registrado.',
            // Otros mensajes de error personalizados...
        ];
    }
}
