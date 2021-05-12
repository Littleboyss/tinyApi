<?php
namespace Packages\Traits;

use App\Models\SystemLog;

trait EventsTrait
{
    /**
     * key = 事件名称
     * value = 存储的表名
     * @var array
     */
    public $EVENTS = [
        'apply' => 'apply_log',
        'user' => 'user_log',
        'order'=> 'order_log',
        'order_notify'=>'order_notify_log'  //第三方支付回调记录
    ];


    /**
     * key = 具体操作类型
     * value = 操作的中文解释
     * @var array
     */
    public $ACTION = [
        'create' => '添加',
        'update' => '修改',
        'delete' => '删除'
    ];

    /**
     * 记录业务日志
     * @param string 操作事件 $event
     * @param string 操作类型 $action
     * @param array 记录的数据 $data
     * @param int 操作人ID $adminId
     * @param string 备注信息支持模板语句 $remark
     * @return bool
     */
    public function busEvent($event, $action, $data, $adminId, $remark = '')
    {
        if (!$this->checkEvent($event)) {
            return false;
        }
        $log = new SystemLog();
        $log->setTable($event);
        $log->action = $action;
        $log->data = $data;
        $log->admin_id = $adminId;
        $log->remark = $remark;
        return $log->save();
    }

    /**
     * 检查事件类型
     * @param $event
     * @return bool
     */
    private function checkEvent($event)
    {
        foreach ($this->EVENTS as $e) {
            if ($e == $event) {
                return true;
            }
        }
        return false;
    }

}