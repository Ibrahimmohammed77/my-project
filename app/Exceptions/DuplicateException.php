<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class DuplicateException extends BaseException
{
    public function __construct(
        string $message = "بيانات مكررة",
        array $messageParams = [],
        array $errors = null
    ) {
        parent::__construct(
            $message,
            'DUPLICATE_ENTRY',
            Response::HTTP_CONFLICT,
            $errors,
            $messageParams
        );
    }

    /**
     * إنشاء استثناء لحقول محددة
     */
    public static function forField(string $fieldName, $fieldValue = null): self
    {
        $message = $fieldValue
            ? "قيمة %s '%s' مُستخدمة مسبقاً"
            : "حقل %s مُستخدم مسبقاً";

        $params = $fieldValue
            ? [$fieldName, $fieldValue]
            : [$fieldName];

        return new self($message, $params);
    }
}
