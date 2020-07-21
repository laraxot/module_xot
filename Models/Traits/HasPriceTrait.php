<?php

namespace Modules\Xot\Models\Traits;

//use Laravel\Scout\Searchable;

//----- models------

//---- services -----
//use Modules\Xot\Services\PanelService as Panel;

//------ traits ---

trait HasPriceTrait {
    public function getPriceCurrencyAttribute($value) {
        return @money($this->price * 100, $this->currency);
    }

    public function getPriceCompleteCurrencyAttribute($value) {
        return @money($this->price_complete * 100, $this->currency);
    }

    public function getSubtotalCurrencyAttribute($value) {
        if ($this->qty > 0) {
            $parz = $this->qty * $this->price;
        } else {
            $parz = $this->price;
        }

        return @money($parz * 100, $this->currency);
    }
}
