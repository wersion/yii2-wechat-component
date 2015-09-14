<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/7
 * Time: 9:20
 */

namespace iit\api\wechat;


use Yii;

class WebOpenId extends Behavior
{
    const SESSION_KEY = 'openid';

    public function getOpenId()
    {
        if (!$openid = Yii::$app->session->get(self::SESSION_KEY)) {
            if ($info = $this->owner->getBaseUserInfo()) {
                $openid = $info['openid'];
                Yii::$app->session->set(self::SESSION_KEY, $openid);
            } else {
                $openid = null;
            }
        }
        return $openid;
    }
}