<?php

namespace App\Busi\Query;

use App\Http\Result\Result;

interface BusiInterface
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
class BaseQuery implements BusiInterface
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
    }

    /**
     * Busi 通用响应方法
     * @return Result
     */
    public function response()
    {
        return $this->result;
    }


    /**
     *  query 列表页带总数的响应方法
     * @param $total
     * @param $list
     * @return mixed
     */
    public function responseForList($total, $list)
    {
        $data = ['data' => ['list' => $list, 'page' => ['total' => $total]]];
        return $this->result->setData($data);
    }

    public function responseForLastId($lastId, $list)
    {
        $data = ['list' => $list, 'last_id' => $lastId];
        return $this->result->setData($data);
    }


    public function responseListSum($count, $sum)
    {
        $data = ['sum' => $sum, 'count' => $count];
        return $this->result->setData($data);
    }

    /**
     *  query 列表页带总数的响应方法
     * @param $total
     * @param $list
     */
    public function responseForListNotData($total, $list)
    {
        $data = ['list' => $list, 'page' => ['total' => $total]];
        $this->result->setData($data);
    }

    /**
     *  query 列表页带总数的响应方法
     * @param $total
     * @param $list
     * @param $page
     */
    public function responseForListPage($total, $list,$page = 1 )
    {
        $data = ['list' => $list, 'page' => ['page'=>$page,'total' => $total]];
        $this->result->setData($data);
    }

}

