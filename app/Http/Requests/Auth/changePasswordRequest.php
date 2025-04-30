<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class changePasswordRequest extends FormRequest
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
            'old_password' => ['required', 'min:6', function ($attribute, $value, $fail) {
                // Récupérer le mot de passe actuel de l'utilisateur depuis la base de données
                $currentPassword = auth()->user()->password;

                // Vérifier si le mot de passe fourni correspond à celui de la base de données
                if (!Hash::check($value, $currentPassword)) {
                    $fail('Le mot de passe actuel ne correspond pas.');
                }
            }],
            'new_password' => 'required|min:6|different:old_password',
            'new_password_confirmation' => 'required|same:new_password',
        ];
    }
}
