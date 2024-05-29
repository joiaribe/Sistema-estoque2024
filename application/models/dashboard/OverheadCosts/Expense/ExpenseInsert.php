<?php

namespace OverheadCosts\Expense;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;
use DateTime as DateTime;
use Func as Func;

/**
 * Classe para visualização
 */
class ExpenseInsert extends ExpenseHTML {

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
            // Check demostration mode is active
            Func::CheckDemostrationMode();
            $this->Insert_On_Database();
        }
    }

    /**
     * Check card name
     * @return mixed
     */
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

    private function CheckSpecifiedDay($column) {
        $cron = filter_input(INPUT_POST, 'interval');

        if ($cron == 'monthly' && $column == 'monthly_day') {
            return filter_input(INPUT_POST, 'day');
        }

        if ($cron == 'weekly' && $column == 'weekly_day') {
            return filter_input(INPUT_POST, 'week');
        }

        if ($cron == 'daily' && $column == 'daily_hour') {
            return filter_input(INPUT_POST, 'hour');
        }

        return NULL;
    }

    /**
     * Insert new registry
     * @access private
     * @return void
     */
    private function Insert_On_Database() {
        // treats the case number is greater than one thousand
        $real = str_replace('.', '', filter_input(INPUT_POST, 'valor'));
        $q = new Query();
        $q
                ->insert_into(
                        $this->table, array(
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
                    'cron_time' => filter_input(INPUT_POST, 'interval'),
                    'monthly_day' => $this->CheckSpecifiedDay('monthly_day'),
                    'weekly_day' => $this->CheckSpecifiedDay('weekly_day'),
                    'daily_hour' => $this->CheckSpecifiedDay('daily_hour'),
                    'data' => $this->format_date($_POST['horario'])
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        Call_JS::alerta("Novo " . $this->msg['singular'] . " cadastrado com sucesso! ");
        Call_JS::retornar(URL . 'dashboard/OverheadCosts/' . $this->page);
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