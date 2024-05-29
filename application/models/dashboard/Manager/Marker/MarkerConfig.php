<?php

namespace Manager\Marker;

/**
 * class for set settings
 */
class MarkerConfig {

    /**
     * Location
     * @var Array 
     */
    var $loc_action = array(
        'add' => '/add/',
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
        'singular' => 'Marcador',
        'plural' => 'Marcadores',
        'manager' => 'Gerenciar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );

    /**
     * Main Table
     * @var String 
     */
    var $table = 'marcador';

    /**
     * Main Marker
     * @var String 
     */
    var $page = 'marker';

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
