<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午3:30
 */

namespace iit\wechat;


class ServiceManager extends BaseWechatManager
{

    const SERVICE_URL = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';

    public function sendText($openid, $text)
    {
        $result = $this->getWechat()->httpRaw(self::SERVICE_URL, json_encode([
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content' => $text
            ]
        ], JSON_UNESCAPED_UNICODE));
        return $result['errcode'] == 0 ? true : false;
    }

    public function sendImage($openid, $mediaId)
    {
        $result = $this->getWechat()->httpRaw(self::SERVICE_URL, json_encode([
            'touser' => $openid,
            'msgtype' => 'image',
            'image' => [
                'media_id' => $mediaId
            ]
        ], JSON_UNESCAPED_UNICODE));
        return $result['errcode'] == 0 ? true : false;
    }

} 