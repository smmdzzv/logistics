<?php

namespace App\Http\Controllers;

use App\Tariff;
use Illuminate\Http\Request;

class TariffPriceHistoriesController extends Controller
{
    public function lastByTariff(Tariff $tariff){
        return $tariff->lastPriceHistory();
    }
}
