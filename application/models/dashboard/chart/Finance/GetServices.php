<?php

namespace chart\Finance;

use Query as Query;

/**
 * Class para listagem
 */
class GetServices extends FinanceJavascript {

    /**
     * Data Services
     * @var Array 
     */
    var $DataTopServices = array();

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
                            'COUNT(id_service) as total'
                        )
                )
                ->from('input_servico')
                ->where_equal_to(
                        array(
                            'status' => true
                        )
                )
                ->group_by('id_service')
                ->order_by('total desc')
                ->limit($this->limit)
                ->run();
        if ($q) {
            foreach ($q->get_selected() as $data) {
                $this->DataTopServices[$data['id_service']] = $data['total'];
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
                ->from('input_servico')
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
        foreach ($this->DataTopServices as $k => $v) {
            $ar[\Func::array_table('servicos', array('id' => $k), 'titulo')] = array('TOTAL' => $v, 'PERCENT' => $this->make_percent($v));
        }
        return $ar;
    }

    /**
     * Magic Metthod
     * Call JS for build graph
     */
    public function __construct() {
        $this->Query();
        parent::MakeTopServices($this->_data_pie_get());
    }

}
