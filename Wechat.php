<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-8-28
 * Time: 下午3:56
 */

namespace iit\wechat;

use yii\base\InvalidParamException;

/**
 * Class Wechat
 * @package iit\wechat
 *
 * @property \iit\wechat\UserManager $userManager The User Manager
 *
 */
class Wechat
{

    const GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
    /**
     * @var \iit\wechat\Component The Wechat Component
     */
    public static $component;

    /**
     * @var String The Cache AccessToken
     */
    private static $_cache;


    /**
     * @param $url
     * @param null $params
     * @param string $token
     * @param bool $etry
     * @return bool|mixed
     */

    public static function httpRaw($url, $params = null, $token = 'url', $etry = true)
    {
        return self::parseHttpResult($url, $params, 'raw', $token, $etry);
    }

    /**
     * @param $url
     * @param null $params
     * @param bool $etry
     * @param string $token
     * @return bool|mixed
     */
    public static function httpGet($url, $params = null, $token = 'url', $etry = true)
    {
        return self::parseHttpResult($url, $params, 'get', $token, $etry);
    }

    /**
     * @param $url
     * @param null $params
     * @param string $token
     * @return bool|mixed
     */
    public static function httpPost($url, $params = null, $token = 'url', $etry = true)
    {
        return self::parseHttpResult($url, $params, 'post', $token, $etry);
    }

    /**
     * @param $url
     * @param $params
     * @param $method
     * @param bool $token
     * @param bool $etry
     * @return bool|mixed
     */
    public static function parseHttpResult($url, $params, $method, $token = false, $etry = true)
    {
        $return = self::http($url, $params, $method, $token);
        $return = self::jsonDecode($return) ?: $return;
        if (isset($return['errcode']) && $etry === true && $token != false) {
            switch ($return['errcode']) {
                case 40001:
                    self::getAccessToken(true) && $return = self::parseHttpResult($url, $params, $method, $token, false);
                    break;
                case 42001:
                    self::getAccessToken(true) && $return = self::parseHttpResult($url, $params, $method, $token, false);
                    break;
            }
        }
        return $return;
    }

    /**
     * Http协议调用微信接口方法
     * @param String $url
     * @param Array|String $params
     * @param string $type 提交类型
     * @param string $token 添加AccessToken类型
     * @return bool|mixed
     * @throws \yii\base\InvalidParamException
     */
    public static function http($url, $params = null, $type = 'get', $token = false)
    {
        if ($token) {
            if ($token == 'url') {
                $url .= (stripos($url, '?') === false ? '?' : '&') . "access_token=" . self::getAccessToken();
            } elseif ($token == 'params') {
                $params['access_token'] = self::getAccessToken();
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

    /**
     * @param bool $forceUpdate
     * @return bool|mixed|null
     */

    public static function getAccessToken($forceUpdate = false)
    {
        if (self::$_cache['accessToken'] === null || $forceUpdate === true) {
            $cacheKey = sha1(self::$component->appid);
            $cacheToken = false;
            $forceUpdate === false && $cacheToken = self::getCache($cacheKey);
            if ($cacheToken == false || $forceUpdate == true) {
                $result = self::httpGet(self::GET_ACCESS_TOKEN_URL, ['appid' => self::$component->appid, 'secret' => self::$component->appsecret], false, false);
                if (!isset($result['errcode'])) {
                    self::$_cache['accessToken'] = $result['access_token'];
                    self::setCache($cacheKey, self::$_cache['accessToken'], $result['expires_in']);
                }
            } else {
                self::$_cache['accessToken'] = $cacheToken;
            }
        }
        return (self::$_cache['accessToken'] === null) ? false : self::$_cache['accessToken'];
    }


    /**
     * @param $key
     * @param $value
     * @param null $duration
     * @return bool
     */

    public static function setCache($key, $value, $duration = null)
    {
        if (\Yii::$app->cache === null) {
            return false;
        } else {
            return \Yii::$app->cache->set('wechat_' . $key, $value, $duration);
        }
    }

    /**
     * @param $key
     * @return bool|mixed
     */

    public static function getCache($key)
    {
        if (\Yii::$app->cache === null) {
            return false;
        } else {
            return \Yii::$app->cache->get('wechat_' . $key);
        }
    }

    /**
     * @param $data
     * @return string
     */

    public static function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $json
     * @return mixed
     */

    public static function jsonDecode($json)
    {
        return json_decode($json, true);
    }

    public static function getNews()
    {
        self::$_cache['news'] === null && self::$_cache['news'] = new \iit\wechat\News();
        return self::$_cache['news'];
    }

    public static function getMassNews()
    {
        self::$_cache['massNews'] === null && self::$_cache['massNews'] = new \iit\wechat\MassNews();
        return self::$_cache['massNews'];
    }

}