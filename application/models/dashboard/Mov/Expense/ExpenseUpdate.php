<?php

namespace Mov\Expense;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;
use DateTime as DateTime;

class ExpenseUpdate extends ExpenseHTML {

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
            Call_JS::retornar(URL . 'dashboard/Mov/' . $this->page);
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
     * Format date from timestamp to dd-mm-yyyy hh:mm
     * @param Datetime $data
     * @return Datetime
     */
    private function format_date($data) {
        try {
            $dateTime = new DateTime($data);
            return $dateTime->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            die("Error : " . $e->getMessage());
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
         // treats the case number is greater than one thousand
        $real = str_replace('.', '', filter_input(INPUT_POST, 'valor'));
        $q = new Query;
        $q
                ->update($this->table)
                ->set(
                        array(
                            'id_user' => \Session::get('user_id'),
                            'title' => filter_input(INPUT_POST, 'name'),
                            'descri' => $this->check_field('comment'),
                            'metthod' => filter_input(INPUT_POST, 'metthod'),
                            'card_name' => $this->check_card_name('card_name'),
                            'card_agence' => $this->check_field('agencia'),
                            'card_number' => $this->check_field('card_number'),
                            'cheque_number' => $this->check_field('cheque_number'),
                            'value' => str_replace(",", ".", $real), # vai evitar bugs no number_format,
                            'status' => $this->check_status('icheck'),
                            'data' => $this->format_date($_POST['horario'])
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
        Call_JS::retornar(URL . 'dashboard/Mov/' . $this->page);
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
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        $this->Check_Update();
        return $this->_build();
    }

}
