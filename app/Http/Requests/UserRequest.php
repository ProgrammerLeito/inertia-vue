<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $id = $this->route('id'); // Obtiene el valor de 'id' de la ruta

        return [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id), // Ignora el usuario con este ID
            ],
            'password' => ['required_if:id,', 'confirmed', 'min:8'], // Asegúrate de definir las reglas correctas para la contraseña
            'sexo' => 'required',
            'celular' => 'required',
            'roles' => 'required|array',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección de correo electrónico válida.',
            'email.unique' => 'El correo electrónico ya ha sido registrado.',
            'password.required_if' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'sexo.required' => 'El campo sexo es obligatorio.',
            'celular.required' => 'El número de celular es obligatorio.',
            'roles.required' => 'El campo roles es obligatorio.',
            'roles.array' => 'Los roles deben ser un arreglo.',
        ];
    }
}
