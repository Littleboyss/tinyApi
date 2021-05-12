<?php
/**
 * 员工管理操作
 * 
 * @category: entity
 * @author: chengye
 * @Date: 2020/05/06 星期三
 * @Time: 09:57
 */

namespace Packages\AdminManage\Entity;

class AdminOperationLogEntity
{
    /**
     * 动作
     * @var string
     */
    public $action;

    /**
     * 修改前的值
     * @var string
     */
    public $before;

    /**
     * 修改后的值
     * @var string
     */
    public $after;

    /**
     * 被修改的表字段
     * @var string
     */
    public $field;

    /**
     * 操作来源
     * @var string
     */
    public $source;

    /**
     * 被修改的字段名
     * @var string
     */
    public $fieldName;


    /**
     * 表名
     * @var string
     */
    public $tableName;

    /**
     * 创建人id
     * @var int
     */
    public $createdAdminId;

    /**
     * 操作对象
     * @var string
     */
    public $operation;

    /**
     * 操作对象id
     * @var int
     */
    public $operationId;

    /**
     * 操作对象类型
     * @var string
     */
    public $operationType;

    /**
     * 备注
     * @var string
     */
    public $remark;

    /**
     * 操作类型
     * @var int
     */
    public $type;

}