<?php

namespace App\Services\Tariff;

use App\Data\Dto\Tariff\TariffPriceHistoryDto;
use App\Models\TariffPriceHistory;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 01.07.2020
 */
class TariffPriceHistoryService
{
    public function store(TariffPriceHistoryDto $dto): TariffPriceHistory
    {
        return TariffPriceHistory::create($dto->toArray());
    }

    public function update(TariffPriceHistory $history, TariffPriceHistoryDto $dto): TariffPriceHistory
    {
        $history->update($dto->toArray());
        return $history;
    }

    public function destroy(TariffPriceHistory $history)
    {
//        if ($history->billingInfos->count() > 0)
//            abort(422, 'Невозможно удалить расценки, использованные для расчета заказов');

        $history->delete();
        return;
    }
}
