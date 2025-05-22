<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends ApiFormRequest
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
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'

        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El correo es obligatorio.',
            'email.string' => 'El correo debe ser una cadena de texto.',
            'email.email' => 'El correo debe ser un correo válido.',
            'email.max' => 'El correo no puede superar los 255 caracteres.',
            'email.unique' => 'El correo ya está en uso.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'ELa contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
