<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-8-28
 * Time: 下午3:56
 */

namespace iit\wechat;

use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;

class Wechat extends Component
{

    const GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';

    const MEDIA_UPLOAD_URL = 'http://file.api.weixin.qq.com/cgi-bin/media/upload';

    public $appid;
    public $appsecret;
    public $token;
    private $_apps;
    private $_accessToken;

    /**
     * @throws \yii\base\InvalidParamException
     */

    public function init()
    {
        parent::init();
        if ($this->appid === null)
            throw new InvalidParamException('The appid has not configure.');
        if ($this->appsecret === null)
            throw new InvalidParamException('The appsecret has not configure.');
        if ($this->token === null)
            throw new InvalidParamException('The token has not configure.');
    }

    /**
     * @param bool $forceUpdate
     * @return bool|mixed|null
     */

    public function getAccessToken($forceUpdate = false)
    {
        if ($this->_accessToken === null || $forceUpdate === true) {
            $cacheKey = sha1($this->appid);
            $cacheToken = false;
            $forceUpdate === false && $cacheToken = \Yii::$app->cache->get($cacheKey);
            if ($cacheToken == false || $forceUpdate == true) {
                $result = $this->httpGet(self::GET_ACCESS_TOKEN_URL, ['appid' => $this->appid, 'secret' => $this->appsecret], false, false);
                if (!isset($result['errcode'])) {
                    $this->_accessToken = $result['access_token'];
                    \Yii::$app->cache->set($cacheKey, $this->_accessToken);
                }
            } else {
                $this->_accessToken = $cacheToken;
            }
        }
        return ($this->_accessToken === null) ? false : $this->_accessToken;
    }

    /**
     * @return \iit\wechat\ReceiveManager $receiveManager
     */

    public function getReceiveManager()
    {
        return $this->getApp('\iit\wechat\ReceiveManager');
    }

    /**
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return bool
     */

    public function signature($signature, $timestamp, $nonce)
    {
        $tmpArr = [$this->token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        return sha1(implode($tmpArr)) == $signature ? true : false;
    }

    /**
     * @return \iit\wechat\BaseOAuth $baseOAuth
     */

    public function getBaseOAuth()
    {
        return $this->getApp('\iit\wechat\BaseOAuth');
    }

    /**
     * @return \iit\wechat\UserInfoOAuth userInfoOAuth
     */

    public function getUserInfoOAuth()
    {
        return $this->getApp('\iit\wechat\UserInfoOAuth');
    }

    /**
     * @return \iit\wechat\UserManager $userManager
     */

    public function getUserManager()
    {
        return $this->getApp('\iit\wechat\UserManager');
    }

    /**
     * @return \iit\wechat\MediaManager mediaManager
     */

    public function getMediaManager()
    {
        return $this->getApp('\iit\wechat\MediaManager');
    }

    /**
     * @return \iit\wechat\MenuManager $menuManager
     */

    public function getMenuManager()
    {
        return $this->getApp('\iit\wechat\MenuManager');
    }

    /**
     * @return \iit\wechat\ResponseManager $responseManager
     */

    public function getResponseManager()
    {
        return $this->getApp('\iit\wechat\ResponseManager');
    }

    /**
     *
     * @param $appName
     * @return mixed
     */

    public function getApp($appName)
    {
        $cacheKey = sha1($appName);
        if (!isset($this->_apps[$cacheKey])) {
            $this->_apps[$cacheKey] = new $appName($this);
        }
        return $this->_apps[$cacheKey];
    }

    /**
     * @param $url
     * @param null $params
     * @param bool $etry
     * @param string $token
     * @return bool|mixed
     */
    public function httpGet($url, $params = null, $token = 'url', $etry = true)
    {
        return $this->parseHttpResult($url, $params, 'get', $token, $etry);
    }

    /**
     * @param $url
     * @param null $params
     * @param string $token
     * @return bool|mixed
     */
    public function httpPost($url, $params = null, $token = 'url', $etry = true)
    {
        return $this->parseHttpResult($url, $params, 'post', $token, $etry);
    }

    /**
     * @param $url
     * @param null $params
     * @param string $token
     * @param bool $etry
     * @return bool|mixed
     */

    public function httpRaw($url, $params = null, $token = 'url', $etry = true)
    {
        return $this->parseHttpResult($url, $params, 'raw', $token, $etry);
    }

    /**
     * @param $url
     * @param $params
     * @param $method
     * @param bool $token
     * @param bool $etry
     * @return bool|mixed
     */
    public function parseHttpResult($url, $params, $method, $token = false, $etry = true)
    {
        $return = $this->http($url, $params, $method, $token);
        $return = json_decode($return, true) ? : $return;
        if (isset($return['errcode']) && $etry === true && $token != false) {
            switch ($return['errcode']) {
                case 40001:
                    $this->getAccessToken(true) && $return = $this->parseHttpResult($url, $params, $method, $token, false);
                    break;
            }
        }
        return $return;
    }

    /**
     * Http协议调用微信接口方法
     * @param $url api地址
     * @param $params 参数
     * @param string $type 提交类型
     * @param string $token 添加AccessToken类型
     * @return bool|mixed
     * @throws \yii\base\InvalidParamException
     */
    public function http($url, $params = null, $type = 'get', $token = false)
    {
        if ($token) {
            if ($token == 'url') {
                $url .= (stripos($url, '?') === false ? '?' : '&') . "access_token=" . $this->getAccessToken();
            } elseif ($token == 'params') {
                $params['access_token'] = $this->getAccessToken();
            } else {
                throw new InvalidParamException("Invalid token type '{$token}' called.");
            }
        }
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