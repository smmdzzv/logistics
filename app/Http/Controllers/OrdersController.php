<?php

namespace App\Http\Controllers;

use App\Common\PasswordGenerator;
use App\Data\RequestWriters\Order\OrderRequestWriter;
use App\Data\RequestWriters\Order\UpdateOrderRequestWriter;
use App\Models\Branch;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Role;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Tariff;
use App\Models\Till\Account;
use App\Models\Users\Client;
use App\User;
use Illuminate\Support\Facades\Hash;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('user.branch');

        $adminOnly = ['edit', 'update', 'destroy'];

        $this->middleware('roles.allow:admin')->only($adminOnly);
        $this->middleware('roles.allow:manager')->except($adminOnly);
    }

    public function index()
    {
        $branches = Branch::all();
        return view('orders.index', compact('branches'));
    }

    public function show(Order $order)
    {
        $order->load([
            'storedItemInfos',
            'storedItemInfos.customsCode',
            'storedItemInfos.owner',
            'storedItemInfos.billingInfo',
            'storedItemInfos.item',
            'storedItemInfos.storedItems',
            'orderRemovedItems.storedItemInfo.item',
            'orderRemovedItems.storedItemInfo.deletedBy',
            'owner'
        ]);
        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $user = auth()->user();
        $tariffs = Tariff::all();
        return view('orders.create', compact('user', 'tariffs'));
    }

    public function store(StoreOrderRequest $request)
    {
        $storedItemInfos = $this->getStoredItemInfos();
        $customPrices = $this->getCustomPricesArray();

        $orderWriter = new OrderRequestWriter(
            $this->findOrCreateClient($request->input('clientCode')),
            Branch::findOrFail(auth()->user()->branch->id),
            auth()->user(),
            $storedItemInfos,
            $customPrices
        );

        return $orderWriter->write();
    }

    public function edit(Order $order){
        if($order->status == 'completed')
            return abort(403, 'Доступ запрещен');
        $order->load([
            'storedItemInfos',
            'storedItemInfos.customsCode',
            'storedItemInfos.owner',
            'storedItemInfos.tariff',
            'storedItemInfos.billingInfo.tariffPricing',
            'storedItemInfos.item.tariff',
            'storedItemInfos.item.codes',
            'storedItemInfos.storedItems',
            'owner'
        ]);

        $user = auth()->user();
        $tariffs = Tariff::all();
//        $shops = Shop::all();

        return view('orders.edit', compact('order', 'user', 'tariffs'));
    }

    public function update(Order $order, StoreOrderRequest $request)
    {
        $storedItemInfos = $this->getStoredItemInfos();
        $customPrices = $this->getCustomPricesArray();

        $orderWriter = new UpdateOrderRequestWriter(
            $this->findOrCreateClient($request->input('clientCode')),
            Branch::findOrFail(auth()->user()->branch->id),
            auth()->user(),
            $storedItemInfos,
            $customPrices,
            $order
        );

        return $orderWriter->write();
    }

    /**
     * @param String $code
     * @return User
     */
    private function findOrCreateClient(String $code){
        $client = User::where('code',$code)->first();
        if(!$client){
            $client = User::create([
                'code' => $code,
                'password' =>  Hash::make(PasswordGenerator::generate()),
                'branch_id' => auth()->user()->branch->id
            ]);

            $client->roles()->attach(Role::where('name', 'client')->first());

            $account = new Account();
            $account->currencyId = Currency::where('isoName', 'USD')->first()->id;
            $account->balance = 0;
            $account->description = 'Долларовый счет пользователя ' . $client->name;

            $client->accounts()->save($account);
        }



        return $client;
    }

    private function getStoredItemInfos(){
        $storedItemInfos = array();
        foreach (request()->input('storedItemInfos') as $itemData) {
            $storedItemInfos[] = new StoredItemInfo([
                'id' => $itemData['id'],
                'width' => $itemData['width'],
                'height' => $itemData['height'],
                'length' => $itemData['length'],
                'weight' => $itemData['weight'],
                'count' => $itemData['count'],
                'shop' => $itemData['shop'],
                'item_id' => $itemData['item']['id'],
//                'placeCount' => $itemData['placeCount'],
                'ownerId' => request()->input('clientId'),
                'branch_id' => auth()->user()->branch->id,
                'tariff_id' => $itemData['tariff']['id'],
                'customs_code_id' => $itemData['customsCode']['id']
            ]);

//            $data->customPrices[$itemIndex] = isset($itemData['customPrice']) ? $itemData['customPrice'] : null;
        }

        return $storedItemInfos;
    }

    private function getCustomPricesArray(){
        $customPrices = array();
        $index = 0;

        foreach (request()->input('storedItemInfos') as $itemData) {
            $customPrices[$index] = isset($itemData['customPrice']) ? $itemData['customPrice'] : null;
            $index++;
        }

        return $customPrices;
    }

    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return Order::with(['owner', 'registeredBy'])->latest()->paginate($paginate);
    }

    public function activeOrders(Client $client)
    {
        return $client->activeOrders;
    }

    public function filteredByBranch(Branch $branch)
    {
        $paginate = request()->paginate ?? 10;
        if (isset($branch)) {
            return $branch->orders()->with(['owner', 'registeredBy'])->paginate($paginate);
        } else abort(404, 'Филиал не найден');
    }

    public function filteredByUser(User $user)
    {
        $paginate = request()->paginate ?? 10;
        if (isset($user)) {
            return $user->orders()->with(['owner', 'registeredBy'])->paginate($paginate);
        } else abort(404, 'Пользователь не найден');
    }
}
