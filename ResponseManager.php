<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:35
 */

namespace iit\wechat;


class ResponseManager
{
    private $_wechat;

    public function __construct(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    /**
     * 发送文字类型响应信息
     * @param $message
     * @return string
     */

    public function sendResponseText($message)
    {
        $sendArr = [
            'ToUserName' => $this->openid,
            'FromUserName' => $this->wechatid,
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $message
        ];
        return $this->sendResponseMessage($sendArr);
    }

    /**
     * 发送图片类型响应信息
     * @param $mediaId
     * @return string
     */

    public function sendResponseImage($mediaId)
    {
        $sendArr = [
            'ToUserName' => $this->openid,
            'FromUserName' => $this->wechatid,
            'CreateTime' => time(),
            'MsgType' => 'image',
            'MediaId' => $mediaId
        ];
        return $this->sendResponseMessage($sendArr);
    }

    /**
     * 发送声音类型响应信息
     * @param $mediaId
     * @return string
     */

    public function sendResponseVoice($mediaId)
    {
        $sendArr = [
            'ToUserName' => $this->openid,
            'FromUserName' => $this->wechatid,
            'CreateTime' => time(),
            'MsgType' => 'voice',
            'MediaId' => $mediaId
        ];
        return $this->sendResponseMessage($sendArr);
    }

    /**
     * 发送视频类型响应信息
     * @param $mediaId
     * @param string $title
     * @param string $description
     * @return string
     */

    public function sendResponseVideo($mediaId, $title = '', $description = '')
    {
        $sendArr = [
            'ToUserName' => $this->openid,
            'FromUserName' => $this->wechatid,
            'CreateTime' => time(),
            'MsgType' => 'video',
            'MediaId' => $mediaId,
            'Title' => $title,
            'Description' => $description
        ];
        return $this->sendResponseMessage($sendArr);
    }

    /**
     * 发送音乐类型响应信息
     * @param $mediaId
     * @param string $title
     * @param string $description
     * @param string $url
     * @param string $hqUrl
     * @return string
     */

    public function sendResponseMusic($mediaId, $title = '', $description = '', $url = '', $hqUrl = '')
    {
        $sendArr = [
            'ToUserName' => $this->openid,
            'FromUserName' => $this->wechatid,
            'CreateTime' => time(),
            'MsgType' => 'music',
            'Title' => $title,
            'Description' => $description,
            'MusicURL' => $url,
            'HQMusicUrl' => $hqUrl,
            'ThumbMediaId' => $mediaId
        ];
        return $this->sendResponseMessage($sendArr);
    }

    /**
     * 发送图文类型响应信息
     * @param News $news
     * @return string
     */

    public function sendResponseNews(News $news)
    {
        if ($news->countNews() != 0) {
            $articles = [];
            foreach ($news->getArticles() as $article) {
                $articles[] = [
                    'item' => [
                        'Title' => $article->title,
                        'Description' => $article->description,
                        'PicUrl' => $article->picUrl,
                        'Url' => $article->url,
                    ]
                ];
            }
            $sendArr = [
                'ToUserName' => $this->openid,
                'FromUserName' => $this->wechatid,
                'CreateTime' => time(),
                'MsgType' => 'news',
                'ArticleCount' => $news->countNews(),
                'Articles' => $articles
            ];
            return $this->sendResponseMessage($sendArr);
        } else {
            return '';
        }
    }

    /**
     * @param array $array
     * @return string
     */

    public function sendResponseMessage(array $array, $addXml = true)
    {
        $xml = $addXml === true ? '<xml>' : '';
        foreach ($array as $key => $val) {
            $xml .= (is_numeric($key) ? '' : '<' . $key . '>') . (is_array($val) ? $this->sendResponseMessage($val, false) : '<![CDATA[' . $val . ']]>') . (is_numeric($key) ? '' : '</' . $key . '> ');
        }
        $xml .= $addXml === true ? '</xml> ' : '';
        return $xml;
    }
} 