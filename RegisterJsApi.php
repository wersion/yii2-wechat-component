<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/6
 * Time: 15:26
 */

namespace iit\api\wechat;


use Yii;

class RegisterJsApi extends WechatBehavior
{
    public function registerJsApi()
    {
        $signData = [
            'appId' => $this->owner->appID,
            'timestamp' => time(),
            'nonceStr' => Yii::$app->security->generateRandomString(10),
            'url' => Yii::$app->request->hostInfo,
        ];
        ksort($signData, SORT_STRING);
        $signStr = $this->owner->arrayToPaySignStr($signData);
        $sign = sha1($signStr);
        Yii::$app->controller->view->registerJs("wx.config({debug: true,appId: '" . $signData['appId']
            . "',timestamp: '" . $signData['timestamp']
            . "',nonceStr: '" . $signData['nonceStr']
            . "',signature: '" . $sign . "',jsApiList: []});");
    }
}