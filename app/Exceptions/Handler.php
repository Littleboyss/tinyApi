<?php

namespace App\Exceptions;

use App\Exceptions\ErrorCode\SystemError;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        //用户自定义类异常就不上报了
        if (!$exception instanceof BaseException) {
            parent::report($exception);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if ($exception instanceof BaseException) {
            return response()->packet($exception->data)
                ->error($exception->getMessage(), $exception->getCode());
        } elseif ($exception instanceof AuthenticationException) {
            return response()->packet()->unAuth('请登录后进行访问');
        } elseif ($exception instanceof ModelNotFoundException) {
            return response()->packet()->fail('没有找到数据');
        }
        if ($exception instanceof RequestException) {
            return response()->packet()->error($exception->getMessage(),SystemError::PARAM_ERROR);
        }
        if ($exception instanceof AuthorizationException) {
            return response()->packet()->fail('无权访问该接口，请联系管理员！');
        }
        if (config('app.debug') == true) {
            return parent::render($request, $exception);
        }

        if ($exception instanceof \RuntimeException) {
            return response()->packet()->error('服务器异常');
        }

        if ($exception instanceof \Exception) {
            return response()->packet()->error($exception->getMessage());
        }


    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}
