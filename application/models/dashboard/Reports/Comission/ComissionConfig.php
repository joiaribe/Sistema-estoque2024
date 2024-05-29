<?php

namespace Reports\Comission;

use Reports\Employee\ComissionActions as ComissionActions;

/**
 * class for set settings
 */
class ComissionConfig{

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
        'singular' => 'Comissão',
        'plural' => 'Comissões',
        'manager' => 'Gerenciar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );

    /**
     * Main Table
     * @var String 
     */
    var $table = 'output_servico';

    /**
     * Main Comission
     * @var String 
     */
    var $page = 'commission';

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
}
