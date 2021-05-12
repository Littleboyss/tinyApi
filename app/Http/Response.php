<?php

namespace App\Http;

use App\Http\Result\Result;
use Illuminate\Http\JsonResponse;

class Response extends JsonResponse
{
    protected $success = [
        'ret' => 0,
        'result' => 0,
        'res_info' => 'ok',
    ];

    /**
     * Constructor.
     *
     * @param  mixed $data
     * @param  int $status
     * @param  array $headers
     * @param  int $options
     */
    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
    {
        if ($data instanceof Result) {
            $data = $data->toArray();
        }
        if ($data) {
            $data = array_merge($this->success, $data);
        } else {
            $data = $this->success;
        }
        parent::__construct($data, $status, $headers, $options);
    }

    public function ret($value)
    {
        $data = $this->getData(true);
        $data['ret'] = $value;
        $this->setData($data);
        return $this;
    }

    /**
     * 设置错误码
     *
     * @param $value
     * @return $this
     */
    public function errorCode($value)
    {
        $data = $this->getData(true);
        $data['result'] = $value;
        $this->setData($data);
        return $this;
    }

    /**
     * 设置data数据
     *
     * @param $value
     * @return $this
     */
    public function data($value)
    {
        $data = $this->getData(true);
        $data['data'] = $value;
        $this->setData($data);
        return $this;
    }

    public function fail($message)
    {
        $data = [
            'ret' => -2,
            'result' => -2,
            'res_info' => $message,
        ];

        $data = array_merge($this->getData(true), $data);

        $this->setData($data);
        return $this;
    }

    /**
     * 返回错误信息
     *
     * @param $message
     * @param null $code
     * @return $this
     */
    public function error($message, $code = null)
    {
        $data = [
            'ret' => -1,
            'result' => $code ?: -1,
            'res_info' => $message,
        ];

        $data = array_merge($this->getData(true), $data);

        $this->setData($data);
        return $this;
    }

    public function unAuth($message)
    {
        $data = [
            'ret' => -3,
            'result' => -3,
            'res_info' => $message,
        ];

        $data = array_merge($this->getData(true), $data);

        $this->setData($data);
        return $this;
    }
}
