<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/14
 * Time: 17:40
 */

namespace iit\api\wechat;


class GetAccessTokenByCode extends Behavior
{
    const URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    public function getAccessTokenByCode($code)
    {
        if ($result = $this->owner->http(self::URL, [
            'appid' => $this->owner->appID,
            'secret' => $this->owner->appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ])
        ) {
            $result = json_decode($result, true);
            return isset($result['errcode']) ? false : $result;
        } else {
            return false;
        }
    }
}