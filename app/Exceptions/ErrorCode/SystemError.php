<?php
/**
 * Created by PhpStorm.
 * User: niebangheng
 * Date: 2018/3/31
 * Time: 15:18
 */

namespace App\Exceptions\ErrorCode;
class SystemError extends BaseError
{
    const AUTH_NOT_PERMISSION = '10011001'; //用户无权限访问此接口
    const AUTH_NOT_ROLE = '10011002'; //该用户无此角色
    const AUTH_NOT_SELECT_ROLE = '10011003'; //没有选择角色


    const INT_MIN = 50001001;
    const INT_MAX = 50001002;
    const INT_REQUIRED = 50001003;
    const FIELD_REQUIRED = 50001004;
    const OUT_OF_ENUM = 50001005;
    const NUMBER_REQUIRED = 50001006;


    const CK_ERROR = '10002001';
    const TO_MANY_REQUEST = '10002002';
    const PARAM_ERROR = '10002003';
    const BUILD_ERROR = '10002004';
    const INTERNAL_ERROR = '10002005';
    const CALL_THIRD_ERROR = '10002006';
    const INSIDE_ERROR = '10002007';

    const RO_ERROR_NOTICE = [
        self::INT_MIN => '不能小于最小值:',
        self::INT_MAX => '不能大于最大值:',
        self::INT_REQUIRED => '需要整型',
        self::FIELD_REQUIRED => '不能为空',
        self::OUT_OF_ENUM => '不在指定范围',
        self::NUMBER_REQUIRED => '必须是数字',
    ];
}