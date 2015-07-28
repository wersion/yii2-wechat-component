<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/7/25
 * Time: 13:25
 */

namespace iit\api\wechat\pay;

use iit\api\wechat\Common;
use yii\base\Action;

class NotifyAction extends Action
{
    /**
     * @var Common
     */
    public $wechat = 'wechat';
}