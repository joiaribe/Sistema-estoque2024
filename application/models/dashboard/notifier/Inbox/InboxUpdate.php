<?php

namespace notifier\Inbox;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

class InboxUpdate extends InboxHTML {

    /**
     * Query data results
     * @var array 
     */
    var $query = array();

    /**
     * product image
     * @var string 
     */
    var $pic = NULL;

    /**
     * Builds page insert new registry and makes form HTML
     * @access private
     * @return object
     */
    private function _build() {
        $this->Query();
        $this->Check_Update();
        return print
                $this->HTML_Update($this->query) .
                $this->_LOAD_REQUIRED_UPDATE($this->query);
    }

    /**
     * Build Query
     * @return Void
     */
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
            Call_JS::retornar(URL . 'dashboard/Mov/' . $this->page);
        } else {
            $this->query = $data;
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
     * Insert new registry
     * @access private
     * @return Query
     */
    private function Update_On_Database() {
        $param = Url::getURL($this->URL_ACTION + 1);
        $f = filter_input(INPUT_POST, 'assoc');
        $assoc = (isset($f) AND $f !== 'nothing') ? $f : NULL;
        // treats the case number is greater than one thousand
        $v = $this->check_field('valor');
        $real = ($v == NULL) ? NULL : str_replace('.', '', $v);
        $q = new Query;
        $q
                ->update($this->table)
                ->set(array(
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
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        return $this->_build();
    }

}
