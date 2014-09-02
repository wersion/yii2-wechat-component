<?php

namespace iit\wechat;

use yii\base\InvalidParamException;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'iit\wechat\controllers';

    public $wechat;

    public function init()
    {
        parent::init();
    }

}