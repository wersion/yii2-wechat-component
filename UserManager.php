<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:04
 */

namespace iit\wechat;


class UserManager
{

    const GROUP_LIST_URL = 'https://api.weixin.qq.com/cgi-bin/groups/get';
    const GROUP_CREATE_URL = 'https://api.weixin.qq.com/cgi-bin/groups/create';
    const SELECT_USER_GROUP_URL = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
    const MODIFY_GROUP_NAME_URL = 'https://api.weixin.qq.com/cgi-bin/groups/update';
    private $_wechat;

    public function __construct(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    public function getGroupList()
    {
        $result = $this->_wechat->httpGet(self::GROUP_LIST_URL);
        return isset($result['errcode']) ? false : $result;
    }

    public function createGroup($name)
    {
        $result = $this->_wechat->httpRaw(self::GROUP_CREATE_URL, json_encode(['group' => ['name' => $name]]));
        return isset($result['errcode']) ? false : $result;
    }

    public function selectUserGroup($openid)
    {

    }

    public function modifyGroupName($groupid)
    {

    }

    public function moveUserGroup($openid, $to_groupid)
    {

    }

    public function setRemark($openid, $remark)
    {

    }

    public function getUserInfo($openid)
    {

    }

    public function getUserList($next_openid = null)
    {

    }

} 