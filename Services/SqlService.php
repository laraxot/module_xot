<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SqlService.
 */
class SqlService
{
    public static function getCoalesceDateRange(
        Model $model,
        int $date_min = null,
        int $date_max = null,
        string $from_field = null,
        string $to_field = null
    ): string {
        if (null === $from_field) {
            $from_field = $model->getAttributeValue('from_field');
        }
        if (null === $to_field) {
            $to_field = $model->getAttributeValue('to_field');
        }
        if (null !== $date_min) {
            $dal = 'if('.$from_field.'=0 or '.$from_field.'<'.$date_min.' ,'.$date_min.','.$from_field.')';
        } else {
            $dal = $from_field;
        }
        if (null !== $date_max) {
            $al = 'if('.$to_field.'=0 or '.$to_field.'>'.$date_max.' ,'.$date_max.','.$to_field.')';
        } else {
            $al = $from_field;
        }
        $select = 'COALESCE(sum(greatest(datediff('.$al.','.$dal.')+1,0)),0)';

        return $select;
    }
}
