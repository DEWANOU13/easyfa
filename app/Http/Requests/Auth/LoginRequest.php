<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        // $user = User::where('email', $_POST['email'])->first();
        // $userId = $user->id;

        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'min:6'],
            // 'site' => [($userId == 1) ? '' : 'required']
        ];
    }
}
