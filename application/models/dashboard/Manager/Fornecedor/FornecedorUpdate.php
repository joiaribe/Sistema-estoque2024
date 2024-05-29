<?php

namespace Manager\Fornecedor;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

class FornecedorUpdate extends FornecedorHTML {

    /**
     * Builds page insert new registry and makes form HTML
     * @access private
     * @return object
     */
    private function _build() {
        return print $this->HTML_Update($this->Query()) . $this->_LOAD_REQUIRED_UPDATE($this->Query());
    }

    private function Query() {
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'id' => $param
                        )
                )
                ->limit(1)
                ->run();
        $data = $q->get_selected();
        $total = $q->get_selected_count();
        if (!$total > 0) {
            Call_JS::alerta('Erro na consulta');
            Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
        } else {
            return $data;
        }
    }

    /**
     * Check param for inset a new registry
     * @return Void
     */
    private function Check_Update() {
        $param = Url::getURL($this->URL_ACTION + 2);
        if ($param == 'update') {
            $this->Update_On_Database();
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
     * @return Query
     */
    private function Update_On_Database() {
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
        $q
                ->update($this->table)
                ->set(
                        array(
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
                ->where_equal_to(
                        array(
                            'id' => $param
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        Call_JS::alerta($this->msg['singular'] . " alterado com sucesso! ");
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
     * really ? I need explain ? 
     * @param mixed $value
     * @return mixed
     */
    private function check_status($value) {
        $field = filter_input(INPUT_POST, $value);
        return (isset($field) AND $field !== '') ? 1 : 0;
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
        $this->Check_Update();
        return $this->_build();
    }

}
