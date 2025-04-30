<?php

namespace App\Http\Requests\FiltreClient;

use Illuminate\Foundation\Http\FormRequest;

class FactureClientRequest extends FormRequest
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
            'debut_periode' => ['date', 'nullable'],
            'fin_periode' => ['date', 'nullable'],
            'client' => ['nullable'],
        ];
    }
}
