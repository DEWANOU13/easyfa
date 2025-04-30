<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class  FactureService{


    public function __construct() { }


// La demande de statut est utilisée pour obtenir un statut de l’API, de jeton et de factures en attente
public function statusService(){
    $response = Http::withoutVerifying()
    ->withHeaders($this->getHeaders())->get($this->getURL_API(). '/invoice');
    return $response;
}

// La demande de facture est utilisée pour envoyer les données sur la facture à l’e-MCF et d’obtenir les totaux calculés par l’e-MCF
public function postInvoiceRequestDtoService($data){
    $response = Http::withoutVerifying()
    ->withHeaders($this->getHeaders())->post($this->getURL_API().'/invoice', $data);
    return $response;
}

//Confirmer ou annuler la demande de facture
public function putFinalizeService($uid){
    $response = Http::withoutVerifying()
    ->withHeaders($this->getHeaders())->put($this->getURL_API().'/invoice'.'/'.$uid.'/confirm');
    return $response;
}


//Dteail de la demande de facture
public function getInvoiceDetailsDtoService($uid){
    $response = Http::withoutVerifying()
    ->withHeaders($this->getHeaders())->get($this->getURL_API().'/invoice'.'/'.$uid);
    return $response;
}


//return l entete pour l acces
public function getHeaders(){
    return ['Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IjEyMDE2NDI0MzgxMDB8VFMwMTAwMDE1NCIsInJvbGUiOiJUYXhwYXllciIsIm5iZiI6MTcxMzUyMzQzNywiZXhwIjoxODMwMjA3NjAwLCJpYXQiOjE3MTM1MjM0MzcsImlzcyI6ImltcG90cy5iaiIsImF1ZCI6ImltcG90cy5iaiJ9.qcyptn_NRVsELS8FBYPJx73V6T4iY9J2LK_nPLpA_N0'];
}


//return l adresse de l'api de emcf
public function getURL_API(){
    return "https://developper.impots.bj/sygmef-emcf/api";
}

}
