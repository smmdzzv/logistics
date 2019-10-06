<?php

namespace App\Http\Controllers;

use App\Models\BillingInfo;
use App\Models\Branch;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\StoredItem;
use App\Models\Tariff;
use App\Models\Users\Client;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Rorecek\Ulid;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (!auth()->user()->branch)
                abort(403, 'Для работы с заказами необходимо быть сотрудником филиала.');
            return $next($request);
        });

        $adminOnly = ['edit, update, destroy'];

        $this->middleware('role:employee,manager,director')->except($adminOnly);
        $this->middleware('role:admin')->only($adminOnly);
    }

    public function index()
    {
        $branches = Branch::all();
        return view('orders.index', compact('branches'));
    }

    public function show(Order $order)
    {
        $order->load(['storedItems.billingInfo', 'storedItems.item', 'owner']);
        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $user = auth()->user();
        $tariffs = Tariff::all();
        return view('orders.create', compact('user', 'tariffs'));
    }

    //TODO check client in StoreOrderRequest
    public function store(StoreOrderRequest $request)
    {
        //Create order
        $storedItems = $request->input('storedItems');
        $clientId = $request->input('clientId');

        $client = Client::findOrFail($clientId);
        $branch = Branch::findOrFail(auth()->user()->branch->id);

        $order = new Order();
        $order->owner()->associate($client);
        $order->branch()->associate($branch);
        $order->registeredBy()->associate(auth()->user());
        $order->push();

        //Create stored items
        $items = [];
        $ulidGenerator = new Ulid\Ulid();

        foreach ($storedItems as $itemData) {
            $items[] = [
                'id' => $ulidGenerator->generate(),
                'width' => $itemData['width'],
                'height' => $itemData['height'],
                'length' => $itemData['length'],
                'weight' => $itemData['weight'],
                'count' => $itemData['count'],
                'item_id' => $itemData['item']['id'],
                'branch_id' => $itemData['branch']['id'],
                'ownerId' => $client->id,
                'order_id' => $order->id,
                'created_at' => Carbon::now()
            ];
        }

        StoredItem::insert($items);

        //Calculate billing
        $billings = [];
        foreach ($items as $item) {
            $stored = new StoredItem();
            $stored->fill($item);
            $billing = $stored->getBillingInfo();
            $billing->id = $ulidGenerator->generate();
            array_push($billings, $billing->attributesToArray());
        }

        BillingInfo::insert($billings);

        //Update order statistics (totalWeight, totalPrice and etc.)
        $order->updateStat($billings);
        $order->save();

        return $order;
    }

    public function update(StoreOrderRequest $request)
    {

    }

    public function all()
    {
        return Order::with(['owner', 'registeredBy'])->paginate(10);
    }

    public function filteredByBranch(Branch $branch)
    {
        if (isset($branch)) {
            return $branch->orders()->with(['owner', 'registeredBy'])->paginate(10);
        } else abort(404, 'Филиал не найден');
    }

    public function filteredByUser(User $user)
    {
        if (isset($user)) {
            return $user->orders()->with(['owner', 'registeredBy'])->paginate(10);
        } else abort(404, 'Пользователь не найден');
    }
}
