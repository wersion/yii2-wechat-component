<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/6
 * Time: 10:37
 */

namespace iit\api\wechat;

class AccessToken extends Behavior
{
    const CACHE_KEY = 'wechat_access_token';

    public function getAccessToken()
    {
        if (!$accessToken = $this->owner->cache->get($this->getCacheKey())) {
            $result = $this->owner->http('https://api.weixin.qq.com/cgi-bin/token', [
                'grant_type' => 'client_credential',
                'appid' => $this->owner->appID,
                'secret' => $this->owner->appSecret,
            ]);
            if ($result) {
                $result = json_decode($result, true);
                $this->owner->cache->set($this->getCacheKey(), $result['access_token'], $result['expires_in']);
                $accessToken = $result['access_token'];
            } else {
                $accessToken = null;
            }
        }
        return $accessToken;
    }

    public function getCacheKey()
    {
        return self::CACHE_KEY . $this->owner->appID;
    }
}