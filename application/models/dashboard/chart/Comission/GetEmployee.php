<?php

namespace chart\Comission;

use Query as Query;

/**
 * Class para listagem
 */
class GetEmployee extends ComissionJavascript {

    /**
     * Data Services
     * @var Array 
     */
    var $DataTopEmployee = array();

    /**
     * Limit Reg
     * @var Int 
     */
    var $limit = 3;

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query() {
        $q = new Query;
        $q
                ->select(
                        array(
                            '*',
                            'COUNT(id_client) as total'
                        )
                )
                ->from('output_servico')
                ->where_equal_to(
                        array(
                            'status' => true
                        )
                )
                ->group_by('id_client')
                ->order_by('total desc')
                ->limit($this->limit)
                ->run();
        if ($q) {
            foreach ($q->get_selected() as $data) {
                $this->DataTopEmployee[$data['id_client']] = $data['total'];
            }
        }
    }

    /**
     * Make a percent
     * @param int $data
     * @return float
     */
    private function make_percent($data) {
        $q = new Query;
        $q
                ->select()
                ->from('output_servico')
                ->run();

        $total = $q->get_selected_count();
        $result = ($data * 100) / $total;
        return round($result, 2);
    }

    /**
     * making a treatment of data
     * Search name and does percentage
     * @return type
     */
    private function _data_pie_get() {
        $ar = array();
        foreach ($this->DataTopEmployee as $k => $v) {
            $ar[\Func::array_table('clientes', array('id' => $k), 'nome')] = array('TOTAL' => $v, 'PERCENT' => $this->make_percent($v));
        }
        return $ar;
    }

    /**
     * Magic Metthod
     * Call JS for build graph
     */
    public function __construct() {
        $this->Query();
        parent::MakeTopEmployee($this->_data_pie_get());
    }

}
