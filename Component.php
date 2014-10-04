<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 2014/9/29
 * Time: 13:44
 */

namespace iit\wechat;


use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 *
 * @property \iit\wechat\Media $media
 * @property \iit\wechat\Receive $receive
 * @property \iit\wechat\Response $response
 * @property \iit\wechat\Menu $menu
 * @property \iit\wechat\Service $service
 * @property \iit\wechat\User $user
 *
 */
class Component extends \yii\base\Component
{
    public $appid;
    public $appsecret;
    public $token;
    public $classMap = [];

    private $_apps;

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
        Wechat::$component = $this;
    }

    /**
     *
     * @param $appName
     * @return mixed
     */

    public function getApp($appName)
    {
        $classMap = ArrayHelper::merge($this->coreClass(), $this->classMap);
        if (isset($classMap[$appName]) && !empty($classMap[$appName])) {
            $cacheKey = sha1($classMap[$appName]);
            if (!isset($this->_apps[$cacheKey])) {
                $this->_apps[$cacheKey] = \Yii::createObject($classMap[$appName]);
            }
            return $this->_apps[$cacheKey];
        } else {
            throw new InvalidParamException("Not Found " . $appName);
        }
    }

    public function coreClass()
    {
        return [
            'receive' => '\iit\wechat\Receive',
            'menu' => '\iit\wechat\Menu',
            'service' => '\iit\wechat\Service',
            'response' => '\iit\wechat\Response',
            'media' => '\iit\wechat\Media',
            'user' => '\iit\wechat\User',
            'baseOAuth' => '\iit\wechat\BaseOAuth',
            'userInfoOAuth' => '\iit\wechat\UserInfoOAuth',
        ];
    }

    /**
     * @return \iit\wechat\Receive $receive
     */

    public function getReceive()
    {
        return $this->getApp('receive');
    }

    /**
     * @return \iit\wechat\BaseOAuth $baseOAuth
     */

    public function getBaseOAuth()
    {
        return $this->getApp('baseOAuth');
    }

    /**
     * @return \iit\wechat\UserInfoOAuth userInfoOAuth
     */

    public function getUserInfoOAuth()
    {
        return $this->getApp('userInfoOAuth');
    }

    /**
     * @return \iit\wechat\UserManager $userManager
     */

    public function getUser()
    {
        return $this->getApp('user');
    }

    /**
     * @return \iit\wechat\MediaManager mediaManager
     */

    public function getMedia()
    {
        return $this->getApp('media');
    }

    /**
     * @return \iit\wechat\MenuManager $menuManager
     */

    public function getMenu()
    {
        return $this->getApp('menu');
    }

    /**
     * @return \iit\wechat\ResponseManager $responseManager
     */

    public function getResponse()
    {
        return $this->getApp('response');
    }

    /**
     * @return \iit\wechat\ServiceManager $serviceManager
     */

    public function getService()
    {
        return $this->getApp('service');
    }


} 