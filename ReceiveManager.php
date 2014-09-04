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

    public function getReceiveObj()
    {
        return $this->_receiveObj;
    }

    public function getOpenid()
    {
        return isset($this->getReceiveObj()->FromUserName) ? (string)$this->getReceiveObj()->FromUserName : false;
    }

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

    public function action()
    {
        return $this->getWechat()->getResponseManager()->sendText(\Yii::t('wechat_component', '功能尚未开放'));
    }

} 