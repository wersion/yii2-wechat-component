<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/8
 * Time: 14:42
 */

namespace iit\api\wechat;


use Yii;

class BindJsPay extends Behavior
{
    public function bindJsPay($prepayId)
    {
        $data = [
            'appId' => $this->owner->appID,
            'timeStamp' => time(),
            'nonceStr' => Yii::$app->security->generateRandomString(),
            'package' => 'prepay_id=' . $prepayId,
            'signType' => 'MD5',
        ];
        $data['paySign'] = $this->owner->paySign($data);
        return $data;
    }
}