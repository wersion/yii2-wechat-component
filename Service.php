<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午3:30
 */

namespace iit\wechat;


class Service
{
    const SERVICE_MESSAGE_URL = 'serviceMessage';
    const CUSTOM_SERVICE_URL = 'customService';

    public function sendText($openid, $text)
    {
        $result = Wechat::httpRaw(Url::get(self::SERVICE_MESSAGE_URL), Wechat::jsonEncode([
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
        $result = Wechat::httpRaw(Url::get(self::SERVICE_MESSAGE_URL), Wechat::jsonEncode([
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
        $result = Wechat::httpRaw(Url::get(self::SERVICE_MESSAGE_URL), Wechat::jsonEncode([
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
        $result = Wechat::httpRaw(Url::get(self::SERVICE_MESSAGE_URL), Wechat::jsonEncode([
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
        $result = Wechat::httpRaw(Url::get(self::SERVICE_MESSAGE_URL), Wechat::jsonEncode([
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
            $result = Wechat::httpRaw(Url::get(self::SERVICE_MESSAGE_URL), Wechat::jsonEncode([
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

    public function getServiceRecord($startTime, $endTime, $pageIndex = 1, $pageSize = 1000, $openid = null)
    {
        $result = Wechat::httpPost(self::CUSTOM_SERVICE_URL, Wechat::jsonEncode([
            'starttime' => $startTime,
            'endtime' => $endTime,
            'openid' => $openid,
            'pagesize' => $pageSize,
            'pageindex' => $pageIndex
        ]));
        return isset($result['errcode']) ? false : $result['recordlist'];
    }

} 