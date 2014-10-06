<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:04
 */

namespace iit\wechat;


class User
{

    const LANG_CN = 'zh_CN';
    const LANG_EN = 'en';
    const LANG_TW = 'zh_TW';
    const GROUP_LIST_URL = 'group_list';
    const GROUP_CREATE_URL = 'group_create';
    const SELECT_USER_GROUP_URL = 'select_user_group';
    const MODIFY_GROUP_NAME_URL = 'modify_group_name';
    const MOVE_USER_GROUP_URL = 'move_user_group';
    const SET_USER_REMARK_URL = 'set_user_remark';
    const GET_USER_INFO_URL = 'get_user_info';
    const GET_USER_LIST_URL = 'get_user_list';

    public function getGroupList()
    {
        $result = Wechat::httpGet(self::GROUP_LIST_URL);
        return isset($result['errcode']) ? false : $result;
    }

    public function createGroup($name)
    {
        $result = Wechat::httpRaw(self::GROUP_CREATE_URL, Wechat::jsonEncode([
            'group' => [
                'name' => $name
            ]
        ]));
        return isset($result['errcode']) ? false : $result;
    }

    public function selectUserGroup($openid)
    {
        $result = Wechat::httpRaw(self::SELECT_USER_GROUP_URL, Wechat::jsonEncode([
            'openid' => $openid
        ]));
        return isset($result['errcode']) ? false : $result['groupid'];
    }

    public function modifyGroupName($groupid, $newName)
    {
        $result = Wechat::httpRaw(self::MODIFY_GROUP_NAME_URL, Wechat::jsonEncode([
            'group' => [
                'id' => $groupid,
                'name' => $newName
            ]
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function moveUserGroup($openid, $to_groupid)
    {
        $result = Wechat::httpRaw(self::MOVE_USER_GROUP_URL, Wechat::jsonEncode([
            'openid' => $openid,
            'to_groupid' => $to_groupid
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function setRemark($openid, $remark)
    {
        $result = Wechat::httpRaw(self::SET_USER_REMARK_URL, Wechat::jsonEncode([
            'openid' => $openid,
            'remark' => $remark
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function getUserInfo($openid, $lang = self::LANG_CN)
    {
        $result = Wechat::httpGet(self::GET_USER_INFO_URL, [
            'openid' => $openid,
            'lang' => $lang
        ]);
        return isset($result['errcode']) ? false : $result;
    }

    public function getUserList($next_openid = null)
    {
        $result = Wechat::httpGet(self::GET_USER_LIST_URL, [
            'next_openid' => $next_openid
        ]);
        return isset($result['errcode']) ? false : $result;
    }

} 