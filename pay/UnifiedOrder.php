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

    protected function sendInternal()
    {
        $this->setSign();
        $result = $this->http('', 'post', Common::arrayToXml($this->getSendData()));
        return $result;
    }

    public function setDeviceInfo($deviceId)
    {
        return $this->setData('device_info', $deviceId);
    }

    public function setBody($body)
    {
        return $this->setData('body', $body);
    }

    public function setDetail($detail)
    {
        return $this->setData('detail', $detail);
    }

    public function setAttach($attach)
    {
        return $this->setData('attach', $attach);
    }

    public function setOutTradeNo($outTradeNo)
    {
        return $this->setData('out_trade_no', $outTradeNo);
    }

    public function setFeeType($feeType)
    {
        return $this->setData('fee_type', $feeType);
    }

    public function setTotalFee($totalFee)
    {
        return $this->setData('total_fee', $totalFee);
    }

    public function setSpbillCreateIp($spbillCreateIp)
    {
        return $this->setData('spbill_create_ip', $spbillCreateIp);
    }

    public function setTimeStart($timeStart)
    {
        return $this->setData('time_start', $timeStart);
    }

    public function setTimeExpire($timeExpire)
    {
        return $this->setData('time_expire', $timeExpire);
    }

    public function setGoodsTag($goodsTag)
    {
        return $this->setData('goods_tag', $goodsTag);
    }

    public function setNotifyUrl($notifyUrl)
    {
        return $this->setData('notify_url', $notifyUrl);
    }

    public function setTradeType($tradeType)
    {
        return $this->setData('trade_type', $tradeType);
    }

    public function setProductId($productId)
    {
        return $this->setData('product_id', $productId);
    }

    public function setLimitPay($limitPay)
    {
        return $this->setData('limit_pay', $limitPay);
    }

    public function setOpenid($openid)
    {
        return $this->setData('openid', $openid);
    }
}