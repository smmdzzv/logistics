<?php


namespace App\Http\Controllers\Till\Reports;


use App\Http\Controllers\Controller;
use App\Models\Users\Client;
use App\Services\Client\ClientExpensesReportService;
use Carbon\Carbon;

class ClientExpenseReportsController extends Controller
{
    private ClientExpensesReportService $service;

    public function __construct(ClientExpensesReportService $service)
    {
        $this->middleware('roles.allow:admin,cashier,manager');

        $this->service = $service;
    }

    public function index()
    {
        return view('till.reports.index');
    }

    public function show()
    {
        $client = Client::findOrFail(request()->get('client'));

        return $this->service->generate($client, request()->get('dateFrom'), request()->get('dateTo'))->toJson();
    }
}
