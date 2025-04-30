<?php

namespace App\Http\Requests\Groupe;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class groupeFormRequest extends FormRequest
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
        // Récupérer l'URL précédente
        $previousUrl = url()->previous();

        // Extraire l'ID de l'URL précédente
        $segments = explode('/', url()->current());
        $id = end($segments);

        return [
            'nom_groupe' => ['required', !preg_match('/\/(\d+)$/', $previousUrl, $matches) ? Rule::unique('groupes', 'nom_groupe')->ignore($id) : Rule::unique('groupes', 'nom_groupe')],
            'description' => ['nullable']
        ];
    }
}
