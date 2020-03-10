<?php


namespace App\Http\Controllers\Till\Reports;


use App\Http\Controllers\Controller;
use App\Models\Users\Client;
use Carbon\Carbon;

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
        $client = Client::findOrFail(request()->get('client'));

        return $client->getExpensesReport(request()->get('dateFrom'), request()->get('dateTo'));
    }
}
