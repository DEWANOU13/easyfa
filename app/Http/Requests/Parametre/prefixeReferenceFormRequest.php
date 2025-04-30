<?php

namespace App\Http\Requests\Parametre;

use Illuminate\Foundation\Http\FormRequest;

class prefixeReferenceFormRequest extends FormRequest
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
            'entre_produit' => ['required'],
            'sortie_produit' => ['required'],
            'transfert_produit' => ['required'],
            'inventaire' => ['required'],
            'proforma' => ['required'],
            'facture' => ['required'],
            'avoir' => ['required'],
            'reglement' => ['required'],
            'libelle_reserves' => ['required'],
            'approvisionnement' => ['required'],
            'reception_approvisionnement' => ['required'],
            'acheminement' => ['required'],
            'reception_acheminement' => ['required'],
            'mode_impression' => ['required'],
            'prise_en_compte_reglement' => ['required'],
            'surplus_reglement' => ['required'],
            'type_normalisation' => ['required'],
            'emballage' => ['nullable'],
            'pre_cocher_aib' => ['nullable'],
            'caisse' => ['nullable'],
        ];
    }
}
