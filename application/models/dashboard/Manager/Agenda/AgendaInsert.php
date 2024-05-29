<?php

namespace Manager\Agenda;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;
use DateTime as DateTime;

#use \DateTime;

/**
 * Classe para visualização
 */
class AgendaInsert extends AgendaHTML {

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
     * Add interval to date timestamp
     * @param timestamp $date Datetime
     * @param String $interval Interval
     * @return type
     */
    protected function add_dates($date, $interval) {
        try {
            $dateTime = new DateTime($date);
            $dateTime->modify('+' . $interval);
            return $dateTime->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            die("Error : " . $e->getMessage());
        }
    }

    /**
     * Insert new registry
     * @access private
     * @return void
     */
    private function Insert_On_Database() {
        $date = date("Y-m-d H:i:s", strtotime(filter_input(INPUT_POST, 'horario')));
        $q = new Query();
        $q
                ->insert_into(
                        $this->table, array(
                    'id_user' => \Session::get('user_id'),
                    'id_cliente' => filter_input(INPUT_POST, 'client'),
                    'titulo' => filter_input(INPUT_POST, 'name'),
                    'description' => $this->check_field('comment'),
                    'horario' => $date,
                    'horario_end' => $this->check_field_end($date)
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
     * check time end if is null
     * @return mixed
     */
    private function check_field_end($horario) {
        $lin = filter_input(INPUT_POST, 'reserva');
        if ($lin !== 'no') {
            return $this->add_dates($horario, $lin);
        }
        return NULL;
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
