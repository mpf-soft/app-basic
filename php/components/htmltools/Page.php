<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 8/11/14
 * Time: 11:42 AM
 */

namespace app\components\htmltools;


use mpf\base\TranslatableObject;
use mpf\web\helpers\Html;
use mpf\WebApp;

class Page extends TranslatableObject {

    private static $instance;

    /**
     * Get an instance of current class; Takes care that it will always return the same instance
     * @return static
     */
    public static function get() {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Get title + with submenu option
     * @param $title
     * @param array $submenu
     */
    public static function title($title, $submenu = array(), $htmlOptions = array(), $menuHtmlOptions = array()) {
        if (!isset($htmlOptions['class'])) {
            $htmlOptions['class'] = '';
        }
        $htmlOptions['class'] .= ' page-title';
        return Html::get()->tag('h1', Html::get()->tag('b', self::get()->translate($title)) . self::menuList($submenu, $menuHtmlOptions), $htmlOptions);
    }

    /**
     * Get a HTML list of links used for menus
     * @param $items
     * @param array $htmlOptions
     * @return string
     */
    public static function menuList($items, $htmlOptions = array()) {
        $lis = array();
        foreach ($items as $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            if (is_array($item['url'])) {
                if (WebApp::get()->accessMap && !WebApp::get()->accessMap->canAccess($item['url'][0], isset($item['url'][1]) ? $item['url'][1] : null, isset($item['url'][2]) ? $item['url'][2] : null)) {
                    continue;
                }
            }
            $url = is_array($item['url']) ? WebApp::get()->request()->createURL($item['url'][0], isset($item['url'][1]) ? $item['url'][1] : null, isset($item['url'][2]) ? $item['url'][2] : array(), isset($item['url'][3]) ? $item['url'][3] : null) : $item['url'];
            $icon = isset($item['icon']) ? Html::get()->image($item['icon'], self::get()->translate(isset($item['title']) ? $item['title'] : (isset($item['label']) ? $item['label'] : '')), isset($item['iconHtmlOptions']) ? $item['iconHtmlOptions'] : array()) : '';
            $itemContent = Html::get()->link($url, $icon . self::get()->translate(isset($item['label']) ? $item['label'] : ''), isset($item['linkHtmlOptions']) ? $item['linkHtmlOptions'] : array());
            $lis[] = Html::get()->tag('li', $itemContent, isset($item['htmlOptions']) ? $item['htmlOptions'] : array());
        }

        return count($lis) ? Html::get()->tag('ul', implode("\n", $lis), $htmlOptions) : '';
    }

} 