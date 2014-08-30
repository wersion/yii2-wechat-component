<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-8-28
 * Time: 下午3:56
 */

namespace iit\wechat;

use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;

class Wechat extends Component
{

    const GET_ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';

    const MEDIA_UPLOAD_URL = 'http://file.api.weixin.qq.com/cgi-bin/media/upload';

    public $appid;
    public $appsecret;
    public $token;
    public $openid;
    public $wechatid;
    private $_accessToken;
    private $_oauth;

    /**
     * @throws \yii\base\InvalidParamException
     */

    public function init()
    {
        parent::init();
        if ($this->appid === null)
            throw new InvalidParamException('The appid has not configure.');
        if ($this->appsecret === null)
            throw new InvalidParamException('The appsecret has not configure.');
        if ($this->token === null)
            throw new InvalidParamException('The token has not configure.');
    }

    /**
     * @param bool $forceUpdate
     * @return bool|mixed|null
     */

    public function getAccessToken($forceUpdate = false)
    {
        if ($this->_accessToken === null || $forceUpdate === true) {
            $cacheKey = sha1($this->appid);
            $forceUpdate === false && $cacheToken = \Yii::$app->cache->get($cacheKey);
            if ($cacheToken == false || $forceUpdate == true) {
                $result = $this->httpGet(self::GET_ACCESS_TOKEN_URL, ['appid' => $this->appid, 'secret' => $this->appsecret], false, false);
                if (!isset($result['errcode'])) {
                    $this->_accessToken = $result['access_token'];
                    \Yii::$app->cache->set($cacheKey, $this->_accessToken);
                }
            } else {
                $this->_accessToken = $cacheToken;
            }
        }
        return ($this->_accessToken === null) ? false : $this->_accessToken;
    }

    /**
     * @param $receiveObj
     * @return string
     */

    public function receive($receiveObj)
    {
        $methodName = 'receive' . ucfirst(strtolower($receiveObj->MsgType));
        if ($this->hasMethod($methodName)) {
            $this->openid = $receiveObj->FromUserName;
            $this->wechatid = $receiveObj->ToUserName;
            return $this->$methodName($receiveObj);
        } else {
            return '';
        }
    }

    /**
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return bool
     */

    public function signature($signature, $timestamp, $nonce)
    {
        $tmpArr = [$this->token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        return sha1(implode($tmpArr)) == $signature ? true : false;
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

    public function getBaseOAuth()
    {
        return new BaseOAuth($this->appid);
    }

    public function getUserInfoOAuth()
    {
        return new UserInfoOAuth();
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
        $result = $this->httpPost(self::MEDIA_UPLOAD_URL . '?type=' . $mediaType, [
            'media' => '@' . $filePath
        ]);
        return isset($result['media_id']) ? $result : false;
    }

    public function getMedia($mediaId)
    {

    }

    /**
     * @param $url
     * @param null $params
     * @param bool $etry
     * @param string $token
     * @return bool|mixed
     */
    public function httpGet($url, $params = null, $token = 'url', $etry = true)
    {
        return $this->parseHttpResult($url, $params, 'get', $token, $etry);
    }

    /**
     * @param $url
     * @param null $params
     * @param string $token
     * @return bool|mixed
     */
    public function httpPost($url, $params = null, $token = 'url', $etry = true)
    {
        return $this->parseHttpResult($url, $params, 'post', $token, $etry);
    }

    /**
     * @param $url
     * @param null $params
     * @param string $token
     * @param bool $etry
     * @return bool|mixed
     */

    public function httpRaw($url, $params = null, $token = 'url', $etry = true)
    {
        return $this->parseHttpResult($url, $params, 'raw', $token, $etry);
    }

    /**
     * @param $url
     * @param $params
     * @param $method
     * @param bool $token
     * @param bool $etry
     * @return bool|mixed
     */
    public function parseHttpResult($url, $params, $method, $token = false, $etry = true)
    {
        $return = $this->http($url, $params, $method, $token);
        $return = json_decode($return, true) ? : $return;
        if (isset($return['errcode']) && $etry === true && $token != false) {
            switch ($return['errcode']) {
                case 40001:
                    $this->getAccessToken(true) && $return = $this->parseHttpResult($url, $params, $method, $token, false);
                    break;
            }
        }
        return $return;
    }

    /**
     * Http协议调用微信接口方法
     * @param $url api地址
     * @param $params 参数
     * @param string $type 提交类型
     * @param string $token 添加AccessToken类型
     * @return bool|mixed
     * @throws \yii\base\InvalidParamException
     */
    public function http($url, $params = null, $type = 'get', $token = false)
    {
        if ($token) {
            if ($token == 'url') {
                $url .= (stripos($url, '?') === false ? '?' : '&') . "access_token=" . $this->getAccessToken();
            } elseif ($token == 'params') {
                $params['access_token'] = $this->getAccessToken();
            } else {
                throw new InvalidParamException("Invalid token type '{$token}' called.");
            }
        }
        $curl = curl_init();
        switch ($type) {
            case 'get':
                is_array($params) && $params = http_build_query($params);
                !empty($params) && $url .= (stripos($url, '?') === false ? '?' : '&') . $params;
                break;
            case 'post':
                curl_setopt($curl, CURLOPT_POST, true);
                if (!is_array($params)) {
                    throw new InvalidParamException("Post data must be an array.");
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case 'raw':
                curl_setopt($curl, CURLOPT_POST, true);
                if (is_array($params)) {
                    throw new InvalidParamException("Post raw data must not be an array.");
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            default:
                throw new InvalidParamException("Invalid http type '{$type}' called.");
        }
        if (stripos($url, "https://") !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($curl);
        $status = curl_getinfo($curl);
        curl_close($curl);
        if (isset($status['http_code']) && intval($status['http_code']) == 200) {
            return $content;
        }
        return false;
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