<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 2014/10/6
 * Time: 17:02
 */

namespace iit\wechat;


class QRCode
{

    const CREATE_TICKET_URL = 'create_tick';
    const SHOW_CODE_URL = 'show_qrcode';
    const TEMP_TICKET = '1';
    const LIMIT_TICKET = '2';

    public function createTempTicket($sceneId, $expire = 1800)
    {
        if ($this->checkSceneId($sceneId, self::TEMP_TICKET)) {
            $result = Wechat::httpPost(self::CREATE_TICKET_URL, Wechat::jsonEncode([
                'expire_seconds' => $expire,
                'action_name' => 'QR_SCENE',
                'action_info' => [
                    'scene' => [
                        'scene_id' => $sceneId
                    ]
                ]
            ]));
            return isset($result['errcode']) ? false : $result['ticket'];
        } else {
            return false;
        }
    }

    public function createTicket($sceneId)
    {
        if ($this->checkSceneId($sceneId, self::LIMIT_TICKET)) {
            $result = Wechat::httpPost(self::CREATE_TICKET_URL, Wechat::jsonEncode([
                'action_name' => 'QR_LIMIT_SCENE',
                'action_info' => [
                    'scene' => [
                        'scene_id' => $sceneId
                    ]
                ]
            ]));
            return isset($result['errcode']) ? false : $result['ticket'];
        } else {
            return false;
        }
    }

    public function checkSceneId($sceneId, $type)
    {
        if (is_numeric($sceneId)) {
            if ($type === self::TEMP_TICKET) {
                if (strlen($sceneId) == 32 && $sceneId != 0) {
                    return true;
                }
            } elseif ($type === self::LIMIT_TICKET) {
                if ($sceneId >= 1 && $sceneId <= 100000) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getTicketUrl($ticketId)
    {
        return Url::get(self::SHOW_CODE_URL) . '?' . http_build_query([
            'ticket' => urlencode($ticketId)
        ]);
    }
}