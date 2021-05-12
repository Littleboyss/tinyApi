<?php


namespace Packages\AdminManage\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Packages\AdminManage\Model\DingtalkEventLogModel
 *
 * @property int $id
 * @property string $event_type 事件类型
 * @property \Carbon\Carbon|null $event_at 事件触发时间
 * @property array $body 触发事件后产生的数据体
 *
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at

 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel whereEventAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Packages\AdminManage\Model\DingtalkEventLogModel withoutTrashed()
 * @mixin \Eloquent
 */

class DingtalkEventLogModel extends Model
{
    use SoftDeletes;

    protected $table = 'dingtalk_event_log';

    protected $casts = [
        'body' => 'json'
    ];

}