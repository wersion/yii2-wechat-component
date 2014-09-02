<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午3:06
 */

namespace iit\wechat;


class GroupSend
{
    private $_wechat;

    public function __construct(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    public function uploadNews(News $news)
    {

    }

    public function uploadVideo($mediaId)
    {

    }

    public function sendTextByGroupId($groupid, $text)
    {

    }

    public function sendImageByGroupId($groupid, $mediaId)
    {

    }

    public function sendVideoByGroupId($groupid, $mediaId)
    {

    }

    public function sendVoiceByGroupId($groupid, $mediaId)
    {

    }

    public function sendNewsByGroupId($groupid, News $news)
    {

    }

    public function sendTextByOpenids(array $openids, $text)
    {

    }

    public function sendImageByOpenids(array $openids, $mediaId)
    {

    }

    public function sendVideoByOpenids(array $openids, $mediaId)
    {

    }

    public function sendVoiceByOpenids(array $openids, $mediaId)
    {

    }

    public function sendNewsByOpenids(array $openids, News $news)
    {

    }

    public function delete($msgid)
    {

    }
} 