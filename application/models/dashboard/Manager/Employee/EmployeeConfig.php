<?php

namespace Manager\Employee;

/**
 * class for set settings
 */
class EmployeeConfig {

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
        'singular' => 'Funcionário',
        'plural' => 'Funcionários',
        'manager' => 'Gerenciar' //palavra que vai aparecer em ferramentas seguido pelo nome da página
    );

    /**
     * Main Table
     * @var String 
     */
    var $table = 'funcionarios';

    /**
     * Main Employee
     * @var String 
     */
    var $page = 'employee';

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
    var $ids_tools = array(14, 49);

}
