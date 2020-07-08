<?php
namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\BaseController;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 07.07.2020
 */
class ScannerController extends BaseController
{
    public function index()
    {
        return view('scanner.index');
    }
}
