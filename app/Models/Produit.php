<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produit extends Model
{
    use HasFactory;
    protected $fillable = ['Type', 'Reference', 'Designation', 'Id_Categorie', 'Id_Unite_Comptage', 'Emballage_id', 'type_emballage','Statut','Enregistrer_par'];

    public function categorieProduit(): BelongsTo{
        return $this->belongsTo(CategorieProduit::class, 'Id_Categorie');
    }

    public function emballage(): BelongsTo{
        return $this->belongsTo(Emballage::class, 'Emballage_id');
    }

    public function typeEmballage(): BelongsTo{
        return $this->belongsTo(CategorieEmballage::class, 'type_emballage');
    }

    public function uniteDeComptage()
    {
        return $this->belongsTo(UniteComptage::class, 'Id_Unite_Comptage');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
