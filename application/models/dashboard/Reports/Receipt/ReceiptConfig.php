<?php

namespace Manager\Receipt;

/**
 * class for set settings
 */
class ReceiptConfig {

    /**
     * Location
     * @var Array 
     */
    var $loc_action = array(
        'add' => false,
        'prev' => '/preview/',
        'alt' => false,
        'del' => '/del/'
    );
    
    # plural e singular da página
    var $msg = array(
        'singular' => 'Recibo',
        'plural' => 'Recibos',
        'manager' => 'Gerenciar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );

    /**
     * Main Table
     * @var String 
     */
    var $table = 'recibos';

    /**
     * Main Receipt
     * @var String 
     */
    var $page = 'receipt';

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
