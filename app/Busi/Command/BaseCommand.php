<?php

namespace App\Busi\Command;

use App\Http\Result\Result;
use App\Models\User\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;


interface CommandInterface
{
    /**
     * Busi 通用响应方法
     *
     * @return Result
     */
    public function response();

}

/**
 * Busi基类
 *
 * Class BaseBusi
 *
 * @package App\Busi\Modules
 */
class BaseCommand implements CommandInterface
{

    /**
     * @var Result
     */
    protected $result = null;


    /**
     * BaseBusi constructor.
     *
     * @param array|null $roParams
     * @param Result $result
     */
    public function __construct(array $roParams = [], Result $result)
    {
        $this->result = $result;
        $data = app()->call([$this, 'handle'], $roParams);
        if ($data instanceof Result) {
            $this->result = $data;
        } else if (!is_null($data)) {
            $this->result->setData($data);
        }
        if (\Auth::user()) {
            $token = \Auth::user()->token();
            $token->expires_at = Carbon::now()->addHour(6);
            $token->save();
        }
    }


    /**
     * Busi 通用响应方法
     * @return Result
     */
    public function response()
    {
        return $this->result;
    }

}

