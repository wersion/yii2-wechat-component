<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午3:30
 */

namespace iit\wechat;


class ServiceManager extends BaseWechatManager
{

    const SERVICE_URL = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';
    const GET_SERVICE_RECORD_URL = 'https://api.weixin.qq.com/cgi-bin/customservice/getrecord';

    public function sendText($openid, $text)
    {
        $result = $this->getWechat()->httpRaw(self::SERVICE_URL, $this->getWechat()->jsonEncode([
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content' => $text
            ]
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function sendImage($openid, $mediaId)
    {
        $result = $this->getWechat()->httpRaw(self::SERVICE_URL, $this->getWechat()->jsonEncode([
            'touser' => $openid,
            'msgtype' => 'image',
            'image' => [
                'media_id' => $mediaId
            ]
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function sendVoice($openid, $mediaId)
    {
        $result = $this->getWechat()->httpRaw(self::SERVICE_URL, $this->getWechat()->jsonEncode([
            'touser' => $openid,
            'msgtype' => 'voice',
            'voice' => [
                'media_id' => $mediaId
            ]
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function sendVideo($openid, $videoMediaId, $thumbMediaId, $title = '', $description = '')
    {
        $result = $this->getWechat()->httpRaw(self::SERVICE_URL, $this->getWechat()->jsonEncode([
            'touser' => $openid,
            'msgtype' => 'video',
            'video' => [
                'media_id' => $videoMediaId,
                'thumb_media_id' => $thumbMediaId,
                'title' => $title,
                'description' => $description
            ]
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function sendMusic($openid, $musicUrl, $hqMusicUrl, $thumbMediaId, $title = '', $description = '')
    {
        $result = $this->getWechat()->httpRaw(self::SERVICE_URL, $this->getWechat()->jsonEncode([
            'touser' => $openid,
            'msgtype' => 'music',
            'music' => [
                'title' => $title,
                'description' => $description,
                'musicurl' => $musicUrl,
                'hqmusicurl' => $hqMusicUrl,
                'thumb_media_id' => $thumbMediaId
            ]
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function sendNews($openid, \iit\wechat\News $news)
    {
        if ($news->countArticle() != 0) {
            $articles = [];
            foreach ($news->getArticles() as $article) {
                $articles[] = [
                    'title' => $article->title,
                    'description' => $article->description,
                    'picurl' => $article->picUrl,
                    'url' => $article->url,
                ];
            }
            $result = $this->getWechat()->httpRaw(self::SERVICE_URL, $this->getWechat()->jsonEncode([
                'touser' => $openid,
                'msgtype' => 'news',
                'news' => [
                    'articles' => $articles,
                ]
            ]));
            return $result['errcode'] == 0 ? true : false;
        } else {
            return false;
        }
    }

    public function getServiceRecord($beginTime, $endTime, $pageIndex = 1, $pageSize = 1000, $openid = null)
    {
        
    }

} 