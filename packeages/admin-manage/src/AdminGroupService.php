<?php


namespace Packages\AdminManage;

use App\Models\Sales\SalesGroup;
use Packages\SalesCenter\Models\AdminSalesGroupModel;
use Packages\SalesCenter\Models\SalesGroupModel;

class AdminGroupService
{
    /**
     * 根据groupId创建当前groupId子级组索引数据
     * @param $groupId
     * @param bool $useCache 是否使用内部缓存
     * @return string
     */
    public function queryByGroupSunIndex($groupId, $useCache = false): string
    {
        $parents = $this->queryByGroupSun($groupId);
        // 拼接出index
        return implode(',', $parents) . ',';
    }

    /**
     * 直接获取某个用户下归属所有组的索引
     * @param $adminId
     * @return array
     */
    public function queryByAdminOwnerGroupSun($adminId): array
    {
        // 生成一个group tree
        $ownerGroups = AdminSalesGroupModel::whereAdminId($adminId)->whereIsOwner(true)->pluck('group_id')->toArray();
        $groups = SalesGroup::whereIn('id', $ownerGroups)->get();
        // 内存快速生成index
        $index = [];
        foreach ($groups as $group) {
            $index[$group->level][] = $this->queryByGroupSunIndex($group->id, true);
        }
        ksort($index);
        // 剔除包含以及重复的
        $maxLevel = max(array_keys($index));
        foreach ($index as $level => $idx) {
            foreach ($idx as $i) {
                for ($ii = $level + 1; $ii <= $maxLevel; $ii++) {
                    if (isset($index[$ii])) {
                        foreach ($index[$ii] as $k => $cii) {
                            if (strstr($cii, $i)) {
                                unset($index[$ii][$k]);
                            }
                        }
                    }
                }
            }
        }
        $search = [];
        foreach ($index as $arr) {
            $search = array_merge($search, $arr);
        }
        return $search;
    }

    public function queryByGroupSun($groupId): ?array
    {
        static $groups;
        if (!$groups) {
            $groups = SalesGroupModel::withTrashed()->get()->keyBy('id');
        }
        $parents = [];
        // 获取父级组
        $reserve = $groupId;
        $parentGroups = collect();
        $parentId = 0;
        do {
            if (isset($groups[$reserve])) {
                $group = $groups[$reserve];
                $parentId = $group->parent_id ?? 0;
                $reserve = $parentId;
                $parentGroups[] = ['id' => $parentId, 'level' => $group->level];
            }
        } while ($parentId !== 0);
        // 排个序
        $sort = $parentGroups->sortBy('level')->all();
        foreach ($sort as $item) {
            $parents[] = $item['id'];
        }
        // 最后还需要添加自己的数据
        $parents[] = $groupId;
        return $parents;
    }
}