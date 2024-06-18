<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateServicioRequest extends FormRequest
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
            'cliente_id'=>'required',
            'direccion' =>'required',
            'dato_id' =>'required',
            'user_id' =>'',
            'n_guia' =>'required',
            'fecha' =>'required',
            'hora' =>'required',
            'tecnico' =>'',
            'estado' =>'',
            'pdf' =>'',
            'e_servicio' =>'',
            'descripcion'=>'required',
        ];
    }
}
