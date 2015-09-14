<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/7/23
 * Time: 22:25
 */

namespace iit\api\wechat;

use Yii;

class PayUnifiedOrder extends Behavior
{
    const ORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    private $_data = [];

    public function unifiedOrder()
    {
        $this->_data['appid'] = $this->owner->appID;
        $this->_data['mch_id'] = $this->owner->merchantID;
        $this->_data['nonce_str'] = Yii::$app->security->generateRandomString();
        return $this;
    }

    public function setProductId($value)
    {
        $this->_data['product_id'] = $value;
        return $this;
    }

    public function setTradeType($value)
    {
        $this->_data['trade_type'] = $value;
        return $this;
    }

    public function setTotalFee($value)
    {
        $this->_data['total_fee'] = $value * 100;
        return $this;
    }

    public function setOrderNo($value)
    {
        $this->_data['out_trade_no'] = $value;
        return $this;
    }

    public function setBody($value)
    {
        $this->_data['body'] = $value;
        return $this;
    }

    public function setAttach($value)
    {
        $this->_data['attach'] = $value;
        return $this;
    }

    public function setOpenid($value)
    {
        $this->_data['openid'] = $value;
        return $this;
    }

    public function setNotifyUrl($value)
    {
        $this->_data['notify_url'] = $value;
        return $this;
    }

    public function send()
    {
        date_default_timezone_set("Asia/ShangHai");
        $this->_data['spbill_create_ip'] = Yii::$app->request->userIP;
        $this->_data['time_start'] = date('YmdHis');
        $this->_data['time_expire'] = date('YmdHis', (time() + 300));
        $this->_data['sign'] = $this->owner->paySign($this->_data);
        $result = $this->owner->http(self::ORDER_URL, $this->owner->arrayToXml($this->_data), 'raw');
        if ($result && $this->owner->checkPaySign($result)) {
            $result = $this->owner->xmlToArray($result);
            if ($result['result_code'] == 'SUCCESS') {
                return $result['prepay_id'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}