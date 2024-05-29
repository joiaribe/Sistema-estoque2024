<?php

namespace Manager\Agenda;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;
use DateTime as DateTime;

class AgendaUpdate extends AgendaHTML {

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
        $date = date("Y-m-d H:i:s", strtotime(filter_input(INPUT_POST, 'horario')));
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
        $q
                ->update($this->table)
                ->set(
                        array(
                            'id_user' => \Session::get('user_id'),
                            'id_cliente' => filter_input(INPUT_POST, 'client'),
                            'titulo' => filter_input(INPUT_POST, 'name'),
                            'description' => $this->check_field('comment'),
                            'horario' => $date,
                            'horario_end' => $this->check_field_end($date)
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
        $this->Check_Update();
        return $this->_build();
    }

}
