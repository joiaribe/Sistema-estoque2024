<?php

namespace Mov\Services;

/**
 * class for set settings
 */
class ServicesConfig {

    /**
     * Location
     * @var Array 
     */
    var $loc_action = array(
        'add' => '/add/',
        'prev' => '/preview/',
        'alt' => '/alt/',
        'del' => '/del/'
    );
    var $resolution_min = array(
        'Width' => 800,
        'Height' => 360
    );

    # plural e singular da página
    var $msg = array(
        'singular' => 'Receita',
        'plural' => 'Receitas',
        'Mov' => 'Movimentar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );

    /**
     * Main Table
     * @var String 
     */
    var $table = 'input_others';

    /**
     * Main Services
     * @var String 
     */
    var $page = 'Services';

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
    var $ids_tools = array(57,7, 6);

}
