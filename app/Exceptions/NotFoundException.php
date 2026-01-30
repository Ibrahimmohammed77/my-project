<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class NotFoundException extends BaseException
{
    public function __construct(
        string $message = "المورد المطلوب غير موجود",
        array $messageParams = [],
        array $errors = null
    ) {
        parent::__construct(
            $message,
            'NOT_FOUND',
            Response::HTTP_NOT_FOUND,
            $errors,
            $messageParams
        );
    }

    /**
     * إنشاء استثناء لموارد محددة
     */
    public static function forResource(string $resourceName, $resourceId = null): self
    {
        $message = $resourceId
            ? "%s برقم %s غير موجود"
            : "%s غير موجود";

        $params = $resourceId
            ? [$resourceName, $resourceId]
            : [$resourceName];

        return new self($message, $params);
    }
}
