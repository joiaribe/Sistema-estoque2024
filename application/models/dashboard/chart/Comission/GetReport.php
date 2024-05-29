<?php

namespace chart\Comission;

use Query as Query;

class GetReport extends ComissionJavascript {

    var $Data1ChartReport = array();
    var $Data2ChartReport = array();
    var $limit_in_days = 31;

    /**
     * Get current date and add x days
     * @return Timestamp
     */
    private function get_date($i, $format = 'Y-m-d  ') {
        $date = date('Y-m-d H:i:s');
        $xmasDay = new \DateTime("$date - $i day");
        return $xmasDay->format($format);
    }

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query($type = 'input') {

        for ($i = 0; $i <= $this->limit_in_days; $i++) {
            $date = $this->get_date($i);
            $where = array(
                'date(data)' => $this->get_date($i),
                'status' => true
            );
            $total_services = \Func::_sum_values('output_servico', 'value', $where);
            $subtotal = $total_services;

            $this->Data1ChartReport[strtotime($date) * 1000] = $subtotal;
        }
    }

    protected function QueryOut() {
        for ($i = 0; $i <= $this->limit_in_days; $i++) {
            $date = $this->get_date($i);
            $where = array(
                'date(data)' => $this->get_date($i),
                'status' => false
            );
            $total_services = \Func::_sum_values('output_servico', 'value', $where);
            $subtotal = $total_services;
            $this->Data2ChartReport[strtotime($date) * 1000] = $subtotal;
        }
    }

    public function __construct() {
        // run query
        $this->Query();
        $this->QueryOut();
        parent::MAKE_GRAPH_REPORT($this->Data1ChartReport, $this->Data2ChartReport);
    }

}
