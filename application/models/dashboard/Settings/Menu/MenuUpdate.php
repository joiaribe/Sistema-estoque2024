<?php

namespace Settings\Menu;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

class MenuUpdate extends MenuHTML {

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
            Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
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
     * Change position
     * @param Integer $id
     */
    private function change_position($id, $position) {
        $q = new Query;
        $q
                ->update($this->table)
                ->set(
                        array(
                            'position' => $id
                        )
                )
                ->where_equal_to(
                        array(
                            'position' => $position
                        )
                )
                ->limit(1)
                ->run();
    }

    private function get_position_before() {
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
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
        return $data['position'];
    }

    /**
     * check position exists, if exist replace
     * @param Integer $id
     * @return Integer
     */
    private function check_position($id) {
        $q = new Query;
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'position' => $id
                        )
                )
                ->limit(1)
                ->run();
        $data = $q->get_selected();
        $count = $q->get_selected_count();
        if (!($count == 1)) {
            return $id;
        } else {
            $this->change_position($this->get_position_before(), $data['position']);
            return $id;
        }
    }

    /**
     * Insert new registry
     * @access private
     * @return Query
     */
    private function Update_On_Database() {
        if (DEMOSTRATION) {
            Call_JS::alerta("Essa alteração não é permitida no modo demostrativo");
            Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
        }
        $name = filter_input(INPUT_POST, 'name');
        $param = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
        $q
                ->update($this->table)
                ->set(
                        array(
                            'status' => $this->check_status('status'),
                            'sub' => filter_input(INPUT_POST, 'submenu'),
                            'name' => $name,
                            'link' => filter_input(INPUT_POST, 'link'),
                            'icone' => filter_input(INPUT_POST, 'icone'),
                            'position' => $this->check_position(filter_input(INPUT_POST, 'position'))
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
        Call_JS::retornar(URL . 'dashboard/Settings/' . $this->page);
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
