<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\{
    NotFoundHttpException,
    AccessDeniedHttpException,
    MethodNotAllowedHttpException,
    HttpException,
};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\{ThrottleRequestsException, PostTooLargeException};
use Illuminate\Database\QueryException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: static function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api/v1.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(static function (NotFoundHttpException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            $message = $e->getPrevious() instanceof ModelNotFoundException
                ? 'موردی با این مشخصات یافت نشد.'
                : 'آدرس مورد نظر یافت نشد.';

            return ApiResponse::notFound($message);
        });

        $exceptions->render(static function (ModelNotFoundException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            return ApiResponse::notFound('موردی با این مشخصات یافت نشد.');
        });

        $exceptions->render(static function (AccessDeniedHttpException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            return ApiResponse::forbidden();
        });

        $exceptions->render(static function (AuthenticationException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            return ApiResponse::unauthorized('لطفاً ابتدا وارد حساب کاربری شوید.');
        });

        $exceptions->render(static function (ValidationException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            return ApiResponse::validationFailed(errors: $e->errors());
        });

        $exceptions->render(static function (MethodNotAllowedHttpException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            return ApiResponse::error(
                'متد ارسالی برای این آدرس صحیح نیست.',
                code: Response::HTTP_METHOD_NOT_ALLOWED,
            );
        });

        $exceptions->render(static function (ThrottleRequestsException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            return ApiResponse::tooManyRequests(meta: ['retry_after' => $e->getHeaders()['Retry-After'] ?? null]);
        });

        $exceptions->render(static function (PostTooLargeException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            return ApiResponse::error(
                'حجم فایل ارسالی بیش از حد مجاز است.',
                code: Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
            );
        });

        $exceptions->render(static function (QueryException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            $sqlState = $e->errorInfo[0] ?? null;

            // duplicate key
            if ($sqlState === '23000') {
                return ApiResponse::error(
                    'اطلاعات وارد شده قبلاً ثبت شده است.',
                    code: Response::HTTP_CONFLICT,
                );
            }

            return ApiResponse::internalServerError('خطایی در پردازش اطلاعات رخ داده است.');
        });

        $exceptions->render(static function (HttpException $e, Request $request) {
            if (!$request->is('api/*')) return null;

            return ApiResponse::error(
                $e->getMessage() ?: 'خطایی در پردازش درخواست رخ داده است.',
                code: $e->getStatusCode(),
            );
        });

        $exceptions->render(static function (Throwable $e, Request $request) {
            if (!$request->is('api/*')) return null;

            $isDebug = config('app.debug');

            return ApiResponse::internalServerError(
                message: $isDebug ? $e->getMessage() : 'خطای غیرمنتظره‌ای رخ داده است. لطفاً کمی بعد مجدداً تلاش کنید.',
                errors: $isDebug ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => collect($e->getTrace())->take(5)->all(),
                ] : null,
            );
        });
    })->create();
