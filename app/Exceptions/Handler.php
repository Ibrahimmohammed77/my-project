<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * قائمة الاستثناءات التي لا يجب الإبلاغ عنها
     */
    protected $dontReport = [
        ValidationException::class,
        NotFoundException::class,
        UnauthorizedException::class,
        ForbiddenException::class,
    ];

    /**
     * تسجيل الاستثناء
     */
    public function report(Throwable $exception): void
    {
        if ($exception instanceof BaseException) {
            $exception->report();
            return;
        }

        parent::report($exception);
    }

    /**
     * تحويل الاستثناء إلى response
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        // التعامل مع استثناءات API فقط
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * معالجة استثناءات API
     */
    private function handleApiException(Request $request, Throwable $exception): JsonResponse
    {
        // إذا كان استثناءً مخصصاً من قبلنا
        if ($exception instanceof BaseException) {
            return $exception->render($request);
        }

        // إذا كان استثناء تحقق من Laravel
        if ($exception instanceof LaravelValidationException) {
            $validationException = ValidationException::fromLaravelException($exception);
            return $validationException->render($request);
        }

        // إذا كان استثناء Model NotFound
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $model = class_basename($exception->getModel());
            $notFoundException = NotFoundException::forResource($model);
            return $notFoundException->render($request);
        }

        // إذا كان استثناء Authentication
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $unauthorizedException = new UnauthorizedException();
            return $unauthorizedException->render($request);
        }

        // إذا كان استثناء Authorization
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            $forbiddenException = new ForbiddenException();
            return $forbiddenException->render($request);
        }

        // استثناءات أخرى غير متوقعة
        $serverErrorException = ServerErrorException::fromException($exception);
        return $serverErrorException->render($request);
    }
}
