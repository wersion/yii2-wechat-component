<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:08
 */

namespace iit\wechat;

use yii\helpers\FileHelper;

class Media
{
    const FILE_CACHE_KEY = 'media_';
    const VIDEO_MAX_FILE_SIZE = '10M';
    const IMAGE_MAX_FILE_SIZE = '1M';
    const VOICE_MAX_FILE_SIZE = '2M';
    const THUMB_MAX_FILE_SIZE = '64K';
    const MEDIA_UPLOAD_URL = 'mediaUpload';
    const MEDIA_DOWNLOAD_URL = 'mediaDownload';
    private $_fileCacheKey;

    public function uploadImage($filePath, $forced = false)
    {
        return $this->uploadMedia($filePath, 'image', $forced);
    }

    /**
     * @param $filePath
     * @param $mediaType
     * @param bool $forced
     * @return bool
     */
    public function uploadMedia($filePath, $mediaType, $forced = false)
    {
        if ($this->getCacheKey($filePath)) {
            if (Wechat::getCache($this->getCacheKey($filePath)) && $forced === false) {
                return Wechat::getCache($this->getCacheKey($filePath));
            } else {
                if ($this->checkFile($filePath, $mediaType)) {
                    $result = Wechat::httpPost(self::MEDIA_UPLOAD_URL, [
                        'type' => $mediaType,
                        'media' => '@' . $filePath
                    ]);
                    if (isset($result['media_id'])) {
                        Wechat::setCache($this->getCacheKey($filePath), $result['media_id'], 3 * 24 * 3600);
                        return $result['media_id'];
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        } else {

            return false;
        }

    }

    public function getCacheKey($filePath)
    {
        if ($this->_fileCacheKey === null) {
            if (file_exists($filePath)) {
                $this->_fileCacheKey = self::FILE_CACHE_KEY . sha1_file($filePath);
            } else {
                return false;
            }
        }
        return $this->_fileCacheKey;
    }

    public function checkFile($filePath, $mediaType)
    {
        if (in_array(FileHelper::getMimeType($filePath), $this->getFileType($mediaType))) {
            if (filesize($filePath) <= $this->getMaxSize($mediaType)) {
                return true;
            }
        }
        return false;
    }

    public function getFileType($type)
    {
        switch ($type) {
            case 'image':
                return ['image/jpeg'];
                break;
            case 'video':
                return ['video/mp4'];
                break;
            case 'voice':
                return ['voice/mp3'];
                break;
            case 'thumb':
                return ['image/jpeg'];
                break;
            default:
                return false;
        }
    }

    public function getMaxSize($type)
    {
        switch ($type) {
            case 'image':
                return $this->convertFileSize(self::IMAGE_MAX_FILE_SIZE);
                break;
            case 'video':
                return $this->convertFileSize(self::VIDEO_MAX_FILE_SIZE);
                break;
            case 'thumb':
                return $this->convertFileSize(self::THUMB_MAX_FILE_SIZE);
                break;
            case 'voice':
                return $this->convertFileSize(self::VOICE_MAX_FILE_SIZE);
                break;
            default:
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

    public function uploadVoice($filePath, $forced = false)
    {
        return $this->uploadMedia($filePath, 'voice', $forced);
    }

    public function uploadVideo($filePath, $forced = false)
    {
        return $this->uploadMedia($filePath, 'video', $forced);
    }

    public function uploadThumb($filePath, $forced = false)
    {
        return $this->uploadMedia($filePath, 'thumb', $forced);
    }

    public function downloadMedia($mediaId, $savePath = null)
    {
        $url = Url::get(self::MEDIA_DOWNLOAD_URL) . '?' . http_build_query([
                'access_token' => Wechat::getAccessToken(),
                'media_id' => $mediaId
            ]);
        $fp = fopen($savePath,'w');
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_FILE,$fp);
        curl_exec($ch);
        curl_close($ch);
    }
} 