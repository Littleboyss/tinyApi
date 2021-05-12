<?php
/**
 * 成员管理操作日志
 * 
 * @category: service
 * @author: chengye
 * @Date: 2020/05/06 星期三
 * @Time: 10:06
 */

namespace Packages\AdminManage;


use Packages\AdminManage\Entity\AdminOperationLogEntity;
use Packages\AdminManage\Models\AdminOperationLogModel;

class AdminOperationLogService
{
    public function addLog(AdminOperationLogEntity $entity)
    {
        $logModel = new AdminOperationLogModel();
        $logModel->action = $entity->action;
        $logModel->before = $entity->before;
        $logModel->after = $entity->after;
        $logModel->field = $entity->field;
        $logModel->created_admin_id = $entity->createdAdminId;
        $logModel->field_name = $entity->fieldName;
        $logModel->table_name = $entity->tableName;
        $logModel->operation_id = $entity->operationId;
        $logModel->operation = $entity->operation ??'';
        $logModel->operation_type = $entity->operationType ?? 1;
        $logModel->remark = $entity->remark??'';
        $logModel->type = $entity->type ?? 1;
        $logModel->saveOrFail();
    }
}