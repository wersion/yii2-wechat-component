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
    const UPLOAD_NEWS_URL = 'mass_upload_news';
    const UPLOAD_VIDEO_URL = 'mass_upload_video';
    const GROUP_SEND_URL = 'mass_group_send';
    const OPENID_SEND_URL = 'mass_openid_send';
    const DELETE_URL = 'mass_delete';

    public function sendTextByGroupId($groupid, $text)
    {
        $result = Wechat::httpRaw(self::GROUP_SEND_URL, Wechat::jsonEncode([
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
            $result = Wechat::httpRaw(self::UPLOAD_NEWS_URL, Wechat::jsonEncode([
                'articles' => $articles
            ]));
            return isset($result['media_id']) ? $result['media_id'] : false;
        } else {
            return false;
        }
    }

    public function sendTextByOpenids(array $openids, $text)
    {
        $result = Wechat::httpRaw(self::OPENID_SEND_URL, Wechat::jsonEncode([
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
        $result = Wechat::httpRaw(self::OPENID_SEND_URL, Wechat::jsonEncode([
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
            $result = Wechat::httpRaw(self::OPENID_SEND_URL, Wechat::jsonEncode([
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
        $result = Wechat::httpRaw(self::OPENID_SEND_URL, Wechat::jsonEncode([
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
            $result = Wechat::httpRaw(self::OPENID_SEND_URL, Wechat::jsonEncode([
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
        $result = Wechat::httpRaw(self::DELETE_URL, Wechat::jsonEncode([
            'msg_id' => $msgid
        ]));
        return $result['errcode'] == 0 ? true : false;
    }
} 