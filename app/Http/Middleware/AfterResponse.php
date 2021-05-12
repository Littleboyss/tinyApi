<?php

namespace App\Http\Middleware;

use App\Exceptions\ErrorCode\SystemError;
use App\Exceptions\SystemException;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class AfterResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     * @throws SystemException
     */
    public function handle($request, Closure $next)
    {
        $ret = $next($request);
        if ($ret->getStatusCode() == Response::HTTP_TOO_MANY_REQUESTS) {
            throw new SystemException(SystemError::TO_MANY_REQUEST);
        }
        return $ret;
    }
}
