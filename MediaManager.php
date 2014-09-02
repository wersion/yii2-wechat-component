<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:08
 */

namespace iit\wechat;


class MediaManager
{
    private $_wechat;

    public function __construct(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    public function uploadImage($filePath)
    {
        return $this->uploadMedia($filePath, 'image');
    }

    public function uploadVoice($filePath)
    {
        return $this->uploadMedia($filePath, 'voice');
    }

    public function uploadVideo($filePath)
    {
        return $this->uploadMedia($filePath, 'video');
    }

    public function uploadThumb($filePath)
    {
        return $this->uploadMedia($filePath, 'thumb');
    }

    /**
     * 上传媒体文件
     * @param $filePath
     * @param $mediaType 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb，主要用于视频与音乐格式的缩略图）
     * 图片（image）: 1M，支持JPG格式
     * 语音（voice）：2M，播放长度不超过60s，支持AMR\MP3格式
     * 视频（video）：10MB，支持MP4格式
     * 缩略图（thumb）：64KB，支持JPG格式
     * @return array|bool
     */
    public function uploadMedia($filePath, $mediaType)
    {
        $result = $this->_wechat->httpPost(self::MEDIA_UPLOAD_URL . '?type=' . $mediaType, [
            'media' => '@' . $filePath
        ]);
        return isset($result['media_id']) ? $result : false;
    }

    public function getMedia($mediaId)
    {

    }
} 