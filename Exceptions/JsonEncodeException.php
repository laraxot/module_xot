<?php
/**
 * @see https://dev.to/jackmiras/laravels-exceptions-part-2-custom-exceptions-1367
 */

declare(strict_types=1);

namespace Modules\Xot\Exceptions;

use Illuminate\Http\Response;

class JsonEncodeException extends ApplicationException
{
    public function status(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function help(): string
    {
        $res = trans('exception.json_not_encoded.help');
        if (! \is_string($res)) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }

        return $res;
    }

    public function error(): string
    {
        $res = trans('exception.json_not_encoded.error');
        if (! \is_string($res)) {
            throw new \Exception('['.__LINE__.']['.__FILE__.']');
        }

        return $res;
    }
}
