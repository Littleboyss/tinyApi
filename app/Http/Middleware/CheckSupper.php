<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use App\Exceptions\ErrorCode\BusinessError;
use App\Models\Manage\Admin;
use Closure;
use Illuminate\Support\Facades\Auth;

//校验用户是否是超级管理员
class CheckSupper
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( Auth::user()->is_supper != Admin::IS_SUPPER['true'] ){
            BusinessException::touch(BusinessError::ROLE_REQUEST_DENIED);
        }

        return $next($request);
    }
}
