<?php

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Controllers;

use App\Data\Dto\Item\ItemDto;
use App\Models\Customs\CustomsCode;
use App\Models\StoredItems\Item;
use App\Services\Item\ItemService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemsController extends Controller
{
    private ItemService $service;

    public function __construct(ItemService $service)
    {
        $this->middleware('auth');

        $except = ['all', 'allEager'];

        $this->middleware('roles.allow:admin')->except($except);
        $this->middleware('roles.deny:client')->only($except);

        $this->service = $service;
    }

    public function all()
    {
//        $paginate = request()->paginate ?? 50;
        return Item::paginate(100);
    }

    public function allEager()
    {
        return Item::with('codes')->get();
    }

    public function index()
    {
        return view('items.index');
    }

    public function create()
    {
//        $branches = Branch::all();
        $customsCodes = CustomsCode::all();
        return view('items.create', compact('customsCodes'));
    }

    public function store(Request $request)
    {
        $this->service->store($this->getValidatedDto($request));
        return redirect()->route('items.index');
    }

    public function edit(Item $item)
    {
        $item->load('codes');
        $customsCodes = CustomsCode::all();
        return view('items.edit', compact('item', 'customsCodes'));
    }

    public function update(Item $item, Request $request)
    {
        $this->service->update($item, $this->getValidatedDto($request));
        return redirect()->route('items.index');
    }

    public function destroy(Item $item)
    {
        $this->service->destroy($item);
        return;
    }

    private function getValidatedDto(Request $request): ItemDto
    {
        $data = $request->validate($this->rules());

        $data['onlyCustomPrice'] = boolval($data['onlyCustomPrice']);
        $data['applyDiscount'] = boolval($data['applyDiscount']);
        $data['onlyAgreedPrice'] = boolval($data['onlyAgreedPrice']);
        $data['calculateByNormAndWeight'] = boolval($data['calculateByNormAndWeight']);

        return new ItemDto($data);
    }

    private function rules(): array
    {
        return [
            'unit' => 'nullable|string|max:10',
            'onlyCustomPrice' => 'required|in:0,1',
            'applyDiscount' => 'required|in:0,1',
            'onlyAgreedPrice' => 'required|in:0,1',
            'calculateByNormAndWeight' => 'required|in:0,1',
            'customsCodes' => 'required|array',
//            'branch_id' => 'required|exists:branches,id',
            'name' => [
                'required',
                'string',
                'max:255',
//                Rule::unique('items')->ignore(request()->get('id'))
            ]
        ];
    }
}
