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
    public $maxCount = 10;

    public function setArticle($id, Article $article)
    {
        if($this->countNews() <= $this->maxCount){
            $this->_news[$id] = $article;
            return true;
        }else{
            return false;
        }
    }

    public function getArticle($id)
    {
        return isset($this->_news[$id]) ? $this->_news[$id] : false;
    }

    public function getArticles()
    {
        return $this->_news;
    }

    public function countNews()
    {
        return count($this->_news);
    }
} 