<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Fournisseur;
use App\Models\Magasin;
use App\Models\User;
use PHPUnit\Framework\Constraint\Count;

class appController extends Controller
{
    //
    public function index()
    {
$TotalClient = Client:: all()-> count();
$TotalFournisseur = Fournisseur:: all()-> count();
$TotalFacture = Facture:: all()-> count();
$Totalmagasin = Magasin::all()-> count();
$Totalusers = User::all()-> count();

        return view ('page.accueil.home.dashboard',
        [
            'TotalClient' => $TotalClient,
            'TotalFournisseur' => $TotalFournisseur,
            'TotalFacture' => $TotalFacture,
           'Totalmagasin' => $Totalmagasin,
           'Totalusers'=> $Totalusers,




        ]

    );
    }
}
