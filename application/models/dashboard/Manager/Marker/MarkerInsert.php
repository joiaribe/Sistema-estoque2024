<?php

namespace Manager\Marker;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class MarkerInsert extends MarkerHTML {

    /**
     * Builds page insert new registry and makes form HTML
     * @access private
     * @return object
     */
    private function _build() {
        return print
                $this->HTML_Insert_New() .
                $this->_LOAD_REQUIRED_INSERT();
    }

    /**
     * Check param for inset a new registry
     * @return Void
     */
    private function Check_Insert() {
        $param = Url::getURL($this->URL_ACTION + 1);
        if ($param == 'new') {
            $this->Insert_On_Database();
        }
    }


    /**
     * Insert new registry
     * @access private
     * @return void
     */
    private function Insert_On_Database() {
        $q = new Query();
        $q
                ->insert_into(
                        $this->table, array(
                    'nome' => filter_input(INPUT_POST, 'name')
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        Call_JS::alerta("Novo " . $this->msg['singular'] . " cadastrado com sucesso! ");
        Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
    }


    /**
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        $this->Check_Insert();
        return $this->_build();
    }

}
