<?php

namespace App\Http\Requests\Facture;

use Illuminate\Foundation\Http\FormRequest;

class listeLigneFactureRequest extends FormRequest
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
            'debut2' => ['nullable', 'date'],
            'fin2' => ['nullable', 'date', 'after:debut2'],
        ];
    }
}
