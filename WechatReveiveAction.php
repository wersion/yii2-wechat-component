<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-2
 * Time: 下午2:09
 */

namespace iit\wechat;


use yii\base\Action;
use yii\base\InvalidParamException;
use yii\web\HttpException;

class WechatReveiveAction extends Action
{
    private $_wechat;

    /**
     * @param Wechat $wechat
     */

    public function setWechat($wechat)
    {
        if (is_object($wechat)) {
            $this->_wechat = $wechat;
        } elseif (is_string($wechat) && \Yii::$app->has($wechat)) {
            $this->_wechat = \Yii::$app->get($wechat);
        } else {
            throw new InvalidParamException('Not Found Wechat Component.');
        }
    }

    /**
     * @return \iit\wechat\Wechat $wechat
     */

    public function getWechat()
    {
        return $this->_wechat;
    }

    public function run($signature, $timestamp, $nonce, $echostr = null)
    {
        if ($this->getWechat() === null) {
            throw new InvalidParamException('Not Found The Wechat Component');
        } else {
            if ($this->getWechat()->getReceiveManager()->signature($signature, $timestamp, $nonce)) {
                if ($echostr === null) {
                    $receive = file_get_contents('php://input');
                    if (empty($receive)) {
                        throw new HttpException(404);
                    } else {
                        $receiveObj = simplexml_load_string($receive, 'SimpleXMLElement', LIBXML_NOCDATA);
                        return $this->getWechat()->getReceiveManager()->setReceiveObj($receiveObj)->action();
                    }
                } else {
                    return $echostr;
                }
            } else {
                throw new HttpException(404);
            }
        }
    }
} 