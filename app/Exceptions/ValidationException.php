<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException as LaravelValidationException;

class ValidationException extends BaseException
{
    public function __construct(
        array $errors = [],
        string $message = "بيانات غير صالحة",
        array $messageParams = []
    ) {
        parent::__construct(
            $message,
            'VALIDATION_ERROR',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $errors,
            $messageParams
        );
    }

    /**
     * إنشاء من Laravel Validation Exception
     */
    public static function fromLaravelException(LaravelValidationException $e): self
    {
        return new self($e->errors(), $e->getMessage());
    }
}
