<?php

namespace Manager\Fonts;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class FontsInsert extends FontsHTML {

    var $criter = array();

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
            $this->CheckCriter();
            $this->Insert_On_Database();
        }
    }

    /**
     * Check criter for insert
     * @return void
     */
    private function CheckCriter() {
        $bank = $this->check_field('bank');
        $wallet = $this->check_field('codWallet');
        $this->criter['titulo'] = $this->check_field('name');
        $this->criter['banco'] = $bank;
        $this->criter['agencia'] = $this->check_field('agence');
        $this->criter['conta'] = $this->check_field('account');
        $this->criter['carteira'] = $wallet;


        if ($bank == 'Banco Do Brasil') {
            $this->criter['Convenio'] = $this->check_field('codAgreement');
        }

        if ($bank == 'Itau' &&
                $wallet == 107 ||
                $wallet == 122 ||
                $wallet == 142 ||
                $wallet == 143 ||
                $wallet == 196 ||
                $wallet == 198
        ) {
            $this->criter['codigoCliente'] = $this->check_field('codClient');
            $this->criter['numeroDocumento'] = $this->check_field('numDocument');
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
                        $this->table, $this->criter
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        Call_JS::alerta("Nova " . $this->msg['singular'] . " cadastrado com sucesso! ");
        Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
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
