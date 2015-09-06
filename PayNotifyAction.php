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

class PayNotifyAction extends Action
{
    /**
     * @var Common
     */
    public $wechat = 'wechat';

    public function run()
    {
        return 'aa';
    }
}