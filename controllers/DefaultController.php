<?php

namespace iit\wechat\controllers;

use iit\wechat\Article;
use iit\wechat\Menu;
use iit\wechat\News;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\VarDumper;

class DefaultController extends Controller
{
    /**
     * 关闭csrf验证
     */
    public $enableCsrfValidation = false;

    public function actionIndex($signature, $timestamp, $nonce, $echostr = null)
    {
        var_dump(\Yii::$app->wechat->getUserManager()->createGroup('test'));
        exit;
        if (\Yii::$app->wechat != null) {
            if (\Yii::$app->wechat->signature($signature, $timestamp, $nonce)) {
                if ($echostr === null) {
                    $receive = file_get_contents('php://input');
                    if (empty($receive)) {
                        throw new HttpException(404);
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