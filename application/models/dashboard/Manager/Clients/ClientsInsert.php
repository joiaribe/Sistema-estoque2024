<?php

namespace Manager\Clients;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class ClientsInsert extends ClientsHTML {

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
                    'id_user' => \Session::get('user_id'),
                    'nome' => filter_input(INPUT_POST, 'name'),
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
        $this->Check_Insert();
        return $this->_build();
    }

}
