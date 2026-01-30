<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UnauthorizedException extends BaseException
{
    public function __construct(
        string $message = "غير مصرح بالوصول",
        array $messageParams = [],
        array $errors = null
    ) {
        parent::__construct(
            $message,
            'UNAUTHORIZED',
            Response::HTTP_UNAUTHORIZED,
            $errors,
            $messageParams
        );
    }
}
