<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Throwable;

class ServerErrorException extends BaseException
{
    public function __construct(
        string $message = "حدث خطأ في الخادم",
        array $messageParams = [],
        array $errors = null,
        Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            'SERVER_ERROR',
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $errors,
            $messageParams
        );
    }

    /**
     * إنشاء من استثناء آخر
     */
    public static function fromException(Throwable $e, string $customMessage = null): self
    {
        $message = $customMessage ?: "حدث خطأ غير متوقع";

        return new self(
            $message,
            [],
            [
                'original_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        );
    }
}
