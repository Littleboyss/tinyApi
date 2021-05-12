<?php
/**
 * Created by PhpStorm.
 * User: niebangheng
 * Date: 2018/10/23
 * Time: 11:29
 */

namespace Packages\Traits;


use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

trait ModelTrait
{
    /**
     * 创建一个新的uuid
     * @param string $name
     * @return string
     */
    public static function uuid($name = '')
    {
        return Uuid::uuid3(Uuid::uuid4()->toString(), $name . env('APP_KEY'))->toString();
    }

    /**
     * 生成一个32位的订单号
     * 使用按位加密法进行加密后变为16进制的字符串
     * @param $version
     * @return string
     */
    public static function makeSn($version)
    {
        //年月日
        $now = Carbon::now();
        $orderId = $now->year + $now->month + $now->day + $version;
        //加密
        $lockNumber = $orderId ^ self::MAGIC_NUMBER;
        //转16进制
        $lockNumber = dechex($lockNumber);
        //拼装订单日期
        $lockNumber = $now->year . $now->month . $now->day . $lockNumber;
        return sprintf('%02d', rand(0, 99)) . $lockNumber;
    }
}