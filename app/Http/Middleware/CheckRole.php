<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use App\Exceptions\ErrorCode\BusinessError;
use App\Models\BasicConfig\AdminJobPosition;
use App\Models\BasicConfig\JobPositionRole;
use Auth;
use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    const ROLES = [
        'onlineCounselor' => '1',           //在线客服
        'counselor' => '2',                  //咨询师
        'counselorManager' => '3',          //咨询师主管
        'finance' => '4',                    //财务
        'teachingManagement' => '5',        //教务
        'branchSchool' => '6',              //分校
        'manager' => '7',              //管理
        'counselorCenterManager' => '8',              //咨询中心管理
    ];

    /**
     * @param $request
     * @param Closure $next
     * @return bool|mixed
     * @throws \App\Exceptions\BaseException
     */
    public function handle($request, Closure $next)
    {

        $uid = Auth::id();
        if(empty($uid))
            BusinessException::touch(BusinessError::AUTH_ERROR);
        //职称
        $jobPositions = AdminJobPosition::whereAdminId($uid)->get()->pluck('job_position_id')->toArray();
        if(empty($jobPositions))
            BusinessException::touch(BusinessError::ROLE_REQUEST_DENIED);
        //角色
        $roles = JobPositionRole::whereIn('job_position_id',$jobPositions)->get()->pluck('role_id')->toArray();
        if(empty($roles))
            BusinessException::touch(BusinessError::ROLE_REQUEST_DENIED);
        $pathInfo = $request->getPathInfo();
        $path = explode('/',$pathInfo);
        $path = array_values(array_filter($path));
        $workbench = $path['2'] ?? null;
        switch ($workbench){
            case  'counselor' :
                if(in_array(self::ROLES['counselor'],$roles))
                    return $next($request);
                break;                  //咨询师
            case 'online_counselor';
                if(in_array(self::ROLES['onlineCounselor'],$roles))
                    return $next($request);
                break;                  //在线客服
            case 'branch_school';
                if(in_array(self::ROLES['branchSchool'],$roles))
                    return $next($request);
                break;                  //分校
            case 'finance';
                if(in_array(self::ROLES['finance'],$roles))
                    return $next($request);
                break;                  //财务
            case 'teaching_management';
                if(in_array(self::ROLES['teachingManagement'],$roles))
                    return $next($request);
                break;                  //教务
            case 'counselor_manager';
                if(in_array(self::ROLES['counselorManager'],$roles))
                    return $next($request);
                break;                  //咨询师管理
            case 'manager';
                if(in_array(self::ROLES['manager'],$roles))
                    return $next($request); //管理
                break;
            case 'counselor_center_manager';
                if(in_array(self::ROLES['counselorCenterManager'],$roles))
                    return $next($request); //管理
                break;
        }
        BusinessException::touch(BusinessError::ROLE_REQUEST_DENIED);
    }
}
