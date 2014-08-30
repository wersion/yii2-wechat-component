<?php

namespace iit\wechat\controllers;

use iit\wechat\Article;
use iit\wechat\News;
use yii\web\Controller;
use yii\web\HttpException;

class DefaultController extends Controller
{
    /**
     * 关闭csrf验证
     */
    public $enableCsrfValidation = false;

    public function actionIndex($signature, $timestamp, $nonce, $echostr = null)
    {
        var_dump(\Yii::$app->wechat->getBaseOAuth()->getCode());
        exit;
        if (isset(\Yii::$app->components['wechat'])) {
            if (\Yii::$app->wechat->signature($signature, $timestamp, $nonce)) {
                if ($echostr === null) {
                    $receive = file_get_contents('php://input');
                    if (empty($receive)) {
                        throw new HttpException("Error Requset Post");
                    } else {
                        $receiveObj = simplexml_load_string($receive, 'SimpleXMLElement', LIBXML_NOCDATA);
                        return \Yii::$app->wechat->receive($receiveObj);
                    }
                } else {
                    return $echostr;
                }
            } else {
                throw new HttpException("Error Requset");
            }
        } else {
            throw new HttpException("Not The Wechat Component");
        }
    }
}