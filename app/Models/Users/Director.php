<?php


namespace App\Models\Users;


use App\Models\Branch;

class Director extends Employee
{
    public function getRoles()
    {
        return [
            'director'
        ];
    }

    public function managedBranch()
    {
        return $this->hasOne(Branch::class, 'director');
    }
}
