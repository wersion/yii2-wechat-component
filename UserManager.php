<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:04
 */

namespace iit\wechat;


class UserManager extends BaseWechatManager
{

    const LANG_CN = 'zh_CN';
    const LANG_EN = 'en';
    const LANG_TW = 'zh_TW';
    const GROUP_LIST_URL = 'https://api.weixin.qq.com/cgi-bin/groups/get';
    const GROUP_CREATE_URL = 'https://api.weixin.qq.com/cgi-bin/groups/create';
    const SELECT_USER_GROUP_URL = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
    const MODIFY_GROUP_NAME_URL = 'https://api.weixin.qq.com/cgi-bin/groups/update';
    const MOVE_USER_GROUP_URL = 'https://api.weixin.qq.com/cgi-bin/groups/members/update';
    const SET_USER_REMARK_URL = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark';
    const USER_INFO_URL = 'https://api.weixin.qq.com/cgi-bin/user/info';
    const USER_LIST_URL = 'https://api.weixin.qq.com/cgi-bin/user/get';

    public function getGroupList()
    {
        $result = $this->getWechat()->httpGet(self::GROUP_LIST_URL);
        return isset($result['errcode']) ? false : $result;
    }

    public function createGroup($name)
    {
        $result = $this->getWechat()->httpRaw(self::GROUP_CREATE_URL, $this->getWechat()->jsonEncode([
            'group' => [
                'name' => $name
            ]
        ]));
        return isset($result['errcode']) ? false : $result;
    }

    public function selectUserGroup($openid)
    {
        $result = $this->getWechat()->httpRaw(self::SELECT_USER_GROUP_URL, $this->getWechat()->jsonEncode([
            'openid' => $openid
        ]));
        return isset($result['errcode']) ? false : $result['groupid'];
    }

    public function modifyGroupName($groupid, $newName)
    {
        $result = $this->getWechat()->httpRaw(self::MODIFY_GROUP_NAME_URL, $this->getWechat()->jsonEncode([
            'group' => [
                'id' => $groupid,
                'name' => $newName
            ]
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function moveUserGroup($openid, $to_groupid)
    {
        $result = $this->getWechat()->httpRaw(self::MOVE_USER_GROUP_URL, $this->getWechat()->jsonEncode([
            'openid' => $openid,
            'to_groupid' => $to_groupid
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function setRemark($openid, $remark)
    {
        $result = $this->getWechat()->httpRaw(self::SET_USER_REMARK_URL, $this->getWechat()->jsonEncode([
            'openid' => $openid,
            'remark' => $remark
        ]));
        return $result['errcode'] == 0 ? true : false;
    }

    public function getUserInfo($openid, $lang = self::LANG_CN)
    {
        $result = Wechat::httpGet(self::USER_INFO_URL, [
            'openid' => $openid,
            'lang' => $lang
        ]);
        return isset($result['errcode']) ? false : $result;
    }

    public function getUserList($next_openid = null)
    {
        $result = Wechat::httpGet(self::USER_LIST_URL, [
            'next_openid' => $next_openid
        ]);
        return isset($result['errcode']) ? false : $result;
    }

} 