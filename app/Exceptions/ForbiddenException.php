<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class ForbiddenException extends BaseException
{
    public function __construct(
        string $message = "غير مسموح بالوصول",
        array $messageParams = [],
        array $errors = null
    ) {
        parent::__construct(
            $message,
            'FORBIDDEN',
            Response::HTTP_FORBIDDEN,
            $errors,
            $messageParams
        );
    }
}
