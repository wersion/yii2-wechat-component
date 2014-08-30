<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-8-30
 * Time: 上午9:09
 */

namespace iit\wechat;

class Article
{
    public $title;
    public $description;
    public $picUrl;
    public $url;

    function __construct($title = '', $description = '', $picUrl = '', $url = '')
    {
        $this->title = $title;
        $this->description = $description;
        $this->picUrl = $picUrl;
        $this->url = $url;
    }
} 