<?php


namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

class ConcreteUsersRoutesController extends Controller
{
    private $controller;

    /**
     * Determines allowed actions and parameters
     */
    private $methodParams = [
        'all' => '',
        'filter' => 'userInfo',
        'findByCode' => 'code',
        'index' => '',
        'orders' => 'client'
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:employee');

        switch (request()->roleName) {
            case 'client':
                $this->controller = new ClientsController();
                break;
            case 'employee':
                $this->controller = new EmployeesController();
                break;
            case 'worker':
                $this->controller = new WorkersController();
                break;
            case 'driver':
                $this->controller = new DriversController();
                break;
            case 'cashier':
                $this->controller = new CashiersController();
                break;
            case 'manager':
                $this->controller = new ManagersController();
                break;
            case 'director':
                $this->controller = new DirectorsController();
                break;
            case 'admin':
                $this->controller = new AdminsController();
                break;
            case 'storekeeper':
                $this->controller = new StorekeepersController();
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

        return $this->controller->user($action);
    }
}
