<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientesRequest extends FormRequest
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
            'numeroDocumento' => 'required|string',
            'razonSocial' => 'required|string',
            'direccion' => 'required|string',
            'distrito' => 'required|string',
            'provincia' => 'required|string',
            'departamento' => 'required|string',
            'estado' => 'required|string',
            'cli_direccion2' => 'required',
            'cli_observacion' => 'required|string',
            'tbprovincia_id' => 'required',
            'ctg' => 'nullable',
            'asesor' => 'nullable',
        ];
    }
}
