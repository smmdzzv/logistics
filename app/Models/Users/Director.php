<?php


namespace App\Models\Users;


class Director extends Employee
{
    public function getRoles()
    {
        return [
            'director'
        ];
    }
}
