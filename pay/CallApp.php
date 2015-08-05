<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/8/4
 * Time: 15:03
 */

namespace iit\api\wechat\pay;

use iit\api\wechat\Base;
use Yii;

class CallApp extends Base
{
    public function init()
    {
        parent::init();
        $this->setPackage()->setTimestamp()->setNonceStr();
    }

    protected function sendInternal()
    {
        return $this->getSendData();
    }

    /**
     * @return $this
     */


    public function setNonceStr()
    {
        return $this->setData('noncestr', Yii::$app->security->generateRandomString());
    }

    /**
     * @param $partnerId
     * @return $this
     */

    public function setPartnerId($partnerId)
    {
        return $this->setData('partnerid', $partnerId);
    }

    /**
     * @param $prepayId
     * @return $this
     */

    public function setPrepayId($prepayId)
    {
        return $this->setData('prepayid', $prepayId);
    }

    /**
     * @return $this
     */

    public function setPackage()
    {
        return $this->setData('package', 'Sign=WXPay');
    }

    /**
     * @return $this
     */

    public function setTimestamp()
    {
        return $this->setData('timestamp', time());
    }
}