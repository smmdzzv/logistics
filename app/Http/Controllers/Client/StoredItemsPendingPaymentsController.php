<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 07.07.2020
 */

namespace App\Http\Controllers\Client;


use App\Data\Dto\Till\PaymentDto;
use App\Http\Controllers\BaseController;
use App\Models\Currency;
use App\Models\StoredItems\StoredItem;
use App\Models\Till\PaymentItem;
use App\Models\Users\Client;
use App\Models\Users\TrustedUser;
use App\Services\StoredItem\StoredItemsPaymentService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StoredItemsPendingPaymentsController extends BaseController
{
    private StoredItemsPaymentService $paymentService;

    public function __construct(StoredItemsPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Client $client)
    {
        $storedItems = StoredItem::with('info.billingInfo')
            ->whereIn('id', \request()->get('storedItems'))
//            ->unpaid()
            ->get();

        $ownerAccount = $client->accounts()->dollarAccount();

        /** @var Collection $storedItems */
        $paymentSum = $storedItems->pluck('info.billingInfo')->sum('pricePerItem')
            - $ownerAccount->balance;;

        if (request()->get('isDebtRequested', false)) {
            $trusted = TrustedUser::where('user_id', $client->id)->where('to', '>=', Carbon::now()->toDateString())
                ->first();

            if ($trusted) {
                $paymentSum -= $trusted->maxDebt;
            }
        }

        $paymentSum = round($paymentSum, 2);

        if ($paymentSum <= 0)
            return abort(400, 'На балансе клиента достаточно денег для оплаты выбранных товаров');

        $paymentDto = new PaymentDto([
            'branch_id' => auth()->user()->branch->id,
            'status' => 'pending',
            'payer_id' => $client->id,
            'payer_account_in_bill_currency_id' => $ownerAccount->id,
            'payer_account_in_second_currency_id' => null,
            'payer_type' => 'user',
            'payee_id' => auth()->user()->branch->id,
            'payee_account_in_bill_currency_id' => auth()->user()->branch->accounts()->dollarAccount()->id,
            'payee_account_in_second_currency_id' => null,
            'payee_type' => 'branch',
            'payment_item_id' => PaymentItem::firstOrCreate([
                'title' => 'Пополнение баланса',
                'description' => 'Пополнение долларового счета пользователя'
            ])->id,
            'billAmount' => $paymentSum,
            'paidAmountInBillCurrency' => $paymentSum,
            'paidAmountInSecondCurrency' => 0.0,
            'bill_currency_id' => Currency::where('isoName', 'USD')->first()->id,
            'second_paid_currency_id' => null,
            'exchange_rate_id' => null,
            'comment' => 'Пополнение баланса пользователя для оплаты заказа',
        ]);

        return $this->paymentService->store($paymentDto, $client, $ownerAccount, $storedItems, false);
    }
}
