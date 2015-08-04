<?php
/**
 * Created by PhpStorm.
 * User: dggug
 * Date: 2015/7/23
 * Time: 22:32
 */

namespace iit\api\wechat;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Object;
use yii\helpers\Url;

abstract class Base extends Object
{
    protected $_send = [];

    abstract protected function sendInternal();

    /**
     * @return mixed
     */

    public function send()
    {
        $this->setNonceStr();
        $this->setSign();
        return $this->sendInternal();
    }

    /**
     * @return array
     */

    public function getSendData()
    {
        return $this->_send;
    }

    /**
     * @return $this|Base
     */

    public function setSign()
    {
        return $this->setData('sign', Common::$wechat->paySign($this->getSendData()));
    }

    /**
     * @return mixed
     */

    public function getSign()
    {
        return $this->_send['sign'];
    }

    public function setAppid($appid)
    {
        return $this->setData('appid', $appid);
    }

    public function setMchid($mchid)
    {
        return $this->setData('mch_id', $mchid);
    }

    public function setNonceStr()
    {
        $this->setData('nonce_str', Yii::$app->security->generateRandomString());
        return $this;
    }

    protected function setData($k, $v)
    {
        $this->_send[$k] = $v;
        return $this;
    }

    protected function getData($k)
    {
        return isset($this->_send[$k]) ? $this->_send[$k] : null;
    }

    public function http($url, $params = null, $type = 'get')
    {
        $curl = curl_init();
        switch ($type) {
            case 'get':
                is_array($params) && $params = http_build_query($params);
                !empty($params) && $url .= (stripos($url, '?') === false ? '?' : '&') . $params;
                break;
            case 'post':
                curl_setopt($curl, CURLOPT_POST, true);
                if (!is_array($params)) {
                    throw new InvalidParamException("Post data must be an array.");
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case 'raw':
                curl_setopt($curl, CURLOPT_POST, true);
                if (is_array($params)) {
                    throw new InvalidParamException("Post raw data must not be an array.");
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            default:
                throw new InvalidParamException("Invalid http type '{$type}' called.");
        }
        if (stripos($url, "https://") !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($curl);
        $status = curl_getinfo($curl);
        curl_close($curl);
        if (isset($status['http_code']) && intval($status['http_code']) == 200) {
            return $content;
        }
        return false;
    }

}