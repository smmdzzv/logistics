<?php

namespace App\Http\Controllers\LostAndFound;

use App\Models\Currency;
use App\Models\LostAndFound\LostStoredItem;
use App\Models\StoredItems\StoredItem;
use App\Models\Till\Payment;
use App\Http\Controllers\Controller;
use App\Models\Till\PaymentItem;
use Illuminate\Database\Eloquent\Builder;
/**
 * @deprecated lost items moved to App\Http\Controllers\StoredItems\LostStoredItemsController.php
*/
class LostItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
    }

    private function rules()
    {
        return [
            'storedItemCode' => 'required|exists:stored_items,code',
            'discount' => 'required|numeric',
            'placeCount' => 'nullable|numeric',
        ];
    }

    public function index()
    {
        $lostItems = LostStoredItem::with('storedItem')->latest()->paginate(10);
        return view('lost-and-found.lost-items.index', compact('lostItems'));
    }

    public function create()
    {
        return view('lost-and-found.lost-items.create');
    }

    public function store()
    {
        $data = request()->validate($this->rules());

        $storedItem = StoredItem::where('code', request('storedItemCode'))->firstOrFail();

        $item = LostStoredItem::create(array_merge($data, ['stored_item_id' => $storedItem->id]));

        $accountTo = $storedItem->info->owner->accounts()
            ->whereHas('currency', function (Builder $query) {
                $query->where('isoName', 'USD');
            })
            ->firstOrFail();

        Payment::create([
            'branchId' => auth()->user()->branch->id,
            'cashierId' => auth()->id(),
            'currencyId' => Currency::where('isoName', 'USD')->firstOrFail()->id,
            'paymentItemId' => PaymentItem::where('title', 'Прием наличных')->firstOrFail()->id,
            'accountToId' => $accountTo->id,
            'amount' => round($item->discount, 2),
            'status' => 'completed',
            'comment' => 'Скидка за утерянный товар ' . $storedItem->code
        ]);

        $accountTo->balance += round($item->discount,2);
        $accountTo->save();

        return redirect(route('lost-items.index'));
    }
}
