<?php


namespace App\Models\Users;


class Cashier extends Employee
{
    public function getRoles()
    {
        return [
            'cashier'
        ];
    }
}
