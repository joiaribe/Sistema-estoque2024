<?php

namespace chart\Finance;

use Query as Query;

class GetReport extends FinanceJavascript {

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
            $total_others = \Func::_sum_values($type . '_others', 'value', array('date(data)' => $this->get_date($i)));
            $total_services = \Func::_sum_values($type . '_servico', 'value', array('date(data)' => $this->get_date($i)));
            $total_products = \Func::_sum_values($type . '_product', 'value', array('date(data)' => $this->get_date($i)));
            $subtotal = $total_products + $total_services + $total_others;
            #$date = $this->get_date($i,'d-m');
            if ($type == 'input') {
                $this->Data1ChartReport[strtotime($date) * 1000] = $subtotal;
            } elseif ($type == 'output') {
                $this->Data2ChartReport[strtotime($date) * 1000] = $subtotal;
            }
        }
    }

    public function __construct() {
        // run query
        $this->Query();
        $this->Query('output');
        parent::MAKE_GRAPH_REPORT($this->Data1ChartReport, $this->Data2ChartReport);
    }

}
