<?php

namespace Reports\CashFlow;

use \Query as Query;
use DateTime as DateTime;

/**
 * Class para listagem
 */
class CashFlowMain extends CashFlowHTML {

    /**
     * data criteria to filter where equal to.
     * used to get data via get method
     * @var array 
     */
    private $equal = array();

    /**
     * data criteria to filter where between.
     * used to get data via get method
     * @var array 
     */
    private $between = array();

    /**
     * format the date format for the timestamp
     * @access protected
     * @param DateTime $date Date in format dd/mm/YYYY
     * @param array $rep Replace rules
     * @return Timestamp
     * */
    protected function verify_data($date, $rep = array('/', '-')) {
        try {
            $final = str_replace($rep[0], $rep[1], $date);
            $dateTime = new DateTime($final);

            return $dateTime->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * validate value if exists
     * @param array $data
     * @return boolean
     */
    private function check_exists(array $data) {
        $erros = array();
        foreach ($data as $value) {
            if (empty($value)) {
                $erros[] = false;
            }
        }

        if (in_array(false, $erros)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check criter filter baseade on param get
     * @access private
     * @return void
     */
    private function check_filter() {
        $min = filter_input(INPUT_GET, 'min');
        $max = filter_input(INPUT_GET, 'max');
        $from = $this->verify_data(filter_input(INPUT_GET, 'from'));
        $to = $this->verify_data(filter_input(INPUT_GET, 'to'));
        $user = filter_input(INPUT_GET, 'poster');

        if (!empty($user)) {
            $this->equal = array(
                'id_user' => $user
            );
        } else {
            $this->equal = '';
        }
        // checks if all values are filled
        if ($this->check_exists(array($min, $max, $from, $to))) {
            $this->between = array(
                'value' => array($min, $max),
                'data' => array($from, $to)
            );
        } else {
            // checks value min and max​are filled
            if ($this->check_exists(array($min, $max))) {
                $this->between = array(
                    'value' => array($min, $max)
                );
            }
            // checks values to date ranger​are filled
            if ($this->check_exists(array($from, $to))) {
                $this->between = array(
                    'data' => array($from, $to)
                );
            }
            if (!isset($min, $max, $from, $to)) {
                $this->between = '';
            }
        }
    }

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query($table) {
        $q = new Query();
        $q
                ->select()
                ->from($table)
                ->where_equal_to($this->equal)
                ->where_between($this->between)
                ->run();
        $result = '';
        if ($q) {
            foreach ($q->get_selected() as $data) {
                $sumary = !isset($this->sumary_reports[$table]) ? 0 : $this->sumary_reports[$table];
                
                $this->sumary_reports[$table] = $sumary + $data['value'];


                if (($table == 'input_product' || $table == 'input_others' || $table == 'input_servico') && $data['status'] == true) {
                    $this->total_brute = $this->total_brute + $data['value'];
                } else {
                    $this->total_expense = $this->total_expense + $data['value'];
                }

                $result.= $this->contain_table($data, $table);
            }
            return $result;
        }
    }

    /**
     * controi classe método mágico
     * @access public
     * @return main
     */
    public function __construct() {
        $this->check_filter();
        $elements = $this->Query('input_others') .
                $this->Query('input_product') .
                $this->Query('input_servico') .
                $this->Query('output_others') .
                $this->Query('output_product') .
                $this->Query('output_servico');
        return print
                $this->Widget($elements) .
                $this->_LOAD_REQUIRED_MAIN();
    }

}
