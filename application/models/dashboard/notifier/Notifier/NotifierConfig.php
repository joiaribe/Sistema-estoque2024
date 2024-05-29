<?php

namespace notifier\Notifier;

/**
 * class for set settings
 */
class NotifierConfig {

    /**
     * Location
     * @var Array 
     */
    var $loc_action = array(
        'add' => false,
        'prev' => '/preview/',
        'alt' => false,
        'del' => false
    );
    var $resolution_min = array(
        'Width' => 800,
        'Height' => 360
    );

    # plural e singular da página
    var $msg = array(
        'singular' => 'Notificação',
        'plural' => 'Notificações',
        'manager' => 'Gerenciar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );


    /**
     * Main Table
     * @var String 
     */
    var $table = 'notifier';

    /**
     * Main Notifier
     * @var String 
     */
    var $page = 'notifier';

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

    /**
     * submenu ids for tools
     * @var array 
     */
    var $ids_tools = array(5, 49);

}
