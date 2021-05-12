<?php


namespace Packages\AdminManage\Query;


use Packages\AdminManage\Model\OutsiderModel;
use Packages\NetworkMarketing\Querys\NetworkMarketingQuery;
use Packages\Traits\ModelQueryTrait;

class OutsiderQuery
{
    use ModelQueryTrait;

    public function __construct(OutsiderModel $model)
    {
        $this->model = $model;
    }

    /**
     * 通过真实姓名查询数据
     * @param $real_name
     * @return $this
     */
    public function byRealNameLike($real_name) {
        if (!empty($real_name)) {
            $this->model = $this->model->where('real_name', 'like', '%' . $real_name . '%');
        }
        return $this;
    }

    /**
     * 通过手机号查询数据
     * @param $phone
     * @return $this
     */
    public function byPhoneLike($phone){
        if (!empty($phone)) {
            $this->model = $this->model->where('phone', 'like', '%' . $phone . '%');
        }
        return $this;
    }

    /**
     * 通过微信号查询数据
     * @param $wechat_number
     * @return $this
     */
    public function byWechatNumber($wechat_number){
        if (!empty($wechat_number)) {
            $this->model = $this->model->where('wechat_number', 'like', '%' . $wechat_number . '%');
        }
        return $this;
    }

    /**
     * 通过状态查询数据
     * @param $status
     * @return $this
     */
    public function byStatus($status){
        if (!empty($status)){
            $this->model = $this->model->where('status', $status);
        }
        return $this;
    }

    /**
     * 通过创建时间查询数据
     * @param $startTime
     * @param $endTime
     * @return $this
     */
    public function byCreatedTime($startTime, $endTime)
    {
        if(!empty($startTime) && !empty($endTime)){
            $this->model = $this->model->where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime);
        }

        return $this;
    }

}