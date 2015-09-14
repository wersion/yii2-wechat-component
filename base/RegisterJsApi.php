<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/6
 * Time: 15:26
 */

namespace iit\api\wechat;


use Yii;
use yii\helpers\Url;

class RegisterJsApi extends Behavior
{
    public function registerJsApi()
    {
        $signData = [
            'jsapi_ticket' => $this->owner->getJsApiTicket(),
            'timestamp' => time(),
            'noncestr' => Yii::$app->security->generateRandomString(10),
            'url' => Url::current([], true),
        ];
        $apiList = [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone',
            'startRecord',
            'stopRecord',
            'onVoiceRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'onVoicePlayEnd',
            'uploadVoice',
            'downloadVoice',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'translateVoice',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'closeWindow',
            'scanQRCode',
            'chooseWXPay',
            'openProductSpecificView',
            'addCard',
            'chooseCard',
            'openCard',
        ];
        ksort($signData, SORT_STRING);
        $signStr = $this->owner->arrayToPaySignStr($signData);
        $sign = sha1($signStr);
        $jsApiList = implode("','", $apiList);
        $jsApiList = "'" . $jsApiList . "'";
        Yii::$app->controller->view->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.0.0.js');
        Yii::$app->controller->view->registerJs("wx.config({appId: '" . $this->owner->appID
            . "',timestamp: '" . $signData['timestamp']
            . "',nonceStr: '" . $signData['noncestr']
            . "',signature: '" . $sign
            . "',jsApiList: [" . $jsApiList . "]});");
    }
}