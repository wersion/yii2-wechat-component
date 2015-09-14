<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/6
 * Time: 15:25
 */

namespace iit\api\wechat;


class JsApiTicket extends Behavior
{
    const CACHE_KEY = 'js_api_ticket';

    public function getJsApiTicket()
    {
        if (!$ticket = $this->owner->cache->get($this->getCacheKey())) {
            $result = $this->owner->http('https://api.weixin.qq.com/cgi-bin/ticket/getticket', [
                'access_token' => $this->owner->getAccessToken(),
                'type' => 'jsapi',
            ]);
            $result = json_decode($result, true);
            if ($result && $result['errcode'] == 0) {
                $this->owner->cache->set($this->getCacheKey(), $result['ticket'], $result['expires_in']);
                $ticket = $result['ticket'];
            } else {
                $ticket = null;
            }
        }
        return $ticket;
    }

    protected function getCacheKey()
    {
        return self::CACHE_KEY . $this->owner->appID;
    }
}