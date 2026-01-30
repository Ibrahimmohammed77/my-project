<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class BaseException extends Exception
{
    protected string $errorCode;
    protected int $httpStatusCode;
    protected array $errors;
    protected array $messageParams;

    public function __construct(
        string $message = "",
        string $errorCode = "ERROR",
        int $httpStatusCode = Response::HTTP_BAD_REQUEST,
        array $errors = null,
        array $messageParams = []
    ) {
        parent::__construct($message);

        $this->errorCode = $errorCode;
        $this->httpStatusCode = $httpStatusCode;
        $this->errors = $errors ?? [];
        $this->messageParams = $messageParams;
    }

    /**
     * تقرير الاستثناء
     */
    public function report(): void
    {
        // Log the exception if needed
        \Log::error($this->getMessage(), [
            'code' => $this->errorCode,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTrace()
        ]);
    }

    /**
     * تحويل الاستثناء إلى response
     */
    public function render(Request $request): JsonResponse
    {
        $message = $this->getMessage();

        // تطبيق المعاملات على الرسالة إذا وجدت
        if (!empty($this->messageParams)) {
            $message = vsprintf($message, $this->messageParams);
        }

        $response = [
            'code' => $this->errorCode,
            'message' => $message,
            'status' => false,
            'data' => null,
            'error' => $this->errors,
            'pagination' => null
        ];

        return response()->json($response, $this->httpStatusCode);
    }

    /**
     * الحصول على كود الخطأ
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * الحصول على كود حالة HTTP
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
