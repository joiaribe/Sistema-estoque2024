<?php

namespace Mov\Services;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class ServicesInsert extends ServicesHTML {

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

    private function check_card_name() {
        $metthod = filter_input(INPUT_POST, 'metthod');
        $card = filter_input(INPUT_POST, 'card_name');
        if ($metthod !== 'Cartão de Crédito' && $metthod !== 'Débito Automático') {
            return NULL;
        } else {
            return $card;
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
                    'id_poster' => \Session::get('user_id'),
                    'title' => filter_input(INPUT_POST, 'name'),
                    'descri' => $this->check_field('comment'),
                    'metthod' => filter_input(INPUT_POST, 'metthod'),
                    'card_name' => $this->check_card_name('card_name'),
                    'card_agence' => $this->check_field('agencia'),
                    'card_number' => $this->check_field('card_number'),
                    'cheque_number' => $this->check_field('cheque_number'),
                    'value' => str_replace(",", ".", filter_input(INPUT_POST, 'valor')), # vai evitar bugs no number_format,
                    'status' => $this->check_status('icheck')
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        Call_JS::alerta("Novo " . $this->msg['singular'] . " cadastrado com sucesso! ");
        Call_JS::retornar(URL . 'dashboard/Mov/' . $this->page);
    }

    /**
     * really ? I need explain ? 
     * @param mixed $value
     * @return mixed
     */
    private function check_field($value) {
        $field = filter_input(INPUT_POST, $value);
        return (isset($field) AND $field !== '') ? $field : NULL;
    }

    private function check_status($status) {
        $s = filter_input(INPUT_POST, $status);
        return isset($s) ? true : false;
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
