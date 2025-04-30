<?php

namespace App\Livewire\Utilisateur;

use App\Models\User;
use Livewire\Component;

class NouveauUtilisateur extends Component
{
    public $users, $email, $name, $password, $actif, $UpEmail, $UpName, $UpPassword, $UpActif, $UpIdUtilisateur;
    public function createUtilisateur(){
        $this->validate([
            'email' => 'required',
            'name' => 'required',
            'password' => 'required',
            'actif' => 'required',
        ]);

        $this->dispatch('actionModalCreateUtilisateur');
    }
    public function editUtilisateur($id){

        $utilisuateur = User::findOrfail($id);

        $this->UpIdUtilisateur = $utilisuateur->id;
        $this->UpEmail = $utilisuateur->email;
        $this->UpName = $utilisuateur->name;
        $this->UpActif = $utilisuateur->actif;

    }
    public function validateEditUtilisateur(){
        $this->validate([
            'UpEmail' => 'required',
            'UpName' => 'required',
            'UpActif' => 'required',
        ]);

        $this->dispatch('actionModalUpdateMagasin');
    }

    public function render()
    {
        return view('livewire.utilisateur.nouveau-utilisateur');
    }
}
