<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class ServiceException extends BaseException
{
    public function __construct(
        string $message = "خطأ في الخدمة",
        string $serviceName = null,
        array $messageParams = [],
        array $errors = null
    ) {
        $message = $serviceName
            ? "خطأ في خدمة %s"
            : $message;

        $params = $serviceName
            ? [$serviceName]
            : $messageParams;

        parent::__construct(
            $message,
            'SERVICE_ERROR',
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $errors,
            $params
        );
    }
}
