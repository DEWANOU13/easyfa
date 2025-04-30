<?php

namespace App\Http\Requests\Affectation;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class affectationUserGroupe extends FormRequest
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
            'user_id' => [
                'required',
                Rule::unique('groupe_users')->where(function ($query) {
                    return $query->where('groupe_id', $this->input('groupe_id'));
                })
            ],
            'groupe_id' => ['required'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.unique' => 'Cet utilisateur avec ce groupe existe déjà.'
        ];
    }
}
