<?php

namespace Settings\Menu;

/**
 * class for set settings
 */
class MenuConfig {

    /**
     * Location
     * @var Array 
     */
    var $loc_action = array(
        'add' => false,
        'prev' => false,
        'alt' => '/alt/',
        'del' => '/del/'
    );
    var $resolution_min = array(
        'Width' => 800,
        'Height' => 360
    );

    # plural e singular da página
    var $msg = array(
        'singular' => 'Menu',
        'plural' => 'Menu',
        'manager' => 'Configurar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );

    /**
     * Main Table
     * @var String 
     */
    var $table = 'menu';

    /**
     * Main Menu
     * @var String 
     */
    var $page = 'Menu';

    /**
     * Position for get url action
     * @var Integer
     */
    var $URL_ACTION = 3;

    /**
     *
     * @var type 
     */
    var $element = NULL;
    var $ids_tools = array(51, 49);

}
