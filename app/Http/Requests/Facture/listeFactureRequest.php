<?php

namespace App\Http\Requests\Facture;

use Illuminate\Foundation\Http\FormRequest;

class listeFactureRequest extends FormRequest
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
            'debut1' => ['nullable', 'date'],
            'fin1' => ['nullable', 'date', 'after:debut1'],
        ];
    }
}
