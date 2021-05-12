<?php


namespace Packages\Traits;

use Exception;
use GuzzleHttp\Client as HttpClient;
use Packages\Third\src\Exceptions\DingtalkException;

trait Third
{
    /**
     * 发送相关请求
     * @param $url
     * @param string $type
     * @param array $data
     * @return array
     * @throws DingtalkException
     */
    public function request($url, $type = 'get', $data = [])
    {
        $http = new HttpClient(['verify'=>false]);
        $result = [];
        try {
            switch ($type) {
                case 'post' :
                    $result = $this->requestResult($http->post($url, $data));
                    break;
                default :
                    $result = $this->requestResult($http->get($url));
                    break;
            }
            return $result;
        } catch (Exception $exception) {
            throw new DingtalkException($exception->getMessage(),$exception->getCode());
        }
    }

    /**
     * @param $response
     * @return mixed
     * @throws Exception
     */
    public function requestResult($response)
    {
        $result = json_decode((string)$response->getBody(), true);
        if ($response->getStatusCode() == 200) {
            if ($result['errcode'] == 0) {
                return $result;
            }else{
                throw new DingtalkException($result['errmsg'],$result['errcode']);
            }
        }else{
            throw new Exception('network error!',$response->getStatusCode());
        }
    }

    /**
     * 数组转字符串
     * @param $array
     * @return string
     */
    public function arrayToString($array,$symbol = ',') : string
    {
        return implode($symbol,$array);
    }
}