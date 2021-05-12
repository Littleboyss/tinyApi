<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use App\Exceptions\ErrorCode\BusinessError;
use App\Exceptions\ErrorCode\SystemError;
use App\Exceptions\SystemException;
use App\Util\SxmapsNetAuthUtil;
use Carbon\Carbon;
use Closure;
use Cache;
use Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            BusinessException::touch(BusinessError::AUTH_ERROR);
        }
        //获取缓存中的权限列表
        $permissionsCacheKey = 'menus';
        $permissions = Cache::get($permissionsCacheKey);
        $perClient = new SxmapsNetAuthUtil(env('SXMAPS_NET_ADDRS'));
        //如果权限列表为空，去获取一下
        if (empty($permissions)) {
            $permissions = $perClient->getAllPermission();
            Cache::put($permissionsCacheKey, $permissions, Carbon::now()->addMinutes(5));
        }
        //判断权限
        $user = Auth::user();
        $userRoles = Cache::get($user->user_id . '_roles');
        if (empty($userRoles)) {
            //无权访问接口
            SystemException::touch(SystemError::AUTH_NOT_PERMISSION);
        } else {
            //超级管理员角色的ID
            $supperUser = env('SXMAPS_NET_SUPPER_USER_ID');
            //获取用户登录的权限
            $loginRole = Cache::get($user->user_id . 'login_role');
            if (empty($loginRole)) {
                SystemException::touch(SystemError::AUTH_NOT_SELECT_ROLE);
            }
            $selfActionName = \Route::current()->getActionName();
            list($class, $method) = explode('@', $selfActionName);
            foreach ($userRoles as $userRole) {
                //超级用户默认有所有权限
                if ($loginRole == $supperUser) {
                    return $next($request);
                }
                //检查具体的权限
                if ($userRole == $loginRole) {
                    $rolePermissions = $perClient->getRolePermission($loginRole);
                    foreach ($rolePermissions as $rolePermission) {
                        //检查权限
                        if ($rolePermission['m'] == $class && $rolePermission['c'] == $method) {
                            return $next($request);
                        }
                    }
                }
            }
        }
        SystemException::touch(SystemError::AUTH_NOT_PERMISSION);
    }
}
