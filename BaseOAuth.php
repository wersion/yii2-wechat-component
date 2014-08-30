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
    const OAUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code';
    const REFRESH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?grant_type=refresh_token';
    const OPENID_SESSION_KEY = 'oauth_openid';
    private $_appid;
    private $_appsecret;
    private $_openid;
    private $_accessToken;
    private $_refreshToken;

    function __construct($appid)
    {
        $this->_appid = $appid;
    }

    public function getOpenid()
    {
        if ($this->_openid === null) {
            if ($sessionOpenid = \Yii::$app->session->get(self::OPENID_SESSION_KEY)) {
                $this->_openid = $sessionOpenid;
            } else {
                $result = $this->httpAccessTokenByCode();
                if ($result) {

                } else {
                    return false;
                }
            }
        }
        return $this->_openid;
    }

    public function httpAccessTokenByCode()
    {
        if ($code = $this->getCode()) {
            $result = \Yii::$app->wechat->httpGet(self::ACCESS_TOKEN_URL, [
                'appid' => $this->_appid,
                'secret' => $this->_appsecret,
                'code' => $code
            ], false);
            if ($result) {
                if(isset($result['errcode']))
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function httpAccessTokenByRefreshToken()
    {
        if ($refreshToken = $this->getRefreshToken()) {

        } else {
            return false;
        }
    }


    public function getRefreshToken()
    {
        if ($this->_refreshToken === null) {
            $cacheKey = $this->_appid . '_oauth_refresh_token';
            if ($cacheRefreshToken = \Yii::$app->cache->get($cacheKey)) {
                $this->_refreshToken = $cacheRefreshToken;
            } else {
                return false;
            }
        }
        return $this->_refreshToken;
    }

    public function setRefreshToken($token)
    {
        $this->_refreshToken = $token;
        \Yii::$app->cache->set()
    }

    public function getAccessToken()
    {
        if ($this->_accessToken === null) {
            $cacheKey = $this->_appid . '_oauth_access_token';
            if ($cacheAccessToken = \Yii::$app->cache->get($cacheKey)) {
                $this->_accessToken = $cacheAccessToken;
            } else {

            }
        }
        return $this->_accessToken;
    }

    public function getCode()
    {
        return \Yii::$app->request->get('code') ? : false;
    }

    public function getOAuthUrl($state = null)
    {
        return self::OAUTH_URL . '?' . http_build_query([
            'appid' => $this->_appid,
            'redirect_uri' => \Yii::$app->homeUrl,
            'response_type' => 'code',
            'scope' => 'snsapi_base',
            'state' => $state
        ]) . '#wechat_redirect';
    }

} 