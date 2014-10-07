<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午3:45
 */

namespace iit\wechat;


class Menu
{
    const EVENT_VIEW = 'view';
    const EVENT_CLICK = 'click';
    const EVENT_SCAN_CODE_PUSH = 'scancode_push';
    const EVENT_SCAN_CODE_WAIT = 'scancode_waitmsg';
    const EVENT_PIC_SYSPHOTO = 'pic_sysphoto';
    const EVENT_PIC_PHOTO_OR_ALBUM = 'pic_photo_or_album';
    const EVENT_PIC_WEIXIN = 'pic_weixin';
    const EVENT_LOCATION_SELECT = 'location_select';
    const TOP_MENU_LIMIT = 3;
    const SUB_MENU_LIMIT = 5;
    const CREATE_MENU_URL = 'create_menu';
    const SELECT_MENU_URL = 'select_menu';
    const DELETE_MENU_URL = 'delete_menu';
    private $_menu;

    public function __construct()
    {
        $this->_menu = $this->select(true);
    }

    public function select($remote = false)
    {
        if ($remote) {
            $result = Wechat::httpGet(self::SELECT_MENU_URL);
            $menu = isset($result['errcode']) ? [] : $result['menu']['button'];
            foreach ($menu as $key => $value) {
                if (empty($value['sub_button'])) {
                    unset($menu[$key]['sub_button']);
                }
            }
            return $menu;
        } else {
            return $this->_menu;
        }
    }

    public function addButtonGroup($name)
    {
        $this->addButton(['name' => $name, 'sub_button' => []], null);
        return $this;
    }

    public function addUrlButton($name, $url, $topName = null)
    {
        $this->addButton(['name' => $name, 'type' => 'view', 'url' => $url], $topName);
        return $this;
    }

    public function addEventButton($type, $name, $key, $topName = null)
    {
        $this->addButton(['name' => $name, 'type' => $type, 'key' => $key], $topName);
        return $this;
    }

    protected function addButton($data, $topName)
    {
        if ($topName === null) {
            if (count($this->_menu) <= self::TOP_MENU_LIMIT) {
                $this->_menu[] = $data;
            }
        } else {
            foreach ($this->_menu as $topKey => $topMenu) {
                if ($topMenu['name'] == $topName && isset($topMenu['sub_button'])) {
                    if (count($topMenu['sub_button']) <= self::SUB_MENU_LIMIT) {
                        $this->_menu[$topKey]['sub_button'][] = $data;
                    }
                }
            }
        }
    }

    public function delete($name)
    {
        foreach ($this->_menu as $topKey => $topMenu) {
            if ($topMenu['name'] == $name) {
                unset($this->_menu[$topKey]);
            } elseif (!empty($topMenu['sub_button'])) {
                foreach ($topMenu['sub_button'] as $subKey => $subMenu) {
                    if ($subMenu['name'] == $name) {
                        unset($this->_menu[$topKey]['sub_button'][$subKey]);
                    }
                }
            }
        }
        return $this;
    }

    public function deleteAll()
    {
        if ($this->_menu === []) {
            $result = Wechat::httpGet(self::DELETE_MENU_URL);
            return $result['errcode'] == 0 ? true : false;
        } else {
            $this->_menu = [];
            return $this;
        }
    }

    public function save()
    {
        if ($this->_menu === []) {
            return $this->deleteAll();
        } else {
            foreach ($this->_menu as $topKey => $topMenu) {
                if (isset($topMenu['sub_button'])) {
                    if (empty($topMenu['sub_button'])) {
                        unset($this->_menu[$topKey]);
                    }
                }
            }
            $result = Wechat::httpRaw(self::CREATE_MENU_URL, Wechat::jsonEncode([
                'button' => $this->_menu,
            ]));
            return $result['errcode'] == 0 ? true : false;
        }
    }
} 