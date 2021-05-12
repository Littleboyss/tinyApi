<?php

namespace Packages\Traits;


use App\Models\BasicConfig\BranchSchool;
use App\Models\BasicConfig\City;
use App\Models\BasicConfig\ItemCategory;
use App\Models\Manage\Admin;
use App\Models\Manage\AdminProfiles;
use App\Models\Sales\PopSaleWebsite;
use App\Models\Sales\SalesChance;
use App\Models\Sales\SalesGroup;
use Illuminate\Database\Eloquent\Model;
use Packages\BasicConfig\Models\PopSaleGroupModel;

trait ModelQueryTrait
{
    /**
     * @var Model
     */
    private $model;
    /**
     * @var null|\Illuminate\Database\Eloquent\Collection
     */
    public $queryResult = null;

    public $queryColumns = null;

    public $hiddenField = [];

    /**
     * @param int $offset
     * @param int $limit
     * @return ModelQueryTrait
     */
    public function forOffsetPage($offset = 0, $limit = 20)
    {
        $this->model = $this->model->forPage($offset, $limit);
        return $this;
    }

    public function forPageAfterId($lastId = 0, $limit = 20, $operator = '>')
    {
        $this->model = $this->model->where('id', $operator, $lastId)
            ->take($limit);
        return $this;
    }

    public function forPageAfterPointedId($condition, $lastId = 0, $limit = 20, $operator = '>')
    {
        $this->model = $this->model->where($condition, $operator, $lastId)
            ->take($limit);
        return $this;
    }

    public function selectRow($row){
        $this->model = $this->model->selectRaw($row);
        return $this;
    }

    public function groupBy(...$column){
        $this->model = $this->model->groupBy($column);
        return $this;
    }

    public function getModel(){
        return $this->model;
    }

    public function toSql(){
        $this->model = $this->model->toSql();
        return $this->model;
    }

