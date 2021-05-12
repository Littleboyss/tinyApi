###api错误码命名规则

- 前四位(模块)+标志位(1业务/2系统)+后三位错误详情
- 第5位是标记位 1为系统级错误 2为业务级错误
- 6-8 为业务自定义错误码

####错误码模块分配

| 错误码 | 说明|
|--------|--------|
|1000  |系统级别错误|
|1001  |用户鉴权模块错误|
|1800  |用户模块错误|
|5000  |请求参数验证|
|1900  |学科产品错误|
|1901  |报名单错误|
|1902  |基础配置错误|
|1702  |优惠券验证错误|



错误码拉取规则:
```php
throw new UserException(ErrorCode);
```

以上为用户异常, 抛出的异常错误码将去UserException中定义的多语言文件中寻找,如: zh_cn/user_error.php
```php
return [ErrorCode => 'msg'];

```
此处强制要求抛出模块异常使用对应模块错误码:

```php
throw new UserException(UserError::PHONE_NOT_FOUND);
throw new MatchException(MatchError::MATCH_NOT_FOUND);
```

