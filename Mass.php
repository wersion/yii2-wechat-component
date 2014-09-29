<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午3:06
 */

namespace iit\wechat;


class Mass
{
    const UPLOAD_NEWS_URL = 'https://api.weixin.qq.com/cgi-bin/media/uploadnews';
    const UPLOAD_VIDEO_URL = 'https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo';
    const GROUP_SEND_URL = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall';
    const OPENID_SEND_URL = 'https://api.weixin.qq.com/cgi-bin/message/mass/send';
    const DELETE_URL = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete';

    public function sendTextByGroupId($groupid, $text)
    {
        $result = Wechat::httpRaw(self::GROUP_SEND_URL, json_encode([
            'filter' => [
                'group_id' => $groupid
            ],
            'text' => [
                'content' => $text
            ],
            'msgtype' => 'text'
        ]));
        return $result['errcode'] == 0 ? $result['msg_id'] : false;
    }

    public function sendImageByGroupId($groupid, $mediaId)
    {
        $result = Wechat::httpRaw(self::GROUP_SEND_URL, Wechat::jsonEncode([
            'filter' => [
                'group_id' => $groupid
            ],
            'image' => [
                'media_id' => $mediaId
            ],
            'msgtype' => 'image'
        ]));
        return $result['errcode'] == 0 ? $result['msg_id'] : false;
    }

    public function sendVideoByGroupId($groupid, $mediaId, $title = '', $description = '')
    {
        $mediaId = $this->uploadVideo($mediaId, $title, $description);
        if ($mediaId !== false) {
            $result = Wechat::httpRaw(self::GROUP_SEND_URL, Wechat::jsonEncode([
                'filter' => [
                    'group_id' => $groupid
                ],
                'mpvideo' => [
                    'media_id' => $mediaId
                ],
                'msgtype' => 'mpvideo'
            ]));
            return $result['errcode'] == 0 ? $result['msg_id'] : false;
        } else {
            return false;
        }
    }

    public function uploadVideo($mediaId, $title = '', $description = '')
    {
        $result = Wechat::httpRaw(self::UPLOAD_VIDEO_URL, Wechat::jsonEncode([
            'media_id' => $mediaId,
            'title' => $title,
            'description' => $description
        ]));
        return isset($result['media_id']) ? $result['media_id'] : false;
    }

    public function sendVoiceByGroupId($groupid, $mediaId)
    {
        $result = Wechat::httpRaw(self::GROUP_SEND_URL, Wechat::jsonEncode([
            'filter' => [
                'group_id' => $groupid
            ],
            'voice' => [
                'media_id' => $mediaId
            ],
            'msgtype' => 'voice'
        ]));
        return $result['errcode'] == 0 ? $result['msg_id'] : false;
    }

    public function sendNewsByGroupId($groupid, News $news)
    {
        $mediaId = $this->uploadNews($news);
        if ($mediaId !== false) {
            $result = Wechat::httpRaw(self::GROUP_SEND_URL, Wechat::jsonEncode([
                'filter' => [
                    'group_id' => $groupid
                ],
                'mpnews' => [
                    'media_id' => $mediaId
                ],
                'msgtype' => 'mpnews'
            ]));
            return $result['errcode'] == 0 ? $result['msg_id'] : false;
        } else {
            return false;
        }
    }

    public function uploadNews(\iit\wechat\News $news)
    {
        if ($news->countGroupArticle() != 0) {
            $articles = [];
            foreach ($news->getGroupArticles() as $line) {
                $articles[] = [
                    'thumb_media_id' => $line->thumb_media_id,
                    'author' => $line->author,
                    'title' => $line->title,
                    'content_source_url' => $line->content_source_url,
                    'content' => $line->content,
                    'digest' => $line->digest,
                ];
            }
            $result = $this->getWechat()->httpRaw(self::UPLOAD_NEWS_URL, $this->getWechat()->jsonEncode([
                'articles' => $articles
            ]));
            return isset($result['media_id']) ? $result['media_id'] : false;
        } else {
            return false;
        }
    }

    public function sendTextByOpenids(array $openids, $text)
    {
        $result = $this->getWechat()->httpRaw(self::OPENID_SEND_URL, $this->getWechat()->jsonEncode([
            'touser' => $openids,
            'msgtype' => 'text',
            'text' => [
                'content' => $text
            ]
        ]));
        return $result['errcode'] == 0 ? $result['msg_id'] : false;
    }

    public function sendImageByOpenids(array $openids, $mediaId)
    {
        $result = $this->getWechat()->httpRaw(self::OPENID_SEND_URL, $this->getWechat()->jsonEncode([
            'touser' => $openids,
            'msgtype' => 'image',
            'image' => [
                'media_id' => $mediaId
            ]
        ]));
        return $result['errcode'] == 0 ? $result['msg_id'] : false;
    }

    public function sendVideoByOpenids(array $openids, $mediaId, $title = '', $description = '')
    {
        $mediaId = $this->uploadVideo($mediaId, $title, $description);
        if ($mediaId !== false) {
            $result = $this->getWechat()->httpRaw(self::OPENID_SEND_URL, $this->getWechat()->jsonEncode([
                'touser' => $openids,
                'msgtype' => 'video',
                'video' => [
                    'media_id' => $mediaId,
                    'title' => $title,
                    'description' => $description
                ]
            ]));
            return $result['errcode'] == 0 ? $result['msg_id'] : false;
        } else {
            return false;
        }
    }

    public function sendVoiceByOpenids(array $openids, $mediaId)
    {
        $result = $this->getWechat()->httpRaw(self::OPENID_SEND_URL, $this->getWechat()->jsonEncode([
            'touser' => $openids,
            'msgtype' => 'voice',
            'voice' => [
                'media_id' => $mediaId
            ]
        ]));
        return $result['errcode'] == 0 ? $result['msg_id'] : false;
    }

    public function sendNewsByOpenids(array $openids, News $news)
    {
        $mediaId = $this->uploadNews($news);
        if ($mediaId !== false) {
            $result = $this->getWechat()->httpRaw(self::OPENID_SEND_URL, $this->getWechat()->jsonEncode([
                'touser' => $openids,
                'msgtype' => 'mpnews',
                'mpnews' => [
                    'media_id' => $mediaId,
                ]
            ]));
            return $result['errcode'] == 0 ? $result['msg_id'] : false;
        } else {
            return false;
        }
    }

    public function delete($msgid)
    {
        $result = $this->getWechat()->httpRaw(self::DELETE_URL, $this->getWechat()->jsonEncode([
            'msg_id' => $msgid
        ]));
        return $result['errcode'] == 0 ? true : false;
    }
} 