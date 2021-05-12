<?php

namespace App\Http\Requests\Admin;

use App\Exceptions\RequestException;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;
use ReflectionClass;
use ReflectionProperty;

class BaseRequest extends FormRequest
{


    /**
     * @txt 客户端设备id
     * @var string
     * @rules string
     */
    public $cid;

    /**
     * @txt 平台
     * @var string
     * @rules string
     */
    public $platform;

    /**
     * @txt 设备信息
     *
     * @var string
     */
    public $device;

    /**
     * @txt 客户端版本
     * @var string
     * @rules string
     */
    public $version;

    /**
     * @txt 数据返回类型
     * @rules string|in:data,sum,page,today_sum
     */
    public $response_data_type = '';

    /**
     * 支持的注解名称
     */
    const _RULE_KEY = '@rules';

    const _RULE_TXT = '@txt';

    /**
     * 参数的反射缓存
     * @var array
     */
    protected $inputKeys = [];

    private $startTimeKeys = [];

    private $endTimeKeys = [];

    private $txt = [];

    /**
     * 重写验证器逻辑
     * @param ValidationFactory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function createDefaultValidator(ValidationFactory $factory)
    {
        $rules = $this->injectRules();
        return $factory->make(
            $this->validationData(), $rules,
            $this->messages(), $this->attributes()
        );
    }

    /**
     * 反射实例注入到表单规则中去
     */
    private function injectRules()
    {
        $params = [];
        $class = new ReflectionClass($this);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $k => $item) {
            $docs = $item->getDocComment();
            $pattern = "#(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_].*)#";
            preg_match_all($pattern, $docs, $matches, PREG_PATTERN_ORDER);
            if (!empty($matches)) {
                $match = $matches[0];
                foreach ($match as $docLine) {
                    $param = explode(' ', trim($docLine));
                    if (count($param) < 2) {
                        continue;
                    }
                    list($key, $value) = $param;
                    //进入验证规则
                    if ($key == self::_RULE_KEY) {
                        //拆分具体规则，把 | 分隔符拆成数组
                        $params[$item->getName()] = explode('|', $value);
                        $this->inputKeys[] = $item->getName();
                    }
                    if($key == self::_RULE_TXT)
                    {
                        $this->txt[$item->getName()] = $value;
                    }

                }
            }
        }
        //合并自身rules方法产生的自定义规则
        $rules = $this->container->call([$this, 'rules']);
        foreach ($rules as $key => $rule) {
            if (isset($params[$key])) {
                if (is_array($rule)) {
                    $params[$key] = array_merge($params[$key], $rule);
                } else {
                    $params[$key][] = $rule;
                }
                $params[$key] = array_unique($params[$key]);
            } else {
                $params[$key] = $rule;
            }
        }
        return $params;
    }

    public function attributes()
    {
        return $this->txt;
    }

    /**
     * @param array $errors
     * @return \Symfony\Component\HttpFoundation\Response|void
     * @throws RequestException
     */
    public function response(array $errors)
    {
        $error = array_shift($errors);
        $errorMsg = $error[0];
        throw new RequestException($errorMsg);
    }


    /**
     * 验证完以后注入回属性中去
     */
    public function withValidator()
    {
        foreach ($this->inputKeys as $inputKey) {
            $this->$inputKey = is_null($this->input($inputKey)) ? $this->$inputKey : $this->input($inputKey);
            $val = $this->input($inputKey);
            foreach ($this->startTimeKeys as $startTimeKey) {
                if ($startTimeKey == $inputKey && !empty($val)) {
                    $date = Carbon::parse($val)->toDateString();
                    $startDate = Carbon::parse($date);
                    $this->$inputKey = $startDate->toDateTimeString();
                }
            }
            foreach ($this->endTimeKeys as $endTimeKey) {
                if ($endTimeKey == $inputKey && !empty($val)) {
                    $date = Carbon::parse($val)->toDateString();
                    $endDate = Carbon::parse($date)->addDay(1)->subSecond(1);
                    $this->$inputKey = $endDate->toDateTimeString();
                }
            }
        }
    }

    public function setStartTimeKeys($startTimeKeys = [])
    {
        $this->startTimeKeys = $startTimeKeys;
    }

    public function setEndTimeKeys($endTimeKeys = [])
    {
        $this->endTimeKeys = $endTimeKeys;
    }


}
