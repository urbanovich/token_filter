<?php

/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*         DISCLAIMER   *
* *************************************** */

/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* *****************************************************
* @category   Belvg
* @package    Page.php
* @author     Dzmitry Urbanovich (urbanovich.mslo@gmail.com)
* @site       http://module-presta.com
* @copyright  Copyright (c) 2007 - 2016 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

class TokenFilter extends ObjectModel
{

    public $id;

    public $id_token_filter;

    public $title;

    public $content;

    public $date_add;

    public $date_upd;

    public $active;

    public $name;

    public static $definition = array(
        'table' => 'token_filter',
        'primary' => 'id_token_filter',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'date_add' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'active' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'name' =>               array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),

            //lang fields
            'title' =>              array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'content' =>            array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getAllTokens()
    {
        $result = array();
        $context = Context::getContext();

        $tokens = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'token_filter AS tf
                                                INNER JOIN ' . _DB_PREFIX_ . 'token_filter_lang tfl
                                                ON (tfl.id_token_filter = tf.id_token_filter
                                                    AND tfl.id_shop = ' . $context->shop->id . ')
                                                WHERE tf.active = 1;');

        foreach($tokens as $token)
        {
            $result[$token['name']] = $token;
        }

        return $result;
    }

}