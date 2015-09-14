<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/9/6
 * Time: 11:05
 */

namespace iit\api\wechat;


use iit\api\Wechat;
use yii\base\Behavior as BaseBehavior;

class Behavior extends BaseBehavior
{
    /**
     * @var Wechat
     */
    public $owner;
}