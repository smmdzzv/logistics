<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Controllers\Customs;

use App\Data\Dto\Customs\CustomsCodeDto;
use App\Data\Dto\Customs\CustomsCodeTaxDto;
use App\Http\Controllers\Controller;
use App\Models\Customs\CustomsCode;
use App\Services\Customs\CustomsCodeAndTaxService;
use Illuminate\Http\Request;

class CustomsCodeController extends Controller
{
    private CustomsCodeAndTaxService $service;

    public function __construct(CustomsCodeAndTaxService $service)
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
        $this->service = $service;
    }

    public function index()
    {
        $codes = CustomsCode::with('tax')->get();
        return view('customs.index', compact('codes'));
    }

    public function create()
    {
        return view('customs.create');
    }

    public function store(Request $request)
    {
        $data = $this->getValidatedData($request);
        $this->service->store(new CustomsCodeDto($data), new CustomsCodeTaxDto($data));
        return redirect(route('customs-code.index'));
    }

    public function edit(CustomsCode $code)
    {
        return view('customs.edit', compact('code'));
    }

    public function update(CustomsCode $code, Request $request)
    {
        $data = $this->getValidatedData($request);
        $this->service->update($code, new CustomsCodeDto($data), new CustomsCodeTaxDto($data));
        return redirect(route('customs-code.index'));
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'shortName' => 'nullable|string|max:255',
            'internationalName' => 'nullable|string|max:255',
            'code' => 'required|string|max:15',
            'price' => 'required|numeric',
            'interestRate' => 'required|numeric',
            'totalRate' => 'required|numeric',
            'vat' => 'required|numeric',
            'isCalculatedByPiece' => 'required|in:0,1'
        ];
    }

    private function getValidatedData($request): array
    {
        $data = $request->validate($this->rules());

        $data['price'] = floatval($data['price']);
        $data['interestRate'] = floatval($data['interestRate']);
        $data['totalRate'] = floatval($data['totalRate']);
        $data['vat'] = floatval($data['vat']);
        $data['isCalculatedByPiece'] = boolval($data['isCalculatedByPiece']);

        return $data;
    }
}
