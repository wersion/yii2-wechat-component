<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-8-30
 * Time: 上午9:08
 */

namespace iit\wechat;


use yii\base\Object;

class News extends Object
{
    private $_news = [];
    private $_groupNews = [];
    public $maxCount = 10;

    public function setArticle($id, \iit\wechat\Article $article)
    {
        if ($this->countArticle() <= $this->maxCount) {
            $this->_news[$id] = $article;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $id
     * @return \iit\wechat\Article
     */

    public function getArticle($id)
    {
        isset($this->_news[$id]) ?: $this->_news[$id] = new \iit\wechat\Article();
        return $this->_news[$id];
    }

    public function getArticles()
    {
        return $this->_news;
    }

    public function countArticle()
    {
        return count($this->_news);
    }

    public function setGroupArticle($id, \iit\wechat\GroupArticle $article)
    {
        if ($this->countGroupArticle() <= $this->maxCount) {
            $this->_groupNews[$id] = $article;
            return true;
        } else {
            return false;
        }
    }

    public function getGroupArticle($id)
    {
        isset($this->_groupNews[$id]) ?: $this->_groupNews[$id] = new \iit\wechat\GroupArticle();
    }

    public function getGroupArticles()
    {
        return $this->_groupNews;
    }

    public function countGroupArticle()
    {
        return count($this->_groupNews);
    }
} 