<?php
/**
 * 成员管理操作日志
 * 
 * @category: facades
 * @author: chengye
 * @Date: 2020/05/06 星期三
 * @Time: 10:03
 */

namespace Packages\AdminManage\Facades;


use Illuminate\Support\Facades\Facade;
use Packages\AdminManage\AdminOperationLogService;

class AdminOperationLog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AdminOperationLogService::class;
    }
}