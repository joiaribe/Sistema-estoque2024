<?php

namespace Manager\Fornecedor;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class FornecedorInsert extends FornecedorHTML {

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
     * Check CNPJ
     * @return mixed
     */
    private function check_cnpj() {
        $c = filter_input(INPUT_POST, 'icheck');
        $cpnj = filter_input(INPUT_POST, 'cpnj');
        if (!isset($c)) {
            return isset($cpnj) ? $cpnj : NULL;
        }
        return NULL;
    }

    /**
     * Check CPF
     * @return mixed
     */
    private function check_cpf() {
        $c = filter_input(INPUT_POST, 'icheck');
        $cpf = filter_input(INPUT_POST, 'CPF');
        if ($c == 'on') {
            return isset($cpf) ? $cpf : NULL;
        }
        return NULL;
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
                    'empresa' => filter_input(INPUT_POST, 'name'),
                    'cpf' => $this->check_cpf(),
                    'cpnj' => $this->check_cnpj(),
                    'Cep' => $this->check_field('cep'),
                    'End' => $this->check_address('rua'),
                    'Num' => $this->check_field('numero'),
                    'Bairro' => $this->check_address('bairro'),
                    'Cidade' => $this->check_address('cidade'),
                    'UF' => $this->check_address('uf'),
                    'email' => filter_input(INPUT_POST, 'email'),
                    'fone' => $this->check_field('tel')
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
     * really ? I need explain ? 
     * @param mixed $value
     * @return mixed
     */
    private function check_field($value) {
        $field = filter_input(INPUT_POST, $value);
        return (isset($field) AND $field !== '') ? $field : NULL;
    }

    /**
     * check link is null
     * @param mixed $value
     * @return mixed
     */
    private function check_field_target($value) {
        $lin = filter_input(INPUT_POST, 'question');
        if (isset($lin) AND $lin !== '') {
            return filter_input(INPUT_POST, $value);
        } else {
            return NULL;
        }
    }

    /**
     * Check Address field
     * @param mixed $data Name of field group address info
     * @return mixed
     */
    private function check_address($data) {
        $cep = filter_input(INPUT_POST, 'cep');
        if (empty($cep)) {
            return NULL;
        }
        return filter_input(INPUT_POST, $data);
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
