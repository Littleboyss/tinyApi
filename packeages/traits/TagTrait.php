<?php
/**
 * Created by PhpStorm.
 * User: json
 * Date: 19-1-11
 * Time: 下午6:04
 */

namespace Packages\Traits;


use Carbon\Carbon;
use Packages\BasicConfig\Models\TagModel;
use Packages\SalesCenter\Models\SalesChanceModel;

trait TagTrait
{
    public function getSalesTags($allTags,$salesTags,$isSaveRecord = false,$isConsultLog = false,$salesChanceIntentions = [])
    {
        if(empty($salesChanceIntentions) && !empty($salesTags))
        {
            $salesChanceIds = $salesTags->pluck('sales_chance_id')->toArray();
            $salesChanceIds = array_unique($salesChanceIds);
            if(count($salesChanceIds) == 1){
                $salesChanceIntentions = SalesChanceModel::where('id',$salesChanceIds[0])->pluck('intention_rate','id')->toArray();
            }else{
                $salesChanceIntentions = SalesChanceModel::whereIn('id',$salesChanceIds)->pluck('intention_rate','id')->toArray();
            }
        }
        $tags = [];
        if(!empty($salesTags)){
            $salesTags = $salesTags->forPage(1,15);
        }
        /**
         * 标签颜色 0 未选择  1 已选择但是过期 2 已选择并且未过期
         */
        $allTags->groupBy('group_name')->map(function($groups,$groupName) use(&$tags,$salesTags,$isSaveRecord,$isConsultLog,$salesChanceIntentions){
            $data['group_name'] = $groupName;
            $group = 0;
            $validTime = 0;
            $type = 0;
            $hasSaveRecord = 0;
            $listShow = 0;
            $isNeed = 0;
            $tag = $groups->map(function($tags) use(&$group,&$validTime,&$type,&$hasSaveRecord,$salesTags,$isSaveRecord,&$listShow,$isConsultLog,&$isNeed,$salesChanceIntentions){
                $group = $tags->group;
                $validTime = $tags->valid_time;
                $type = $tags->type;
                $hasSaveRecord = $tags->has_save_record;
                $listShow = $tags->list_show;
                $isNeed = $tags->is_need;
                $tag['id'] = $tags->id;
                $tag['name'] = $tags->name;
                $tag['colour'] = 0;
                $tag['sales_consult_log_id'] = 0;
                $tag['sales_chance_id'] = 0;
                foreach ($salesTags as $salesTag){
                    if(!$isConsultLog){
                       if($salesTag->status == 2){
                           if($salesTag->tag_id == $tags->id && empty($tags->valid_time)){
                               $tag['colour'] = 2;
                           }
                           if($salesTag->tag_id == $tags->id && !empty($tags->valid_time)){
                               $nowTime =  Carbon::now();
                               $validAt =  Carbon::parse($salesTag->valid_at)->addHour($tags->valid_time);//标签有效时间
                               $tag['colour'] = $nowTime > $validAt ? 1 : 2;
                           }
                           if(!$isSaveRecord && !$hasSaveRecord){
                               $tag['colour'] = 0;
                           }

                           if($salesTag->tag_id == $tags->id){
                               $tag['sales_consult_log_id'] = $salesTag->sales_consult_log_id;
                           }
                       }
                    }else{
                        if($salesTag->tag_id == $tags->id && empty($tags->valid_time)){
                            $tag['colour'] = 2;
                        }
                        if($salesTag->tag_id == $tags->id && !empty($tags->valid_time)){
                            $nowTime =  Carbon::now();
                            $validAt =  Carbon::parse($salesTag->valid_at)->addHour($tags->valid_time);//标签有效时间
                            $tag['colour'] = $nowTime > $validAt ? 1 : 2;
                        }
                        if(!$isSaveRecord && !$hasSaveRecord){
                            $tag['colour'] = 0;
                        }

                        if($salesTag->tag_id == $tags->id){
                            $tag['sales_consult_log_id'] = $salesTag->sales_consult_log_id;
                        }
                    }
                    $tag['sales_chance_id'] = $salesTag->sales_chance_id;
                }
                return $tag;
            });
            $data['group'] = $group;
            $data['valid_time'] = $validTime;
            $data['type'] = $type;
            $data['list_show'] = $listShow;
            $data['has_save_record'] = $hasSaveRecord;
            $data['is_need'] = $isNeed;
            if($type == TagModel::TYPE['star']){
                $star = 0;
                $selectStar = 0;
                $tag = $tag->sortBy('name');

                if($isConsultLog){
                    $tag->values()->all();
                    foreach ($tag as $item){
                        if($item['colour'] == 2){
                            $selectStar = $item['name'];
                        }
                    }
                }else{
                    $salesChanceId = $tag->first();
                    $salesChanceId = $salesChanceId['sales_chance_id'] ?? 0;
                    $selectStar = array_key_exists($salesChanceId,$salesChanceIntentions) ? $salesChanceIntentions[$salesChanceId] : 0;
                }

                $star = $tag->last();
                $star = (int)$star['name'] ?? 0;
                $stars['select'] = $selectStar;
                $stars['star'] = $star;
                $data['tags'] = $stars;
            }else{
                $data['tags'] = $tag;
                //dd($tag);
            }

            $tags[] = $data;
        });

        return $tags;
    }

    public function getSalesConsultLogTags($allTag,$salesConsultLogTags,$consultLogId)
    {
        $consultLogTags = [];
        $star = $allTag->where('type','=',TagModel::TYPE['star'])->pluck('name')->toArray();
        $star = !empty($star) ? max($star) : 0;
        foreach ($salesConsultLogTags as $salesTag){
            $tagArray = [];
            $starArray = [];    //Intention Evaluation
            foreach ($allTag as $tags){
                if($salesTag->tag_id == $tags->id && $tags->list_show == 1){
                    if($tags->type != TagModel::TYPE['star'])
                    {
                        $tagArray['id'] = $tags->id;
                        $tagArray['name'] = $tags->name;
                        $tagArray['select'] = 0;
                        $tagArray['star'] = 0;
                        $tagArray['type'] = $tags->type;
                        $tagArray['sales_consult_log_id'] = $salesTag->sales_consult_log_id;
                        $tagArray['colour'] = 0;
                        if(!empty($tags->valid_time)){
                            $nowTime =  Carbon::now();
                            $validAt =  Carbon::parse($salesTag->valid_at)->addHour($tags->valid_time);//标签有效时间
                            $tagArray['colour'] = $nowTime > $validAt ? 1 : 2;
                        }
                    }else{
                        $starArray['id'] = $tags->id;
                        $starArray['name'] = $tags->name;
                        $starArray['select'] = (int)$tags->name;
                        $starArray['star'] = (int)$star;
                        $starArray['type'] = $tags->type;
                        $starArray['sales_consult_log_id'] = $salesTag->sales_consult_log_id;
                        $starArray['colour'] = 0;
                        if(!empty($tags->valid_time)){
                            $nowTime =  Carbon::now();
                            $validAt =  Carbon::parse($salesTag->valid_at)->addHour($tags->valid_time);//标签有效时间
                            $starArray['colour'] = $nowTime > $validAt ? 1 : 2;
                        }

                    }
                }
            }
            if($consultLogId == $salesTag->sales_consult_log_id && !empty($tagArray)){
                $allTags[] = $tagArray;
                $consultLogTags['tags'] = $allTags;
            }
            if($consultLogId == $salesTag->sales_consult_log_id && !empty($starArray)){
                $allStar[] = $starArray;
                $consultLogTags['star'] = $allStar;
            }
        }

        return $consultLogTags;
    }
}