    public function distinct($column){
        $this->model = $this->model->distinct($column);
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function get($columns = ['*'])
    {
        $this->queryColumns = $columns;
        $this->queryResult = $this->model->get($columns);
        return $this;
    }

    /**
     * 获取查询出来的所有数据，返回出来的数据可以继续处理
     * @return \Illuminate\Support\Collection|null
     */
    public function all()
    {
        if ($this->queryResult) {
            if (!empty($this->hiddenField)) {
                return $this->queryResult->makeHidden($this->hiddenField)->toBase();
            } else {
                return $this->queryResult->toBase();
            }
        }
        return null;
    }


    public function orderById($type = 'desc')
    {
        $this->model = $this->model->orderBy('id', $type);
        return $this;
    }


    public function count($columns = '*'): int
    {
        return $this->model->count($columns);
    }

    public function sum($column): int
    {
        return $this->model->sum($column);
    }

    /**
     * @return $this
     */
    public function repeat()
    {
        $new = clone $this;
        $new->model = clone $this->model;
        return $new;
    }

    /**
     * 在get数据后拼接对应的管理员姓名字段
     * @param string $withAdminIdField
     * @param string $outAdminNameField
     * @param string $defaultName
     * @return $this
     */
    public function withAdminName($withAdminIdField, $outAdminNameField, $defaultName = '')
    {
        if (!$this->checkColumnExists($withAdminIdField)) {
            $adminResults = $this->repeat()->get([$withAdminIdField])->all();
        } else {
            $adminResults = $this->queryResult->toBase();
        }
        $adminIds = [];
        if (strpos($withAdminIdField, '.') !== false) {
            $fieldArr = explode('.', $withAdminIdField);
            $withAdminIdField = array_pop($fieldArr);
        }
        foreach ($adminResults as $item) {
            if (isset($item->$withAdminIdField)) {
                $adminIds[] = $item->$withAdminIdField;
            }
        }

        if ($adminIds) {
            $adminIds = array_unique($adminIds);
            //准备管理员信息数据
            $admins = AdminProfiles::whereIn('user_id', $adminIds)->get(['real_name', 'user_id']);
            $admins = $admins->keyBy('user_id')->all();
            $this->queryResult->map(function (Model $row) use ($admins, $withAdminIdField, $outAdminNameField, $defaultName) {
                $row->$outAdminNameField = $admins[$row->$withAdminIdField]->real_name ?? $defaultName;
                return $row->toArray();
            });
        }
        return $this;
    }

    /**
     * @param $withSalesChanceIdField
     * @param $outSalesChanceDeadlineField
     * @return $this
     */
    public function withSalesChanceDeadline($withSalesChanceIdField, $outSalesChanceDeadlineField)
    {
        if (!$this->checkColumnExists($withSalesChanceIdField)) {
            $salesChanceResults = $this->repeat()->get([$withSalesChanceIdField])->all();
        } else {
            $salesChanceResults = $this->queryResult->toBase();
        }
        $salesChanceIds = [];
        foreach ($salesChanceResults as $chance) {
            if (isset($chance->$withSalesChanceIdField)) {
                $salesChanceIds[] = $chance->$withSalesChanceIdField;
            }
        }
        if ($salesChanceIds) {
            $salesChanceIds = array_unique($salesChanceIds);
            $salesChances = SalesChance::whereIn('id', $salesChanceIds)->get(['id', 'deadline']);
            $salesChances = $salesChances->keyBy('id')->all();
            $this->queryResult->map(function (Model $row) use ($salesChances, $withSalesChanceIdField, $outSalesChanceDeadlineField) {
                $row->$outSalesChanceDeadlineField = $salesChances[$row->$withSalesChanceIdField]->deadline ?? null;
                return $row->toArray();
            });

        }
        return $this;
    }

    /**
     * 匹配组名
     * @param $withSalesGroupIdField
     * @param $outSalesGroupNameField
     * @return $this
     */
    public function withSalesGroupName($withSalesGroupIdField, $outSalesGroupNameField)
    {
        if (!$this->checkColumnExists($withSalesGroupIdField)) {
            $result = $this->repeat()->get([$withSalesGroupIdField])->all();
        } else {
            $result = $this->queryResult->toBase();
        }
        if (strpos($withSalesGroupIdField, '.') !== false) {
            $fieldArr = explode('.', $withSalesGroupIdField);
            $withSalesGroupIdField = array_pop($fieldArr);
        }
        $salesGroupIds = [];
        foreach ($result as $item) {
            if (isset($item->$withSalesGroupIdField)) {
                $salesGroupIds[] = $item->$withSalesGroupIdField;
            }
        }
        if ($salesGroupIds) {
            $salesGroupIds = array_unique($salesGroupIds);
            $salesGroups = SalesGroup::withTrashed()->whereIn('id', $salesGroupIds)->get(['id', 'name']);
            $salesGroups = $salesGroups->keyBy('id')->all();
            $this->queryResult->map(function (Model $row) use ($salesGroups, $withSalesGroupIdField, $outSalesGroupNameField) {
                $row->$outSalesGroupNameField = $salesGroups[$row->$withSalesGroupIdField]->name ?? null;
                return $row->toArray();
            });

        }
        return $this;
    }

    /**
     * 匹配分校名
     * @param $withBranchSchoolIdField
     * @param $outBranchSchoolNameField
     * @return $this
     */
    public function withBranchSchoolName($withBranchSchoolIdField, $outBranchSchoolNameField)
    {
        if (!$this->checkColumnExists($withBranchSchoolIdField)) {
            $result = $this->repeat()->get([$withBranchSchoolIdField])->all();
        } else {
            $result = $this->queryResult->toBase();
        }
        $branchSchoolIds = [];
        foreach ($result as $item) {
            if (isset($item->$withBranchSchoolIdField)) {
                $branchSchoolIds[] = $item->$withBranchSchoolIdField;
            }
        }
        if ($branchSchoolIds) {
            $branchSchoolIds = array_unique($branchSchoolIds);
            $branchSchools = BranchSchool::withTrashed()->whereIn('id', $branchSchoolIds)->get(['id', 'name']);
            $branchSchools = $branchSchools->keyBy('id')->all();
            $this->queryResult->map(function (Model $row) use ($branchSchools, $withBranchSchoolIdField, $outBranchSchoolNameField) {
                $row->$outBranchSchoolNameField = $branchSchools[$row->$withBranchSchoolIdField]->name ?? null;
                return $row->toArray();
            });

        }
        return $this;
    }

    /**
     * 匹配地区名
     * @param $withCityIdField
     * @param $outCityNameField
     * @return $this
     */
    public function withCityName($withCityIdField, $outCityNameField)
    {
        if (!$this->checkColumnExists($withCityIdField)) {
            $result = $this->repeat()->get([$withCityIdField])->all();
        } else {
            $result = $this->queryResult->toBase();
        }
        $cityIds = [];
        foreach ($result as $item) {
            if (isset($item->$withCityIdField)) {
                $cityIds[] = $item->$withCityIdField;
            }
        }
        if ($cityIds) {
            $cityIds = array_unique($cityIds);
            $city = City::whereIn('id', $cityIds)->get(['id', 'name']);
            $city = $city->keyBy('id')->all();
            $this->queryResult->map(function (Model $row) use ($city, $withCityIdField, $outCityNameField) {
                $row->$outCityNameField = $city[$row->$withCityIdField]->name ?? null;
                return $row->toArray();
            });

        }
        return $this;
    }

    /**
     * 匹配项目名
     * @param $withCategoryIdField
     * @param $outCategoryNameField
     * @return $this
     */
    public function withCategoryName($withCategoryIdField, $outCategoryNameField)
    {
        if (!$this->checkColumnExists($withCategoryIdField)) {
            $result = $this->repeat()->get([$withCategoryIdField])->all();
        } else {
            $result = $this->queryResult->toBase();
        }

        $categoryIds = [];
        foreach ($result as $item) {
            if (isset($item->$withCategoryIdField)) {
                $categoryIds[] = $item->$withCategoryIdField;
            }
        }

        if ($categoryIds) {
            $categoryIds = array_unique($categoryIds);
            $categorys = ItemCategory::whereIn('id', $categoryIds)->get(['id', 'name']);
            $categorys = $categorys->keyBy('id')->all();
            $this->queryResult->map(function (Model $row) use ($categorys, $withCategoryIdField, $outCategoryNameField) {
                $row->$outCategoryNameField = $categorys[$row->$withCategoryIdField]->name ?? null;
                return $row->toArray();
            });

        }
        return $this;
    }

    /**
     * 匹配推广组
     * @param $withCategoryIdField
     * @param $outCategoryNameField
     * @return $this
     */
    public function withPopSaleGroupName($withPopSaleGroupIdField, $outPopSaleGroupNameField)
    {
        if (!$this->checkColumnExists($withPopSaleGroupIdField)) {
            $result = $this->repeat()->get([$withPopSaleGroupIdField])->all();
        } else {
            $result = $this->queryResult->toBase();
        }

        $popSaleGroupIds = [];
        foreach ($result as $item) {
            if (isset($item->$withPopSaleGroupIdField)) {
                $popSaleGroupIds[] = $item->$withPopSaleGroupIdField;
            }
        }

        if ($popSaleGroupIds) {
            $popSaleGroupIds = array_unique($popSaleGroupIds);
            $popSaleGroups = PopSaleGroupModel::whereIn('id', $popSaleGroupIds)->get(['id', 'name']);
            $popSaleGroups = $popSaleGroups->keyBy('id')->all();
            $this->queryResult->map(function (Model $row) use ($popSaleGroups, $withPopSaleGroupIdField, $outPopSaleGroupNameField) {
                $row->$outPopSaleGroupNameField = $popSaleGroups[$row->$withPopSaleGroupIdField]->name ?? null;
                return $row->toArray();
            });

        }
        return $this;
    }


    /**
     * 匹配来源渠道名称
     * @param $withFromWebsiteIdField
     * @param $outFromWebsiteNameField
     * @return $this
     */
    public function withFromWebsiteName($withFromWebsiteIdField, $outFromWebsiteNameField)
    {
        if (!$this->checkColumnExists($withFromWebsiteIdField)) {
            $result = $this->repeat()->get([$withFromWebsiteIdField])->all();
        } else {
            $result = $this->queryResult->toBase();
        }

        $websiteIds = [];

        foreach ($result as $item) {
            if (isset($item->$withFromWebsiteIdField)) {
                $websiteIds[] = $item->$withFromWebsiteIdField;
            }
        }
        if ($websiteIds) {
            $websiteIds = array_unique($websiteIds);
            $website = PopSaleWebsite::whereIn('id', $websiteIds)->get(['id', 'name']);
            $website = $website->keyBy('id')->all();
            $this->queryResult->map(function (Model $row) use ($website, $withFromWebsiteIdField, $outFromWebsiteNameField) {
                $row->$outFromWebsiteNameField = $website[$row->$withFromWebsiteIdField]->name ?? null;
                return $row->toArray();
            });

        }
        return $this;
    }

    private function checkColumnExists($column)
    {
        if ($this->queryColumns) {
            if (count($this->queryColumns) == 1 && $this->queryColumns[0] == '*') {
                return true;
            }
            foreach ($this->queryColumns as $queryColumn) {
                if ($queryColumn == $column) {
                    return true;
                }
            }
        }
        return false;
    }

    public function orderByCreatedAt()
    {
        $this->model = $this->model->latest();
        return $this;
    }

    /**
     * @param $createdTime
     * @param string $op
     * @return $this
     */
    public function byCreatedAt($createdTime, $op = '=')
    {
        $this->model = $this->model->where('created_at', $op, $createdTime);
        return $this;
    }

    /**
     * 创建时间从几点到几点查询
     * @param $startTime
     * @param $endTime
     * @return $this
     */
    public function byCreatedAtFromStartToEnd($startTime, $endTime)
    {
        if (!empty($startTime) && !empty($endTime))
            $this->model = $this->model->where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime);

        return $this;
    }

