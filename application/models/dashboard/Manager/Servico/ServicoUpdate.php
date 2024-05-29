<?php

namespace Manager\Servico;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

class ServicoUpdate extends ServicoHTML {

    /**
     * Query data results
     * @var array 
     */
    var $query = array();

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
            Call_JS::retornar(URL . 'dashboard/Manager/' . $this->page);
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
        if ($param == 'update')
            $this->Update_On_Database();
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
                            'titulo' => filter_input(INPUT_POST, 'name'),
                            'descri' => $this->check_field('comment'),
                            'valor' => \Func::RealToFloat(\Request::post('valor')),
                            'comissao' => $this->check_comi('comi')
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
     * Check commission value
     * @param float $v
     * @param array $r Replace rules
     * @return float or NULL
     */
    private function check_comi($v, $r = array(',' => '.', '%' => '')) {
        $f = filter_input(INPUT_POST, $v);
        if (isset($f) AND $f !== '') {
            foreach ($r as $k => $v) {
                $f = str_replace($k, $v, $f);
            }
            return $f;
        }
        return NULL;
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
