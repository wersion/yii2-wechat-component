<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 2014/9/29
 * Time: 22:32
 */

namespace iit\wechat;


class Url
{

    const BASE_URL = 'https://api.weixin.qq.com/cgi-bin';
    const FILE_BASE_URL = 'http://file.api.weixin.qq.com/cgi-bin';

    public static function get($type)
    {
        $urlList = [
            'mediaUpload' => self::FILE_BASE_URL . '/media/upload',
            'serviceMessage' => self::BASE_URL . '/message/custom/send',
            'customService' => self::BASE_URL . '/customservice/getrecord',

        ];
        return isset($urlList[$type]) ?: false;
    }

} 