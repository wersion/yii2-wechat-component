<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-8-30
 * Time: 上午11:17
 */

namespace iit\wechat;


class BaseOAuth
{
    /**
     * OAuth2.0鉴权地址
     */

    const OAUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    /**
     * 通过验证码获取访问token地址
     */

    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code';

    /**
     * 通过刷新token获取访问token地址
     */

    const REFRESH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?grant_type=refresh_token';

    /**
     * 通过session存放openid的key值
     */

    const OPENID_SESSION_KEY = 'oauth_openid';

    /**
     * 缓存刷新token的key后缀
     */
    const REFRESH_TOKEN_CACHE = '_oauth_refresh_token';

    /**
     * 缓存访问token的key后缀
     */
    const ACCESS_TOKEN_CACHE = '_oauth_access_token';

    protected  $_component;
    private $_openid;
    private $_accessToken;
    private $_refreshToken;

    function __construct(Wechat $component)
    {
        $this->_component = $component;
    }

    /**
     * 获取openid，优先从缓存里读取
     * @return bool|mixed|null
     */

    public function getOpenid()
    {
        if ($this->_openid === null) {
            if ($sessionOpenid = \Yii::$app->session->get(self::OPENID_SESSION_KEY)) {
                $this->_openid = $sessionOpenid;
            } else {
                $result = $this->httpAccessTokenByCode();
                if ($result) {
                    isset($result['openid']) && $this->setOpenid($result['openid']);
                } else {
                    return false;
                }
            }
        }
        return $this->_openid;
    }

    /**
     * 设置openid
     * @param $openid
     */

    public function setOpenid($openid)
    {
        $this->_openid = $openid;
        return \Yii::$app->session->set(self::OPENID_SESSION_KEY, $openid);
    }

    /**
     * 通过验证码从微信服务器获取访问token
     * @return bool|mixed
     */

    public function httpAccessTokenByCode()
    {
        if ($code = $this->getCode()) {
            $result = $this->_component->httpGet(self::ACCESS_TOKEN_URL, [
                'appid' => $this->_component->appid,
                'secret' => $this->_component->appsecret,
                'code' => $code
            ], false);
            if ($result && !isset($result['errcode'])) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 通过刷新token从微信服务器获取访问token
     * @return bool
     */

    public function httpAccessTokenByRefreshToken()
    {
        if ($refreshToken = $this->getRefreshToken()) {
            $result = $this->_component->httpGet(self::REFRESH_TOKEN_URL, [
                'appid' => $this->_component->appid,
                'refresh_token' => $refreshToken
            ], false);
            if ($result && !isset($result['errcode'])) {
                $this->setRefreshToken($result['refresh_token']);
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取刷新token
     * @return bool|mixed
     */

    public function getRefreshToken()
    {
        if ($this->_refreshToken === null) {
            $cacheKey = $this->getOpenid() . self::REFRESH_TOKEN_CACHE;
            if ($cacheRefreshToken = $this->getCache($cacheKey)) {
                $this->_refreshToken = $cacheRefreshToken;
            } else {
                return false;
            }
        }
        return $this->_refreshToken;
    }

    /**
     * 设置刷新token
     * @param $token
     * @return bool
     */

    public function setRefreshToken($token)
    {
        $this->_refreshToken = $token;
        return $this->setCache($this->getOpenid() . self::REFRESH_TOKEN_CACHE, $token);
    }

    /**
     * @return mixed|null
     */

    public function getAccessToken()
    {
        if ($this->_accessToken === null) {
            if ($cacheAccessToken = $this->getCache($this->getOpenid() . self::ACCESS_TOKEN_CACHE)) {
                $this->_accessToken = $cacheAccessToken;
            } else {
                if ($result = $this->httpAccessTokenByRefreshToken()) {
                    $this->setAccessToken($result['access_token'], $result['expires_in']);
                } else {
                    if ($result = $this->httpAccessTokenByCode()) {
                        $this->setAccessToken($result['access_token'], $result['expires_in']);
                    } else {
                        return false;
                    }
                }
            }
        }
        return $this->_accessToken;
    }

    /**
     * 设置访问token的各种缓存
     * @param $token
     * @param $duration
     * @return bool
     */

    public function setAccessToken($token, $duration)
    {
        $this->_accessToken = $token;
        return $this->setCache($this->getOpenid() . self::ACCESS_TOKEN_CACHE, $token, $duration);
    }

    /**
     * 获取从鉴权地址跳转回来时带上的验证码
     * @return bool
     */

    public function getCode()
    {
        return \Yii::$app->request->get('code') ? : false;
    }

    /**
     * 组装OAuth2.0鉴权地址
     * @param null $state
     * @return string
     */

    public function getOAuthUrl($state = null)
    {
        return static::OAUTH_URL . '?' . http_build_query([
            'appid' => $this->_component->appid,
            'redirect_uri' => \Yii::$app->request->absoluteUrl,
            'response_type' => 'code',
            'scope' => 'snsapi_base',
            'state' => $state
        ]) . '#wechat_redirect';
    }

    /**
     * 调用应用缓存组件进行缓存数据
     * 智能判断应用缓存组件是否开启
     * 如果没有开启直接返回失败
     * @param $key
     * @param $value
     * @return bool
     */

    public function setCache($key, $value, $duration = null)
    {
        if (\Yii::$app->cache) {
            return \Yii::$app->cache->set($key, $value, $duration);
        } else {
            return false;
        }
    }

    /**
     * 从应用缓存组件里读取缓存数据
     * 智能判断应用缓存组件是否开启
     * 如果没有开启直接返回失败
     * @param $key
     * @return mixed
     */

    public function getCache($key)
    {
        if (\Yii::$app->cache) {
            return \Yii::$app->cache->get($key);
        } else {
            false;
        }
    }

} 