<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-2
 * Time: 上午11:20
 */

namespace iit\wechat;


use yii\helpers\ArrayHelper;

class Receive
{
    private $_receiveData;

    /**
     * 计算签名并验证是否正确
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return bool
     */

    public function signature($signature, $timestamp, $nonce)
    {
        $tmpArr = [Wechat::$component->token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        return sha1(implode($tmpArr)) == $signature ? true : false;
    }

    /**
     * 获取微信服务器发送过来的信息并转换成数组
     * @param null $key
     * @return array|bool
     */

    public function getData($key = null)
    {
        if ($this->_receiveData === null) {
            $data = file_get_contents('php://input');
            if (empty($data)) {
                return false;
            }
            $this->_receiveData = ArrayHelper::toArray(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA));
        }
        return $key === null ? $this->_receiveData : $this->_receiveData[$key];
    }

    /**
     * 获取发送信息的OPENID
     * @return array|bool
     */

    public function getOpenid()
    {
        return $this->getData('FromUserName');
    }

    /**
     * 获取接收信息的微信ID
     * @return array|bool
     */

    public function getWechatid()
    {
        return $this->getData('ToUserName');
    }

} 