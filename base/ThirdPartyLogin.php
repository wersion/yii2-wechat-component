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
    const URL = 'https://open.weixin.qq.com/connect/qrconnect?';

    public function getThirdPartyOpenId()
    {
        if ($openid = Yii::$app->session->get(self::OPENID_SESSION_KEY)) {
            return $openid;
        } else {
            $code = Yii::$app->request->get('code');
            if (empty($code)) {
                $queryData = [
                    'appid' => $this->owner->appID,
                    'redirect_uri' => Url::current([], true),
                    'response_type' => 'code',
                    'scope' => 'SCOP',
                    'state' => 'STATE',
                ];
                Yii::$app->controller->redirect('https://open.weixin.qq.com/connect/qrconnect?' . http_build_query($queryData) . '#wechat_redirect');
            } else {

            }
        }
    }

    public function getThirdPartyUnionId()
    {

    }


}