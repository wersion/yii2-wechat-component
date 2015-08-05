<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/7/23
 * Time: 22:25
 */

namespace iit\api\wechat\pay;

use iit\api\wechat\Base;
use iit\api\wechat\Common;

class UnifiedOrder extends Base
{
    public function init()
    {
        parent::init();
        $this->setNonceStr();
    }


    protected function sendInternal()
    {
        $result = $this->http('https://api.mch.weixin.qq.com/pay/unifiedorder', Common::arrayToXml($this->getSendData()), 'raw');
        return Common::xmlToArray($result);
    }

    /**
     * @param $deviceId
     * @return $this
     */

    public function setDeviceInfo($deviceId)
    {
        return $this->setData('device_info', $deviceId);
    }

    /**
     * @param $body
     * @return $this
     */

    public function setBody($body)
    {
        return $this->setData('body', $body);
    }

    /**
     * @param $detail
     * @return $this
     */

    public function setDetail($detail)
    {
        return $this->setData('detail', $detail);
    }

    /**
     * @param $attach
     * @return $this
     */

    public function setAttach($attach)
    {
        return $this->setData('attach', $attach);
    }

    /**
     * @param $outTradeNo
     * @return $this
     */

    public function setOutTradeNo($outTradeNo)
    {
        return $this->setData('out_trade_no', $outTradeNo);
    }

    /**
     * @param $feeType
     * @return $this
     */

    public function setFeeType($feeType)
    {
        return $this->setData('fee_type', $feeType);
    }

    /**
     * @param $totalFee
     * @return $this
     */

    public function setTotalFee($totalFee)
    {
        return $this->setData('total_fee', $totalFee);
    }

    /**
     * @param $spbillCreateIp
     * @return $this
     */

    public function setSpbillCreateIp($spbillCreateIp)
    {
        return $this->setData('spbill_create_ip', $spbillCreateIp);
    }

    /**
     * @param $timeStart
     * @return $this
     */

    public function setTimeStart($timeStart)
    {
        return $this->setData('time_start', $timeStart);
    }

    /**
     * @param $timeExpire
     * @return $this
     */

    public function setTimeExpire($timeExpire)
    {
        return $this->setData('time_expire', $timeExpire);
    }

    /**
     * @param $goodsTag
     * @return $this
     */

    public function setGoodsTag($goodsTag)
    {
        return $this->setData('goods_tag', $goodsTag);
    }

    /**
     * @param $notifyUrl
     * @return $this
     */

    public function setNotifyUrl($notifyUrl)
    {
        return $this->setData('notify_url', $notifyUrl);
    }

    /**
     * @param $tradeType
     * @return $this
     */

    public function setTradeType($tradeType)
    {
        return $this->setData('trade_type', $tradeType);
    }

    /**
     * @param $productId
     * @return $this
     */

    public function setProductId($productId)
    {
        return $this->setData('product_id', $productId);
    }

    /**
     * @param $limitPay
     * @return $this
     */

    public function setLimitPay($limitPay)
    {
        return $this->setData('limit_pay', $limitPay);
    }

    /**
     * @param $openid
     * @return $this
     */

    public function setOpenid($openid)
    {
        return $this->setData('openid', $openid);
    }
}