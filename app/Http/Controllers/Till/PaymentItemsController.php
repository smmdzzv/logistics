<?php

namespace App\Http\Controllers\Till;

use App\Models\Till\PaymentItem;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PaymentItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
    }

    public function rules()
    {
        return [
            'description' => 'nullable|string|max:500',
            'type' => [
                'required',
                Rule::in(['in', 'out'])
            ],
            'title' => [
                'required',
                Rule::unique('payment_items')->ignore(request()->get('id'))
            ]
        ];
    }

    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return PaymentItem::paginate($paginate);
    }

    public function filteredByType($type){
        return PaymentItem::where('type', $type)->get();
    }

    public function index()
    {
        return view('till.items.index');
    }

    function create()
    {
        return view('till.items.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        PaymentItem::create($data);
        return redirect(route('payment-items.index'));
    }

    public function edit(PaymentItem $paymentItem)
    {
        return view('till.items.edit', compact('paymentItem'));
    }

    public function update(Request $request, PaymentItem $paymentItem)
    {
        $data = $request->validate($this->rules());
        $paymentItem->fill($data);
        $paymentItem->save();
        return redirect(route('payment-items.index'));
    }

    public function destroy(PaymentItem $paymentItem)
    {
        $paymentItem->delete();
       return;
    }
}
