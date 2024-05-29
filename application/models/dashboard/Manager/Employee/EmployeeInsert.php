<?php

namespace Manager\Employee;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class EmployeeInsert extends EmployeeHTML {

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
        $f = filter_input(INPUT_POST, 'assoc');
        $assoc = (isset($f) AND $f !== 'nothing') ? $f : NULL;
        // treats the case number is greater than one thousand
        $v = $this->check_field('valor');
        $real = ($v == NULL) ? NULL : str_replace('.', '', $v);
        $q = new Query();
        $q
                ->insert_into(
                        $this->table, array(
                    'nome' => filter_input(INPUT_POST, 'name'),
                    'user_id' => $assoc,
                    'Sexo' => filter_input(INPUT_POST, 'sexo'),
                    'DtNasc' => $this->check_field('nascimento'),
                    'CPF' => $this->check_field('CPF'),
                    'RG' => $this->check_field('RG'),
                    'Salario' => $real, #vai evitar bugs no number_format
                    'End' => $this->check_field('rua'),
                    'Num' => $this->check_field('numero'),
                    'Bairro' => $this->check_field('bairro'),
                    'Cidade' => $this->check_field('cidade'),
                    'UF' => $this->check_field('uf'),
                    'Cep' => $this->check_field('CEP'),
                    'Tel' => $this->check_field('tel'),
                    'Celular' => $this->check_field('cel'),
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
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        $this->Check_Insert();
        return $this->_build();
    }

}
