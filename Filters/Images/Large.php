<?php

declare(strict_types=1);

// namespace Intervention\Image\Templates;

namespace Modules\Xot\Filters\Images;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Large implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(480, 360);
    }
}
