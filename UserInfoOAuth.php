<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 上午9:06
 */

namespace iit\wechat;


class UserInfoOAuth extends BaseOAuth
{

    /**
     * 获取用户信息地址
     */

    const USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo';

    /**
     * 用户信息缓存key后缀
     */

    const USER_INFO_CACHE = '_oauth_userinfo';

    private $_userInfo;

    /**
     * @param string $lang
     * @return bool|mixed
     */

    public function getUserInfo($lang = 'zh_CN')
    {
        if ($this->_userInfo === null) {
            if (!$this->_userInfo = $this->getCache($this->getOpenid() . self::USER_INFO_CACHE)) {
                $result = $this->_component->httpGet(self::USER_INFO_URL, [
                    'access_token' => $this->getAccessToken(),
                    'openid' => $this->getOpenid(),
                    'lang' => $lang
                ], false);
                if ($result && !isset($result['errcode'])) {
                    $this->setUserInfo($result);
                } else {
                    return false;
                }
            }
        }
        return $this->_userInfo;
    }

    /**
     * @param $info
     * @return bool
     */

    public function setUserInfo($info)
    {
        $this->_userInfo = $info;
        return $this->setCache($this->getOpenid() . self::USER_INFO_CACHE, $info, 60);
    }

    /**
     * @return mixed
     */

    public function getNickName()
    {
        return $this->getUserInfo()['nickname'];
    }

    /**
     * @return mixed
     */

    public function getSex()
    {
        return $this->getUserInfo()['sex'];
    }

    /**
     * @return mixed
     */

    public function getProvince()
    {
        return $this->getUserInfo()['province'];
    }

    /**
     * @return mixed
     */

    public function getCity()
    {
        return $this->getUserInfo()['city'];
    }

    /**
     * @return mixed
     */

    public function getCountry()
    {
        return $this->getUserInfo()['country'];
    }

    /**
     * @return mixed
     */

    public function getHeadImgUrl()
    {
        return $this->getUserInfo()['headimgurl'];
    }

    /**
     * @return mixed
     */

    public function getPrivilege()
    {
        return $this->getUserInfo()['privilege'];
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
            'scope' => 'snsapi_userinfo',
            'state' => $state
        ]) . '#wechat_redirect';
    }
} 