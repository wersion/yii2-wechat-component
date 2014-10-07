<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:35
 */

namespace iit\wechat;


class Response
{

    /**
     * 发送文字类型响应信息
     * @param $message
     * @return string
     */

    public function sendText($message)
    {
        $sendArr = [
            'ToUserName' => Wechat::$component->receive->getOpenid(),
            'FromUserName' => Wechat::$component->receive->getWechatid(),
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $message
        ];
        return $this->send($sendArr);
    }

    /**
     * 发送图片类型响应信息
     * @param $mediaId
     * @return string
     */

    public function sendImage($mediaId)
    {
        $sendArr = [
            'ToUserName' => Wechat::$component->receive->getOpenid(),
            'FromUserName' => Wechat::$component->receive->getWechatid(),
            'CreateTime' => time(),
            'MsgType' => 'image',
            'Image' => [
                'MediaId' => $mediaId
            ]
        ];
        return $this->send($sendArr);
    }

    /**
     * 发送声音类型响应信息
     * @param $mediaId
     * @return string
     */

    public function sendVoice($mediaId)
    {
        $sendArr = [
            'ToUserName' => Wechat::$component->receive->getOpenid(),
            'FromUserName' => Wechat::$component->receive->getWechatid(),
            'CreateTime' => time(),
            'MsgType' => 'voice',
            'Voice' => [
                'MediaId' => $mediaId
            ]
        ];
        return $this->send($sendArr);
    }

    /**
     * 发送视频类型响应信息
     * @param $mediaId
     * @param string $title
     * @param string $description
     * @return string
     */

    public function sendVideo($mediaId, $title = '', $description = '')
    {
        $sendArr = [
            'ToUserName' => Wechat::$component->receive->getOpenid(),
            'FromUserName' => Wechat::$component->receive->getWechatid(),
            'CreateTime' => time(),
            'MsgType' => 'video',
            'Video' => [
                'MediaId' => $mediaId,
                'Title' => $title,
                'Description' => $description
            ]
        ];
        return $this->send($sendArr);
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

    public function sendMusic($musicUrl, $hqMusicUrl, $thumbMediaId, $title = '', $description = '')
    {
        $sendArr = [
            'ToUserName' => Wechat::$component->receive->getOpenid(),
            'FromUserName' => Wechat::$component->receive->getWechatid(),
            'CreateTime' => time(),
            'MsgType' => 'music',
            'Music' => [
                'Title' => $title,
                'Description' => $description,
                'MusicURL' => $musicUrl,
                'HQMusicUrl' => $hqMusicUrl,
                'ThumbMediaId' => $thumbMediaId
            ]

        ];
        return $this->send($sendArr);
    }

    /**
     * 发送图文类型响应信息
     * @param News $news
     * @return string
     */

    public function sendNews(\iit\wechat\News $news)
    {
        if ($news->count() != 0) {
            $articles = [];
            foreach ($news->getAll() as $article) {
                $articles[] = [
                    'item' => [
                        'Title' => $article['title'],
                        'Description' => $article['description'],
                        'PicUrl' => $article['picurl'],
                        'Url' => $article['url'],
                    ]
                ];
            }
            $sendArr = [
                'ToUserName' => Wechat::$component->receive->getOpenid(),
                'FromUserName' => Wechat::$component->receive->getWechatid(),
                'CreateTime' => time(),
                'MsgType' => 'news',
                'ArticleCount' => $news->count(),
                'Articles' => $articles
            ];
            return $this->send($sendArr);
        } else {
            return '';
        }
    }

    public function turnToService()
    {
        return $this->send([
            'ToUserName' => Wechat::$component->receive->getOpenid(),
            'FromUserName' => Wechat::$component->receive->getWechatid(),
            'CreateTime' => time(),
            'MsgType' => 'transfer_customer_service'
        ]);
    }

    /**
     * @param array $array
     * @return string
     */

    public function send(array $array, $addXml = true)
    {
        $xml = $addXml === true ? '<xml>' : '';
        foreach ($array as $key => $val) {
            $xml .= (is_numeric($key) ? '' : '<' . $key . '>') . (is_array($val) ? $this->send($val, false) : '<![CDATA[' . $val . ']]>') . (is_numeric($key) ? '' : '</' . $key . '> ');
        }
        $xml .= $addXml === true ? '</xml> ' : '';
        return $xml;
    }
} 