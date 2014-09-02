<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午2:41
 */

namespace iit\wechat;


class MenuManager
{
    const DELETE_URL = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
    const SELECT_URL = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const ADD_URL = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    private $_wechat;

    public function __construct(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    public function create(WechatMenu $menu)
    {
        $sendMenu = [];
        $tmpMenu = [];
        foreach ($menu->getAll() as $topMenu) {
            if (isset($topMenu['sub'])) {
                $tmpMenu['name'] = $topMenu['name'];
                foreach ($topMenu['sub'] as $subMenu) {
                    $tmpMenu['sub_button'][] = $menu->convertMenu($subMenu);
                }
            } else {
                $tmpMenu = $menu->convertMenu($topMenu);
            }
            $sendMenu['button'][] = $tmpMenu;
        }
        if (!empty($sendMenu)) {
            $result = $this->_wechat->httpRaw(self::ADD_URL, json_encode($sendMenu, JSON_UNESCAPED_UNICODE));
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function select()
    {
        $result = $this->_wechat->httpGet(self::SELECT_URL);
        if (!isset($result['errcode'])) {
            $menu = new WechatMenu();
            foreach ($result['menu']['button'] as $topKey => $topMenu) {
                if (empty($topMenu['sub_button'])) {
                    $menu->add('top_' . $topKey, $topMenu['type'], $topMenu['name'], ($topMenu['type'] == 'click' ? $topMenu['key'] : $topMenu['url']));
                } else {
                    $menu->add('top_' . $topKey, '', $topMenu['name'], '');
                    foreach ($topMenu['sub_button'] as $subKey => $subMenu) {
                        $menu->add('sub_' . $subKey, $subMenu['type'], $subMenu['name'], ($subMenu['type'] == 'click' ? $subMenu['key'] : $subMenu['url']), 'top_' . $topKey);
                    }
                }
            }
            return $menu;
        } else {
            return false;
        }
    }

    public function delete()
    {
        $result = $this->_wechat->httpGet(self::DELETE_URL);
        return $result['errcode'] == 0 ? true : false;
    }
}