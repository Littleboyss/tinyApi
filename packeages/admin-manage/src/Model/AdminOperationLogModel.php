<?php
/**
 * 员工管理操作日志
 *
 * @category: model
 * @author: chengye
 * @Date: 2020/05/06 星期三
 * @Time: 09:52
 */

namespace Packages\AdminManage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminOperationLogModel extends Model
{
    use SoftDeletes;

    const ACTION = [
        'create' => '创建',
        'update' => '修改',
        'delete' => '删除',
    ];

    const TYPE = [
        1 => '新增组 - 名称',
        2 => '编辑组 - 负责人',
        3 => '编辑组 - 成员',
        4 => '编辑组 - 关联分校',
        5 => '删除组',
        6 => '注册账号-ID',
        7 => '编辑 - 手机号',
        8 => '编辑 - 姓名',
        9 => '编辑 - 邮箱',
        10 => '编辑 - 状态',
        11 => '编辑 - 职称',
        12 => '编辑 - 所属组 （ 按编辑 组成员 或 负责人 执行）',
        13 => '编辑 - 关联第三方登录ID',
        14 => '编辑组 - 组名',
        15 => '重置密码',
        16 => '编辑组 - 关联学院',
    ];

    const EN_TYPE = [
        'add_group_name' => 1,
        'update_group_owner' => 2,
        'update_group_admin' => 3,
        'update_group_branch_school' => 4,
        'delete_group' => 5,
        'register_account' => 6,
        'update_phone' => 7,
        'update_admin_name' => 8,
        'update_email' => 9,
        'update_status' => 10,
        'update_job_position' => 11,
        'update_admin_group' => 12,
        'update_third_id' => 13,
        'update_group_name' => 14,
        'recover_password' => 15,
        'update_group_academy' =>16,
    ];

    const OPERATION_TYPE = [
        'admin_id' => 1,
        'group_id' => 2,
    ];

    protected $table = 'admin_operation_log';

}
