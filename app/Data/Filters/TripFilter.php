<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 08.07.2020
 */

namespace App\Data\Filters;


class TripFilter extends Filter
{
    public function filter()
    {
        if (isset($this->filters['status']))
            $this->query->where('status', $this->filters['status']);

        return $this->query;
    }
}
