<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class BusinessLogicException extends BaseException
{
    public function __construct(
        string $message = "خطأ في منطق العمل",
        string $errorCode = "BUSINESS_ERROR",
        array $messageParams = [],
        array $errors = null,
        int $httpStatusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct(
            $message,
            $errorCode,
            $httpStatusCode,
            $errors,
            $messageParams
        );
    }

    /**
     * إنشاء استثناء لعملية محددة
     */
    public static function forOperation(string $operation, string $reason = null): self
    {
        $message = $reason
            ? "فشلت عملية %s: %s"
            : "فشلت عملية %s";

        $params = $reason
            ? [$operation, $reason]
            : [$operation];

        return new self($message, 'OPERATION_FAILED', $params);
    }
}
