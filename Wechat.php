<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-8-28
 * Time: 下午3:56
 */

namespace yii\wechat;

use yii\base\Component;

class Wechat extends Component
{
    const MENU = '';

    public function getAccessToken()
    {

    }

    public function arrayToXml($array)
    {
        $xml = "<xml>";
        foreach ($array as $key => $val) {
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    public static function addTokenToData($data)
    {
        $data['access_token'] = GetAccessToken::get();
        return $data;
    }

    public static function addTokenToUrl($url)
    {
        return $url . "?access_token=" . GetAccessToken::action();
    }


} 