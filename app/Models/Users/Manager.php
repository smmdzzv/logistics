<?php


namespace App\Models\Users;


class Manager extends Employee
{
    public function getRoles()
    {
        return [
            'manager'
        ];
    }
}
