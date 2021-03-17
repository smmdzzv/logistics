<?php

namespace App\Http\Controllers\Orders;

use App\Common\PasswordGenerator;
use App\Data\Dto\Order\OrderDto;
use App\Http\Controllers\BaseController;
use App\Http\Requests\OrderRequest;
use App\Models\Currency;
use App\Models\Customs\CustomsCode;
use App\Models\Order;
use App\Models\Role;
use App\Models\Tariff;
use App\Models\Till\Account;
use App\Services\Order\OrderService;
use App\User;
use Illuminate\Support\Facades\Hash;

class OrdersController extends BaseController
{
    private OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;

        $this->middleware('auth');

        $this->middleware('user.branch');

        $adminOnly = ['edit', 'update', 'destroy'];

        $this->middleware('roles.allow:admin')->only($adminOnly);
        $this->middleware('roles.allow:manager,storekeeper')->except($adminOnly);
    }

    public function index()
    {
        $branches = $this->getBranches();
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
            'storedItemInfos.tariff',
            'storedItemInfos.storedItems',
            'storedItemInfos.branch',
            'owner'
        ]);
        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $user = auth()->user();
        $tariffs = $this->getTariffs();
        $branches = $this->getBranches();
        return view('orders.create', compact('user', 'tariffs', 'branches'));
    }

    public function store(OrderRequest $request)
    {
        $orderDto = new OrderDto($this->getData($request));

        return $this->service->store($orderDto);
    }

    public function edit(Order $order)
    {
        if ($order->status == 'completed')
            return abort(403, 'Редактирование завершенных заказов запрещено');

        $order->load([
            'storedItemInfos',
            'storedItemInfos.customsCode',
            'storedItemInfos.owner',
            'storedItemInfos.billingInfo.tariffPricing',
            'storedItemInfos.item.codes',
            'storedItemInfos.tariff',
            'storedItemInfos.branch',
            'storedItemInfos.storedItems',
//            'orderRemovedItems.storedItemInfo.item',
            'owner'
        ]);

        $user = auth()->user();
        $tariffs = $this->getTariffs();
        $branches = $this->getBranches();
        return view('orders.edit', compact('order', 'user', 'tariffs', 'branches'));
    }

    public function update($order, OrderRequest $request)
    {
        $order = Order::find($order);

        $orderDto = new OrderDto($this->getData($request));

        return $this->service->update($order, $orderDto);
    }

    public function destroy(Order $order)
    {
        $this->service->destroy($order);
        return;
    }


    private function getData(OrderRequest $request): array
    {
        $data = collect($request->validated());
        $owner_id = $this->findOrCreateClient($request->get('clientCode'))->id;
        $data['owner_id'] = $owner_id;
        $data['storedItemInfos'] = collect($data['storedItemInfos'])
            ->map(function ($storedItemInfo) use ($request, $owner_id) {
                $storedItemInfo['owner_id'] = $owner_id;
                $storedItemInfo['branch_id'] = $request->get('branch_id');
                $storedItemInfo['customs_code_tax_id'] = CustomsCode::find($storedItemInfo['customs_code_id'])->tax->id;
                return $storedItemInfo;
            })->all();
        return $data->all();
    }

    private function getTariffs()
    {
        return auth()->user()->hasRole('admin') ? Tariff::all() : Tariff::where('branch_id', auth()->user()->branch->id)->get();
    }

    /**
     * TODO remove from here
     * @param String $code
     * @return User
     */
    private function findOrCreateClient(string $code): User
    {
        $client = User::where('code', $code)->first();
        if (!$client) {
            $client = User::create([
                'code' => $code,
                'name' => request()->get('clientName'),
                'phone' => request()->get('clientPhone'),
                'email' => request()->get('clientEmail'),
                'password' => Hash::make(PasswordGenerator::generate()),
                'branch_id' => auth()->user()->branch->id
            ]);

            $client->roles()->attach(Role::where('name', 'client')->first());

            $account = new Account();
            $account->currency_id = Currency::where('isoName', 'USD')->first()->id;
            $account->balance = 0;
            $account->description = 'Долларовый счет пользователя ' . $client->name;

            $client->accounts()->save($account);
        }

        return $client;
    }

}
