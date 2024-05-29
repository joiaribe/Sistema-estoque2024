<?php

namespace chart\Finance;

use Query as Query;

/**
 * Class para listagem
 */
class GetInventoryControl extends FinanceJavascript {

    var $filter = array(
        'daily',
        'weekly',
        'monthly',
        'yearly'
    );

    /**
     * Data Chart total income
     * @var array 
     */
    var $DataChartInventory = array();

    /**
     * Data Chart total expense
     * @var array 
     */
    var $DataChartInventoryD = array();

    /**
     * Data Chart total profit
     * @var array 
     */
    var $DataChartInventoryL = array();

    /**
     * Data Chart Names xaxis and yxaxis
     * @var array 
     */
    var $DataChartInventoryN = array();

    /**
     * Limit no filter
     * @var integer 
     */
    var $limitNoFilter = 14;

    /**
     * Get current date and add x days
     * @return Timestamp
     */
    private function get_date($i, $format = 'Y-m-d') {
        $date = date('Y-m-d H:i:s');
        $xmasDay = new \DateTime("$date - $i day");
        return $xmasDay->format($format);
    }

    private function calculate_diff() {
        $data1 = new \DateTime('2013-12-11');
        $data2 = new \DateTime('1994-04-17');

        $intervalo = $data1->diff($data2);
        return $intervalo;
    }

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query($type = 'input') {
        for ($i = 0; $i <= $this->limitNoFilter; $i++) {
            $date = $this->get_date($i);
            $total_others = \Func::_sum_values($type . '_others', 'value', array('date(data)' => $this->get_date($i)));
            $total_services = \Func::_sum_values($type . '_servico', 'value', array('date(data)' => $this->get_date($i)));
            $total_products = \Func::_sum_values($type . '_product', 'value', array('date(data)' => $this->get_date($i)));
            $subtotal = $total_products + $total_services + $total_others;
            if ($type == 'input') {
                $this->DataChartInventory[strtotime($date) * 1000] = $subtotal;
            } elseif ($type == 'output') {
                $this->DataChartInventoryD[strtotime($date) * 1000] = $subtotal;
            }
        }
    }

    protected function QueryLucro() {
        for ($i = 0; $i <= $this->limitNoFilter; $i++) {
            $date = $this->get_date($i);
            $total_others = \Func::_sum_values('input_others', 'value', array('date(data)' => $this->get_date($i)));
            $total_services = \Func::_sum_values('input_servico', 'value', array('date(data)' => $this->get_date($i)));
            $total_products = \Func::_sum_values('input_product', 'value', array('date(data)' => $this->get_date($i)));
            $total = $total_products + $total_services + $total_others;

            $stotal_others = \Func::_sum_values('output_others', 'value', array('date(data)' => $this->get_date($i)));
            $stotal_services = \Func::_sum_values('output_servico', 'value', array('date(data)' => $this->get_date($i)));
            $stotal_products = \Func::_sum_values('output_product', 'value', array('date(data)' => $this->get_date($i)));
            $subtotal = $stotal_products + $stotal_services + $stotal_others;
            $this->DataChartInventoryL[strtotime($date) * 1000] = $total - $subtotal;
        }
       # var_dump($this->DataChartInventoryL);
    }

    protected function Queryticks() {
        for ($i = 0; $i <= $this->limitNoFilter; $i++) {
            $date = $this->get_date($i);
            $formated = strftime('%d %b', strtotime($date));
            $this->DataChartInventoryN["'lolo $i'"] = "'$formated'";
        }
    }

    protected function QueryFilter($start, $end) {
        $data = array();
        for ($i = 0; $i <= $this->limitNoFilter; $i++) {
            $date = $this->get_date($i);
            $total = \Func::_sum_values($table, 'value', array('date(date_history)' => $this->get_date($i)));
            $data[] = $total;
        }
        $this->DataChartInventory = $data;
    }

    public function __construct() {
        $action = filter_input(INPUT_GET, 'invetory');
        $start = filter_input(INPUT_GET, 'invetory_start');
        $end = filter_input(INPUT_GET, 'invetory_end');
        $interval = filter_input(INPUT_GET, 'invetory_interval');

        if (isset($action) && $action == 'filter' && isset($start) && isset($end)) {
            // run query
            $this->QueryFilter($start, $end);
        } else {
            // run query
            $this->Query();
            $this->Query('output');
            $this->QueryLucro();
            $this->Queryticks();
        }
        parent::MakeInventory();
    }

}
