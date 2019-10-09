<?php


namespace App\Models\Users;


class Worker extends Employee
{
    public function getRoles()
    {
        return [
            'worker'
        ];
    }
}
