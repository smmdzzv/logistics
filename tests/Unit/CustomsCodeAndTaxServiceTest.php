<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */

namespace Tests\Unit;


use App\Data\Dto\Customs\CustomsCodeDto;
use App\Data\Dto\Customs\CustomsCodeTaxDto;
use App\Models\Customs\CustomsCode;
use App\Models\Customs\CustomsCodeTax;
use App\Services\Customs\CustomsCodeAndTaxService;
use App\Services\Customs\CustomsCodeService;
use App\Services\Customs\CustomsCodeTaxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomsCodeAndTaxServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_code_and_tax()
    {
        $code = $this->create_code_and_tax();

        $this->assertCount(1, CustomsCode::all());
        $this->assertCount(1, CustomsCode::first()->taxes);
        $this->assertCount(1, CustomsCodeTax::all());
        $this->assertEquals($code->id, CustomsCodeTax::first()->code->id);
    }

    private function create_code_and_tax(): CustomsCode
    {
        $service = new CustomsCodeAndTaxService(new CustomsCodeService(), new CustomsCodeTaxService());

        $codeDto = new CustomsCodeDto([
            'name' => 'testCode',
            'code' => '123456789',
        ]);

        $taxDto = new CustomsCodeTaxDto([
            'price' => 1002.5,
            'interestRate' => 5.5,
            'vat' => 18.0,
            'totalRate' => 5.0,
            'isCalculatedByPiece' => true
        ]);

        return $service->store(
            $codeDto,
            $taxDto
        );
    }

    public function test_update_code_and_tax()
    {
        $service = new CustomsCodeAndTaxService(new CustomsCodeService(), new CustomsCodeTaxService());

        $code = $this->create_code_and_tax();

        $codeDto = new CustomsCodeDto([
            'name' => 'updatedTestCode',
            'code' => '058585',
        ]);

        $taxDto = new CustomsCodeTaxDto([
            'price' => 10202.5,
            'interestRate' => 2.5,
            'vat' => 19.0,
            'totalRate' => 10.0,
            'isCalculatedByPiece' => false
        ]);

        $updatedCode = $service->update($code, $codeDto, $taxDto);

        $this->assertCount(1, CustomsCode::all());
        $this->assertCount(2, CustomsCode::first()->taxes);
        $this->assertCount(2, CustomsCodeTax::all());
        $this->assertEquals($updatedCode->id, CustomsCodeTax::first()->code->id);
    }
}
