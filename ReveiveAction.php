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

class ReveiveAction extends Action
{

    public function run($signature, $timestamp, $nonce, $echostr = null)
    {
        if (Wechat::$component === null) {
            throw new InvalidParamException('Not Found The Wechat Component');
        } else {
            if (Wechat::$component->getReceiveManager()->signature($signature, $timestamp, $nonce)) {
                if ($echostr === null) {
                    $receive = file_get_contents('php://input');
                    if (empty($receive)) {
                        throw new HttpException(404);
                    } else {
                        $receiveObj = simplexml_load_string($receive, 'SimpleXMLElement', LIBXML_NOCDATA);
                        return Wechat::$component->getReceiveManager()->setReceiveObj($receiveObj)->action();
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