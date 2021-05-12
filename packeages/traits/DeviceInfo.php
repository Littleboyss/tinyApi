<?php
/**
 * 设备状态处理
 *
 * @category: traits
 * @author: chengye
 * @Date: 2019/05/06 星期一
 * @Time: 11:48
 */

namespace Packages\Traits;

use App\Exceptions\BusinessException;
use App\Exceptions\ErrorCode\BusinessError;
use Carbon\Carbon;
use Packages\UserCenter\Models\DeviceInfoModel;

//设备信息trait
trait DeviceInfo
{

    public function setDeviceInfo($ro, $admin_id = 0)
    {
        $deviceInfo = new DeviceInfoModel;
        if (!empty($ro->cid)) {
            $deviceInfo = $deviceInfo->whereCid($ro->cid)->first();
            if (empty($deviceInfo)) {
                $deviceInfo = new DeviceInfoModel;
                $deviceInfo->cid = $ro->cid;
                $deviceInfo->platform = $ro->platform ?? '';
                $deviceInfo->device = $ro->device ?? '';
                $deviceInfo->version = $ro->version ?? '';
                $deviceInfo->last_login_at = Carbon::now();
                $deviceInfo->save();
            }
            $deviceInfo->last_login_at = Carbon::now();
            if ($deviceInfo->status == DeviceInfoModel::STATUS['ban']) {
                $deviceInfo->save();
                if (config('device.checkDevice')) {
                    BusinessException::touch(BusinessError::UNAUTHORIZED_DEVICE);
                }
            } 
            if ($admin_id) {
                $deviceInfo->admin_id = $admin_id;
                $deviceInfo->save();
            }
        }
    }
}
