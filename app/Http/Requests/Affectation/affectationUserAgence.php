<?php

namespace App\Http\Requests\Affectation;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class affectationUserAgence extends FormRequest
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
                Rule::unique('agence_users')->where(function ($query) {
                    return $query->where('agence_id', $this->input('agence_id'));
                })
            ],
            'agence_id' => ['required'],
        ];
    }
}
