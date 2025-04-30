<?php

namespace App\Http\Requests\UploadTemplate;

use Illuminate\Foundation\Http\FormRequest;

class entetePiedPageA5Request extends FormRequest
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
            'entete' => ['required', 'file', 'mimes:jpeg,png,pdf,jpg', 'max:4096'],
            'pied' => ['required', 'file', 'mimes:jpeg,png,pdf,jpg', 'max:4096'],
        ];
    }
}
