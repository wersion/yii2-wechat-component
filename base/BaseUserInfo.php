<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/6
 * Time: 21:49
 */

namespace iit\api\wechat;


use Yii;
use yii\helpers\Url;

class BaseUserInfo extends Behavior
{
    const URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    const ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    public function getBaseUserInfo()
    {
        $code = Yii::$app->request->get('code', null);
        if ($code == null) {
            $query = [
                'appid' => $this->owner->appID,
                'redirect_uri' => Url::current([], true),
                'response_type' => 'code',
                'scope' => 'snsapi_base',
                'state' => '',
            ];

            $url = self::URL . http_build_query($query) . '#wechat_redirect';
            Yii::$app->controller->redirect($url);
        } else {
            $result = $this->owner->http(self::ACCESS_TOKEN, [
                'appid' => $this->owner->appID,
                'secret' => $this->owner->appSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]);
            if ($result) {
                return json_decode($result, true);
            } else {
                return null;
            }
        }
    }
}