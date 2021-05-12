<?php
/**
 * 基础配置缓存
 *
 * @category: trait
 * @author: chengye
 * @Date: 2019/05/13 星期一
 * @Time: 17:59
 */
namespace Packages\Traits;

use Illuminate\Support\Facades\Cache;

trait BasicConfigCacheTrait
{
    // const CACHE_KEY = [
    //     'basic_config' => 'busiV3:query:basicConfig:cache:basic_config',
    //     'sales_group' => 'busiV3:query:basicConfig:cache:sales_group',
    //     'pop_website' => 'busiV3:query:basicConfig:cache:pop_website',
    //     'pop_host'  => 'busiV3:query:basicConfig:cache:pop_host',
    //     'product'  => 'busiV3:query:basicConfig:cache:product',
    //     'product_package'  => 'busiV3:query:basicConfig:cache:product_package',
    //     'price_name'  => 'busiV3:query:basicConfig:cache:price_name',
    //     'permission_config'  => 'busiV3:query:basicConfig:cache:permission_config',
    //     'cache_timestamp'  => 'busiV3:query:basicConfig:cache:cache_timestamp',
    // ];
    private static $cacheKey = 'busiV3:query:basicConfig:cache:%s';

    /**
     * @return mixed|null
     */
    public function getBasicConfigCache()
    {
        $cache = Cache::get(sprintf(self::$cacheKey, 'basicConfigEntity'));

        if ($cache) {
            return $cache;
        }

        return null;
    }

    /**
     * @param $model
     * @param $data
     */
    public function setBasicConfigCache($data)
    {
        Cache::forever(sprintf(self::$cacheKey, 'basicConfigEntity'), $data);
    }

    /**
     * @param $modelName
     * @param $id
     */
    public function delBasicConfigCache($type = '')
    {
        Cache::forget(sprintf(self::$cacheKey, 'basic_config'));
        if ($type) {
            Cache::forget(sprintf(self::$cacheKey, $type));
        }
    }

}
