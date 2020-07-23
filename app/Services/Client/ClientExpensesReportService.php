<?php

namespace App\Services\Client;

use App\Data\Dto\Till\ClientExpenseDto;
use App\Models\Users\Client;
use Carbon\Carbon;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 23.07.2020
 */
class ClientExpensesReportService
{
    public function generate(Client $client, string $dateFrom, ?string $dateTo): ClientExpenseDto
    {
        $expensesDto = new ClientExpenseDto();

        $dateTo = $dateTo ? Carbon::createFromDate($dateTo)->addDay() : Carbon::now();

        $expensesDto->orderPayments = $client->orderPayments()->without([
            'branch',
            'preparedBy',
            'cashier',
            'payer',
            'payee',
            'payerAccountInBillCurrency',
            'payerAccountInSecondCurrency',
            'payeeAccountInBillCurrency',
            'payeeAccountInSecondCurrency',
            'paymentItem',
            'billCurrency',
            'secondPaidCurrency',
            'exchangeRate'
        ])
            ->where('updated_at', '>=', Carbon::now()->startOfYear())
            ->where('updated_at', '<', $dateTo)
            ->with(['clientItemsSelection' => function ($query) {
                $query->withCount('storedItems');
            }])
            ->get();

        $expensesDto->orders = $client->orders()
            ->where('created_at', '>=', Carbon::now()->startOfYear())
            ->where('created_at', '<', $dateTo)
            ->with('storedItemInfos')
            ->get();


        $expensesDto->orders->each(function ($order) {
            $order->storedItemsCount = $order->storedItemInfos->sum('count');
        });

        $expensesDto->prepareReport($dateFrom);
        return $expensesDto;
    }
}
