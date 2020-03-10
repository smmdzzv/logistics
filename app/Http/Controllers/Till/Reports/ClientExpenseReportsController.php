<?php


namespace App\Http\Controllers\Till\Reports;


use App\Http\Controllers\Controller;

class ClientExpenseReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('roles.allow:admin,cashier,manager');
    }

    public function index()
    {
        return view('till.reports.index');
    }

    public function generateReport()
    {
        return 2;
    }
}
