<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 2014/9/29
 * Time: 22:32
 */

namespace iit\wechat;


class Url
{
    const LONG_TO_SHORT_URL = 'url_long_to_short';
    const API_URL_SSL = 'https://api.weixin.qq.com';
    const FILE_API_URL_SSL = 'https://file.api.weixin.qq.com';
    const FILE_API_URL = 'http://file.api.weixin.qq.com';
    const OPEN_API_URL = 'https://open.weixin.qq.com';
    const MP_URL_SSL = 'https://mp.weixin.qq.com';
    const CGI = '/cgi-bin';
    const OAUTH2 = '/sns/oauth2';

    public static function get($type)
    {
        $urlList = [
            'access_token' => self::API_URL_SSL . self::CGI . '/token?grant_type=client_credential',
            'mediaUpload' => self::FILE_API_URL . self::CGI . '/media/upload',
            'mediaDownload' => self::FILE_API_URL . self::CGI . '/media/get',
            'serviceMessage' => self::API_URL_SSL . self::CGI . '/message/custom/send',
            'customService' => self::API_URL_SSL . self::CGI . '/customservice/getrecord',
            'oauth_url' => self::OPEN_API_URL . '/connect/oauth2/authorize',
            'oauth_access_token' => self::API_URL_SSL . self::OAUTH2 . '/access_token?grant_type=authorization_code',
            'oauth_refresh_token' => self::API_URL_SSL . self::OAUTH2 . '/refresh_token?grant_type=refresh_token',
            'mass_upload_news' => self::API_URL_SSL . self::CGI . '/media/uploadnews',
            'mass_upload_video' => self::FILE_API_URL_SSL . self::CGI . '/media/uploadvideo',
            'mass_group_send' => self::API_URL_SSL . self::CGI . '/message/mass/sendall',
            'mass_openid_send' => self::API_URL_SSL . self::CGI . '/message/mass/send',
            'mass_delete' => self::API_URL_SSL . self::CGI . '/message/mass/delete',
            'group_list' => self::API_URL_SSL . self::CGI . '/groups/get',
            'group_create' => self::API_URL_SSL . self::CGI . '/groups/create',
            'select_user_group' => self::API_URL_SSL . self::CGI . '/groups/getid',
            'modify_group_name' => self::API_URL_SSL . self::CGI . '/groups/update',
            'move_user_group' => self::API_URL_SSL . self::CGI . '/groups/members/update',
            'set_user_remark' => self::API_URL_SSL . self::CGI . '/user/info/updateremark',
            'get_user_info' => self::API_URL_SSL . self::CGI . '/user/info',
            'get_user_list' => self::API_URL_SSL . self::CGI . '/user/get',
            'url_long_to_short' => self::API_URL_SSL . self::CGI . '/shorturl',
            'create_tick' => self::API_URL_SSL . self::CGI . '/qrcode/create',
            'show_qrcode' => self::MP_URL_SSL . self::CGI . '/showqrcode',
        ];
        return isset($urlList[$type]) ? $urlList[$type] : false;
    }

    public static function longToShort($longUrl)
    {
        $result = Wechat::httpPost(self::LONG_TO_SHORT_URL, [
            'action' => 'long2short',
            'long_url' => $longUrl
        ]);
        return $result['errcode'] == 0 ? $result['short_url'] : false;
    }
} 