<?php


namespace App\Http\Controllers\Users;

class ConcreteUsersRoutesController
{
    private $controller;

    /**
     * Determines allowed actions and parameters
     */
    private $methodParams = [
        'all'=> '',
        'filter' => 'userInfo',
        'index' => '',
        'orders' => 'client'
    ];

    public function __construct()
    {
        switch (request()->roleName) {
            case 'driver':
                $this->controller = new DriversController();
                break;
            case 'client':
                $this->controller = new ClientsController();
                break;
            default:
                abort(404, "Роль не найдена");
        }
    }

    public function __invoke($roleName, $action)
    {
        if (isset($this->methodParams[$action])
            && is_callable([$this->controller, $action])) {

            //Checks if the required params are provided
            foreach ((array)$this->methodParams[$action] as $param) {
                if ($param && !isset(request()->$param))
                    abort(302, 'Параметр ' . $param . ' не был передан');
            }

            return $this->controller->$action();
        }

        return $this->controller->concreteUser($action);
    }
}