    /**
     * 排序方式
     * @param string $field 字段
     * @param string $mode 方式 desc asc
     * @return ModelQueryTrait
     */
    public function orderBy($field, $mode)
    {
        $this->model = $this->model->orderBy($field, $mode);
        return $this;
    }

    /**
     * limit
     * @param int $num 条数
     * @return ModelQueryTrait
     */
    public function limit($num)
    {
        $this->model = $this->model->limit($num);
        return $this;
    }

    /**
     * offset
     * @param int $num 偏移量
     * @return ModelQueryTrait
     */
    public function offset($num)
    {
        $this->model = $this->model->offset($num);
        return $this;
    }

    /**
     * 通过id
     * @param $ids
     * @return $this
     */
    public function byIds(Array $ids)
    {

        $ids = array_unique($ids);
        if (count($ids) == 1) {
            $this->model = $this->model->whereId(array_pop($ids));
        } else {
            $this->model = $this->model->whereIn('id', $ids);
        }
        return $this;
    }

    /**
     * 锁定一个数据
     * @param $id
     * @return Model|\Illuminate\Database\Query\Builder|null
     */
    public function lockForUpdate($id)
    {
        $model = $this->model->newQuery();
        return $model->where('id', $id)->lockForUpdate()->first();
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    public function byColumn($column, $value, $operator = '')
    {
        $this->model = $this->model->where($column, $operator, $value);
        return $this;
    }

    /**
     *
     * [
     * 'column' => 'name',
     * 'value' => '1',
     * 'operator' => '=',
     * ];
     * @param $data
     * @return ModelQueryTrait
     */
    public function byColumns($data)
    {
        if (!empty($data)) {
            foreach ($data as $item) {
                $column = array_key_exists('column', $item) ? $item['column'] : '';
                $value = array_key_exists('value', $item) ? $item['value'] : '';
                $operator = array_key_exists('operator', $item) ? $item['operator'] : '';

                if (empty($column) || empty($value))
                    continue;

                $this->model = $this->model->where($column, $operator, $value);
            }
        }
        return $this;
    }

    public function setHiddenField($hiddenField)
    {
        $this->hiddenField = $hiddenField;
        return $this;
    }

    /**
     *
     * @param $field
     * @param $value
     * @return $this|Model
     */
    public function whereIn($field, $value)
    {
        if (empty($value)) {
            return $this;
        }

        if (is_array($value) && count($value) == 1) {
            $value = array_pop($value);
            $this->model = $this->model->where($field, $value);
            return $this;
        }

        if (is_array($value)) {
            $this->model = $this->model->whereIn($field, $value);
            return $this;
        }

        $this->model = $this->model->where($field, $value);
        return $this;
    }


    /**
     * 简化pluck到返回数组
     * @param $param
     * @param string $key
     * @return array
     */
    public function pluckToArray($param, $key = '')
    {
        if (empty($key)) {
            return $this->model->pluck($param)->toArray();
        } else {
            return $this->model->pluck($param, $key)->toArray();
        }
    }

}