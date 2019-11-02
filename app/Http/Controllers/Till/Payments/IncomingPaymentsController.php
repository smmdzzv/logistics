<?php

namespace App\Http\Controllers\Till\Payments;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Till\PaymentRequest;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\Payment;
use Illuminate\Database\Eloquent\Builder;
use App\Data\RequestWriters\Payments\IncomingPaymentRequestWriter;
use stdClass;

class IncomingPaymentsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:cashier, manager');
    }

    public function all()
    {
        return Payment::with('accountTo', 'payer', 'currency', 'paymentItem')
            ->whereHas('paymentItem', function (Builder $query) {
                $query->where('type', 'in');
            })->latest()->paginate($this->pagination());
    }

    public function filteredByBranch(Branch $branch)
    {
        return $branch->payments()
            ->with('accountTo', 'payer', 'currency', 'paymentItem')
            ->paginate($this->pagination());
    }

    public function create()
    {
        $accountsTo = LegalEntity::first()->accounts()->with('currency')->get();
        $currencies = Currency::all();
        return view('till.payments.incoming.create', compact('accountsTo', 'currencies'));
    }

    //All incoming payments are transferred to Duob main account immediately.
    //Even if they were sent to user account, which also fills up
    public function store(PaymentRequest $request)
    {
        $data = new stdClass();
        $data->payment = $request->all();
        $data->branchId = auth()->user()->branch->id;
        $data->cashierId = auth()->id();

        $paymentWriter = new IncomingPaymentRequestWriter($data);
        $paymentWriter->write();

        return redirect()->route('payments.index');
    }
}
