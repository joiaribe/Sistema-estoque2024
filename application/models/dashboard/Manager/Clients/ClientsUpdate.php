<?php

namespace Manager\Clients;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

class ClientsUpdate extends ClientsHTML {

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
     * Insert new registry
     * @access private
     * @return Query
     */
    private function Update_On_Database() {
        $name = filter_input(INPUT_POST, 'name');
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
        $q
                ->update($this->table)
                ->set(
                        array(
                            'id_user' => \Session::get('user_id'),
                            'nome' => $name,
                            'agenda' => $this->check_field('agenda'),
                            'agenda_cor' => $this->check_field('agenda_font'),
                            'Sexo' => $this->check_field('sexo'),
                            'Cpf' => $this->check_field('CPF'),
                            'Rg' => $this->check_field('RG'),
                            'Cep' => $this->check_field('CEP'),
                            'End' => $this->check_address('rua'),
                            'Num' => $this->check_field('numero'),
                            'Bairro' => $this->check_address('bairro'),
                            'Cidade' => $this->check_address('cidade'),
                            'UF' => $this->check_address('uf'),
                            'Email' => filter_input(INPUT_POST, 'email'),
                            'Aniversario' => $this->verify_data(filter_input(INPUT_POST, 'nascimento')),
                            'Fone' => $this->check_field('tel'),
                            'Obs' => $this->check_field('text'),
                            'Indicacao' => $this->check_field('indicacao')
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
        Call_JS::alerta($this->msg['singular'] . ' ' . $name . " alterado com sucesso! ");
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
        $cep = filter_input(INPUT_POST, 'CEP');
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
