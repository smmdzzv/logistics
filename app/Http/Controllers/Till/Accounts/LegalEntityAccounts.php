<?php


namespace App\Http\Controllers\Till\Accounts;


use App\Models\LegalEntities\LegalEntity;

class LegalEntityAccounts
{
    public function index(){
        $legalEntity = LegalEntity::with('accounts.currency')->first();
        return view('legal-entities.accounts.index', compact('legalEntity'));
    }
}
