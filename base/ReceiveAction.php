<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/7/24
 * Time: 9:11
 */

namespace iit\api\wechat;

use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\di\Instance;

class ReceiveAction extends Action
{
    /**
     *
     * @var Common
     */
    public $wechat = 'wechat';
    const EVENT_RECEIVE_CHECK = 'event_receive_check';
    const EVENT_RECEIVE_TEXT = 'event_receive_text';

    public function init()
    {
        parent::init();
        if ($this->wechat !== null) {
            $this->wechat = Instance::ensure($this->wechat, Common::className());
        } else {
            throw new InvalidConfigException("Cache must be turned on");
        }
    }

    public function run($signature, $timestamp, $nonce, $echostr = null, $encrypt_type = null, $msg_signature = null)
    {

    }
}