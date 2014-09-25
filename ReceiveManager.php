<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-2
 * Time: 上午11:20
 */

namespace iit\wechat;


class ReceiveManager extends BaseWechatManager
{

    private $_receiveObj;

    /**
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return bool
     */

    public function signature($signature, $timestamp, $nonce)
    {
        $tmpArr = [$this->token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        return sha1(implode($tmpArr)) == $signature ? true : false;
    }

    /**
     * @return mixed
     */

    public function getReceiveObj()
    {
        return $this->_receiveObj;
    }

    /**
     * @return bool|string
     */

    public function getOpenid()
    {
        return isset($this->getReceiveObj()->FromUserName) ? (string)$this->getReceiveObj()->FromUserName : false;
    }

    /**
     * @return bool|string
     */

    public function getWechatid()
    {
        return isset($this->getReceiveObj()->ToUserName) ? (string)$this->getReceiveObj()->ToUserName : false;
    }

    /**
     * @param $receiveObj
     * @return \iit\wechat\ReceiveManager $this
     */

    public function setReceiveObj($receiveObj)
    {
        $this->_receiveObj = $receiveObj;
        return $this;
    }

    /**
     * @return string
     */

    public function action()
    {
        return $this->getWechat()->getResponseManager()->sendText(\Yii::t('wechat_component', '功能尚未开放'));
    }

} 