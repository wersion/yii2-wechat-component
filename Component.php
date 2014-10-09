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
 * @property \iit\wechat\OAuth $oauth
 * @property \iit\wechat\Template $template
 * @property \iit\wechat\Mass $mass
 *
 */
class Component extends \yii\base\Component
{
    public $appid;
    public $appsecret;
    public $token;
    public $classMap = [];

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
            $name = 'wechat' . ucfirst($appName);
            if (!\Yii::$app->has($name)) {
                \Yii::$app->set($name, $classMap[$appName]);
            }
            return \Yii::$app->get($name);
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
            'oauth' => '\iit\wechat\OAuth',
            'template' => '\iit\wechat\Template',
            'mass' => '\iit\wechat\Mass',
            'qrcode' => '\iit\wechat\QRCode',
        ];
    }

    /**
     * @return \iit\wechat\Template $template
     */

    public function getTemplate()
    {
        return $this->getApp('template');
    }

    /**
     * @return \iit\wechat\Receive $receive
     */

    public function getReceive()
    {
        return $this->getApp('receive');
    }

    /**
     * @return \iit\wechat\OAuth $OAuth
     */

    public function getOAuth()
    {
        return $this->getApp('oauth');
    }

    /**
     * @return \iit\wechat\User $userManager
     */

    public function getUser()
    {
        return $this->getApp('user');
    }

    /**
     * @return \iit\wechat\Media $media
     */

    public function getMedia()
    {
        return $this->getApp('media');
    }

    /**
     * @return \iit\wechat\Menu $menu
     */

    public function getMenu()
    {
        return $this->getApp('menu');
    }

    /**
     * @return \iit\wechat\Response $response
     */

    public function getResponse()
    {
        return $this->getApp('response');
    }

    /**
     * @return \iit\wechat\Service $service
     */

    public function getService()
    {
        return $this->getApp('service');
    }


} 