<?php

namespace App\Http\Requests\Maintenace;

use Illuminate\Foundation\Http\FormRequest;

class maitenaceRequest extends FormRequest
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
            'jour_maintenance' => ['integer','min:0'],
            'heure_maintenance' => ['integer','min:0'],
            'minute_maintenance' => ['integer','min:0']
        ];
    }
}
