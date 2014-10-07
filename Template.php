<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 2014/10/6
 * Time: 15:06
 */

namespace iit\wechat;


class Template
{

    const SEND_TEMPLATE_URL = 'send_template';

    public function send($openid, $templateId, $url, $topcolor, $data)
    {
        $result = Wechat::httpRaw(self::SEND_TEMPLATE_URL, Wechat::jsonEncode([
            'touser' => $openid,
            'template_id' => $templateId,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $data
        ]));
        return $result['errcode'] == 0 ? $result['msgid'] : false;
    }
} 