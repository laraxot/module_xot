<?php

// namespace Intervention\Image\Templates;

declare(strict_types=1);

namespace Modules\Xot\Filters\Images;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Medium implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->fit(240, 180);
    }
}
