<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-8-30
 * Time: 上午9:08
 */

namespace iit\wechat;

class News
{
    protected $_news = [];
    const MAX = 10;

    public function add($title = null, $description = null, $picUrl = null, $url = null)
    {
        if ($this->count() <= self::MAX) {
            $this->_news[] = [
                'title' => $title,
                'description' => $description,
                'picurl' => $picUrl,
                'url' => $url
            ];
            return $this;
        } else {
            return false;
        }
    }

    public function getAll()
    {
        return $this->_news;
    }

    public function count()
    {
        return count($this->_news);
    }

} 