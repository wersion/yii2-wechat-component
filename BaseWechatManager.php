<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-2
 * Time: 上午11:37
 */

namespace iit\wechat;


use yii\base\Object;

class BaseWechatManager extends Object
{
    private $_wechat;

    /**
     * @return \iit\wechat\Wechat $wechat
     */

    public function getWechat()
    {
        return $this->_wechat;
    }

    /**
     * @param Wechat $wechat
     */

    public function setWechat(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }
} 