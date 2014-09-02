<?php
/**
 * Created by PhpStorm.
 * User: 俊杰
 * Date: 14-9-1
 * Time: 下午3:45
 */

namespace iit\wechat;


use yii\base\InvalidParamException;

class WechatMenu
{
    const TOP_MENU_LIMIT = 3;
    const SUB_MENU_LIMIT = 5;
    private $_menu;


    public function getCount($key = null)
    {
        $count = 0;
        foreach ($this->_menu as $menu) {
            $menu['top'] === null && $count++;
        }
        return $count;
    }

    public function add($key, $type, $name, $value, $top = null)
    {
        if (isset($this->_menu[$key])) {
            throw new InvalidParamException('Existing key,Pleas Use Edit Function');
        } elseif ($top !== null && !isset($this->_menu[$top])) {
            throw new InvalidParamException('Not Found Top Key');
        } else {
            $this->_menu[$key] = ['type' => $type, 'name' => $name, 'value' => $value, 'top' => $top];
        }
    }

    public function edit($key, $type, $name, $value, $top = null)
    {
        if (isset($this->_menu[$key])) {
            $this->_menu[$key] = ['type' => $type, 'name' => $name, 'value' => $value, 'top' => $top];
            return true;
        } else {
            return false;
        }
    }

    public function delete($key)
    {
        unset($this->_menu[$key]);
        return true;
    }

    public function get($key)
    {
        return isset($this->_menu[$key]) ? : false;
    }

    public function getAll()
    {
        $menuList = [];
        foreach ($this->_menu as $key => $menu) {
            if ($menu['top'] === null) {
                $menuList[$key] = $menu;
            }
        }
        foreach ($this->_menu as $key => $menu) {
            if ($menu['top'] !== null) {
                $menuList[$menu['top']]['sub'][$key] = $menu;
            }
        }
        return $menuList;
    }

    public function convertMenu(array $menu)
    {
        $return['name'] = $menu['name'];
        $return['type'] = $menu['type'];
        switch ($menu['type']) {
            case 'click':
                $return['key'] = $menu['value'];
                break;
            case 'view':
                $return['url'] = $menu['value'];
                break;
            default:
                throw new InvalidParamException('Unkown Menu Type');
        }
        return $return;
    }
} 