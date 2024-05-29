<?php

namespace Manager\Fonts;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

class FontsUpdate extends FontsHTML {

    var $criter = array();

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
                            'Id' => $param
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
            $this->CheckCriter();
            $this->Update_On_Database();
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
     * @return Query
     */
    private function Update_On_Database() {
        $name = filter_input(INPUT_POST, 'name');
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
        $q
                ->update($this->table)
                ->set($this->criter)
                ->where_equal_to(
                        array(
                            'Id' => $param
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
