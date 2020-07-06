<?php

namespace App\Data\Dto\Till;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */
class PaymentDto extends FlexibleDataTransferObject
{
    public ?string $id;

    public string $branch_id;

    public string $status;

    public ?string $payer_id;

    public ?string $payer_account_in_bill_currency_id;

    public ?string $payer_account_in_second_currency_id;

    public ?string $payer_type;

    public ?string $payee_id;

    public ?string $payee_account_in_bill_currency_id;

    public ?string $payee_account_in_second_currency_id;

    public ?string $payee_type;

    public ?string $payment_item_id;

    public ?string $related_payment_id;

    public float $billAmount;

    public float $paidAmountInBillCurrency;

    public float $paidAmountInSecondCurrency;

    public string $bill_currency_id;

    public ?string $second_paid_currency_id;

    public ?string $exchange_rate_id;

    public ?string $exchange_rate_to_tjs;

    public ?string $comment;

    public ?float $billAmountInTjs;

    public ?float $clientDebt;

    public ?int $placesLeft;

    public ?int $number;

    public ?float $discount;
}
