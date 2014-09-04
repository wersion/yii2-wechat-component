<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:08
 */

namespace iit\wechat;

use yii\helpers\FileHelper;

class MediaManager extends BaseWechatManager
{
    const FILE_CACHE_KEY = 'media_';
    const VIDEO_MAX_FILE_SIZE = '10M';
    const IMAGE_MAX_FILE_SIZE = '1M';
    const VOICE_MAX_FILE_SIZE = '2M';
    const THUMB_MAX_FILE_SIZE = '64K';
    const MEDIA_UPLOAD_URL = 'http://file.api.weixin.qq.com/cgi-bin/media/upload';

    public function getMediaIdByImage($filePath)
    {
        return $this->getMediaIdByFile($filePath, 'image');
    }


    public function getMediaIdByVoice($filePath)
    {

    }

    public function getMediaIdByVideo($filePath)
    {

    }

    public function getMediaIdByThumb()
    {

    }

    public function getMediaIdByFile($filePath, $type)
    {
        if (file_exists($filePath)) {
            $hash = sha1_file($filePath);
            if ($mediaId = $this->getWechat()->getCache(self::FILE_CACHE_KEY . $hash)) {
                return $mediaId;
            } else {
                $fileSize = filesize($filePath);
                if ($fileSize <= $this->convertFileSize($this->getMaxSize($type)) && in_array(FileHelper::getMimeType($filePath), $this->getFileType($type))) {
                    $this->getWechat()->getGearman()->doBackground('upload', [
                        'type' => $type,
                        'file' => $filePath,
                        'openid' => $this->getWechat()->getReceiveManager()->getOpenid()
                    ]);
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
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
        $result = $this->getWechat()->httpPost(self::MEDIA_UPLOAD_URL . '?type=' . $mediaType, [
            'media' => '@' . $filePath
        ]);
        if (isset($result['media_id'])) {
            $hash = sha1_file($filePath);
            $this->getWechat()->setCache(self::FILE_CACHE_KEY . $hash, $result['media_id'], 259000);
            return $result['media_id'];
        } else {
            return false;
        }
    }

    public function convertFileSize($size)
    {
        if (strpos($size, 'M')) {
            return str_replace('M', '', $size) * 1024 * 1024;
        } elseif (strpos($size, 'K')) {
            return str_replace('K', '', $size) * 1024;
        } else {
            return 0;
        }
    }


    public function getMaxSize($type)
    {
        switch ($type) {
            case 'image':
                return self::IMAGE_MAX_FILE_SIZE;
                break;
            case 'video':
                return self::VIDEO_MAX_FILE_SIZE;
                break;
            default:
                return false;
        }
    }

    public function getFileType($type)
    {
        switch ($type) {
            case 'image':
                return ['image/jpeg'];
                break;
            case 'video':
                return [''];
                break;
            default:
                return false;
        }
    }


    public function downloadMedia($mediaId)
    {

    }
} 