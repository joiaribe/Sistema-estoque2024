<?php

namespace Manager\Servico;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class ServicoInsert extends ServicoHTML {

    var $pic = NULL;

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
                    'titulo' => filter_input(INPUT_POST, 'name'),
                    'descri' => $this->check_field('comment'),
                    'valor' => \Func::RealToFloat(\Request::post('valor')),
                    'comissao' => $this->check_comi('comi')
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
        $this->Check_Insert();
        return $this->_build();
    }

}
