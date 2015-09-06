<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/6
 * Time: 10:37
 */

namespace iit\api\wechat;

class AccessToken extends WechatBehavior
{
    const CACHE_KEY = 'wechat_access_token';

    public function getAccessToken()
    {
        if (!$accessToken = $this->owner->cache->get(self::CACHE_KEY)) {
            $result = $this->owner->http('https://api.weixin.qq.com/cgi-bin/token', [
                'grant_type' => 'client_credential',
                'appid' => 'wx61b675f58783745f',
                'secret' => 'ab5a99d278d4ac1b2de2068eaf39f9a7'
            ]);
            if ($result) {
                $result = json_decode($result, true);
                $this->owner->cache->set(self::CACHE_KEY, $result['access_token'], $result['expires_in']);
                $accessToken = $result['access_token'];
            } else {
                $accessToken = null;
            }
        }
        return $accessToken;
    }
}