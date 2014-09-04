<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-4
 * Time: 上午10:57
 */

namespace iit\wechat;


class UploadGearmanJob extends \iit\gearman\BaseJob
{
    public function execute(\GearmanJob $job, $component = null)
    {
        $params = unserialize($job->workload());
        if (isset($params['type']) && isset($params['openid']) && isset($params['file'])) {
            $mediaId = $component->getMediaManager()->uploadMedia($params['file'], $params['type']);
            if ($mediaId) {
                $sendFuntion = 'send' . ucfirst($params['type']);
                $component->getServiceManager()->$sendFuntion($params['openid'], $mediaId);
            } else {
                $component->getServiceManager()->sendText($params['openid'], '对不起，系统无法处理您的请求，请稍候再试');
            }
        }
    }
}