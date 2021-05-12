<?php
/**
 * Created by PhpStorm.
 * User: json
 * Date: 19-3-29
 * Time: 下午2:24
 */

namespace Packages\Traits;
use App\Models\Apply\Attachment;


trait OssFileNameTrait
{
    public function getOssFileName($filename)
    {
        return config('aliyun.oss_host').'/'.$filename;
    }

    /**
     * 给目标对象添加filename字段
     *
     * @param array $attachmentIds
     * @param object $attachment 目标对象
     * @return void
     */
    public function getFileNameByIds($attachmentIds,$attachment){
        $filenames = Attachment::whereIn('id', $attachmentIds)->pluck('filename', 'id');
        foreach ($attachment as &$value) {
            if (isset($filenames[$value->attachment_id])) {
                $value->filename = $this->getOssFileName($filenames[$value->attachment_id]);
            } else {
                $value->filename = '';
            }
        }
    }
}