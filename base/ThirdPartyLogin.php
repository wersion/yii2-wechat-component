<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/14
 * Time: 15:46
 */

namespace iit\api\wechat;


use Yii;
use yii\helpers\Url;

class ThirdPartyLogin extends Behavior
{
    const OPENID_SESSION_KEY = 'wechat_openid';
    const UNION_ID_SESSION_KEY = 'wechat_union_id';
    const OAUTH_URL = 'https://open.weixin.qq.com/connect/qrconnect?';
    const USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo';
    private $_info;

    public function getThirdPartyUserInfo()
    {
        if ($this->_info === null) {
            $accessToken = $this->goOAuth();
            $queryData = [
                'access_token' => $accessToken['access_token'],
                'openid' => $accessToken['openid'],
            ];
            if ($result = $this->owner->http(self::USER_INFO_URL, $queryData)) {
                $result = json_decode($result, true);
                if (isset($result['errcode'])) {
                    return false;
                } else {
                    $info = [
                        'id' => empty($result['unionid']) ? $result['openid'] : $result['unionid'],
                        'nickname' => $result['nickname'],
                        'headimgurl' => $result['headimgurl'],
                    ];
                    $this->_info = $info;
                }
            } else {
                return false;
            }
        }
        return $this->_info;
    }

    protected function goOAuth()
    {
        $code = Yii::$app->request->get('code');
        if (empty($code)) {
            $queryData = [
                'appid' => $this->owner->appID,
                'redirect_uri' => Url::current([], true),
                'response_type' => 'code',
                'scope' => 'snsapi_login',
                'state' => Yii::$app->security->generateRandomString(),
            ];
            Yii::$app->controller->redirect('https://open.weixin.qq.com/connect/qrconnect?' . http_build_query($queryData) . '#wechat_redirect');
        } else {
            if ($result = $this->owner->getAccessTokenByCode($code)) {
                return $result;
            } else {
                return false;
            }
        }
    }

}