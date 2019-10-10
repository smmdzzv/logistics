<?php


namespace App\Models\Users;


class Admin extends Employee
{
    public function getRoles()
    {
        return [
            'admin'
        ];
    }
}
