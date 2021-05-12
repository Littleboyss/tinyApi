<?php


namespace Packages\AdminManage\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Packages\AdminManage\Model\OutsiderModel
 *
 * @property int $id
 * @property string $real_name 真实姓名
 * @property string $phone 手机号
 * @property string $wechat_number 微信号
 * @property int $status 状态：1、生效；2、失效
 * @property string $remark 备注
 * @property int $created_admin_id 创建人id
 * @property int $update_admin_id  最后更新人id
 *
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at

 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Packages\AdminManage\Model\OutsiderModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereRealName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereWechatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereCreatedAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereUpdateAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\OutsiderModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Packages\AdminManage\Model\OutsiderModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Packages\AdminManage\Model\OutsiderModel withoutTrashed()
 * @mixin \Eloquent
 */

class OutsiderModel extends Model
{
    use SoftDeletes;
    protected $table = 'outsider';

}