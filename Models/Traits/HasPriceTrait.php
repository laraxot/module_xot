<?php

declare(strict_types=1);

namespace Modules\Xot\Models\Traits;

// use Laravel\Scout\Searchable;

// ----- models------

// ---- services -----
// use Modules\Cms\Services\PanelService;

// ------ traits ---

/**
 * Modules\Food\Models\Traits\HasPriceTrait.
 *
 * @property string $currency
 * @property float  $price
 * @property string $price_complete
 * @property int    $qty
 */
trait HasPriceTrait
{
    /**
     * @return \Cknow\Money\Money
     */
    public function getPriceCurrencyAttribute($value)
    {
        return @money($this->price, $this->currency);
    }

    /**
     * @return \Cknow\Money\Money
     */
    public function getPriceCompleteCurrencyAttribute($value)
    {
        return @money($this->price_complete, $this->currency);
    }

    /**
     * @return \Cknow\Money\Money
     */
    public function getSubtotalCurrencyAttribute($value)
    {
        if ($this->qty > 0) {
            $value = $this->qty * $this->price;
        } else {
            $value = $this->price;
        }

        return @money($value, $this->currency);
    }

    /**
     * @param float $number
     *
     * @return \Cknow\Money\Money
     */
    public function getCurrency($number)
    {
        return @money($number, $this->currency);
    }
}
