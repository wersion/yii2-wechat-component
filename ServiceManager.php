<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午3:30
 */

namespace iit\wechat;


class ServiceManager
{
    private $_wechat;

    public function __construct(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }
} 