<?php

namespace Mov\Income;

use \Query as Query;

/**
 * Class para listagem
 */
class IncomeListingFilter extends IncomeHTML {

    private $equal = array();
    private $between = array();
    private $between_d = array();

    /**
     * format the date format for the timestamp
     * @access protected
     * @param DateTime $date Date in format dd/mm/YYYY
     * @param array $rep Replace rules
     * @return Timestamp
     * */
    protected function verify_data($date, $rep = array('/', '-')) {
        if ($date == '') {
            return '';
        }
        try {
            $final = str_replace($rep[0], $rep[1], $date);
            $dateTime = new \DateTime($final);

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
            if ($value == '') {
                $erros[] = false;
            }
        }
        #var_dump($erros);
        if (in_array(false, $erros)) {
            return false;
        } else {
            return true;
        }
    }

    private function check_filter() {
        $min = filter_input(INPUT_GET, 'min');
        $max = filter_input(INPUT_GET, 'max');
        $from = $this->verify_data(filter_input(INPUT_GET, 'from'));
        $to = $this->verify_data(filter_input(INPUT_GET, 'to'));
        $user = filter_input(INPUT_GET, 'poster');


        switch (true) {
            // value ranger
            case ($min !== '' && $max !== ''):
                #  echo '<pre>pego aki</pre>';
                $this->between = array('value' => array($min, $max));
                break;
            case ($min !== '' && $max == '' && isset($min)):
                $this->between = array('value' => array($min, 1000000)); // poder de luta de freeza
                break;
            case ($min == '' && $max !== '' && isset($max)):
                $this->between = array('value' => array(0, $max));
                break;
            case ($min == '' && $max == ''):
                $this->between = false;
                break;
            default:
                break;
        }

        switch (true) {
            //date ranger
            case ($from !== '' && $to !== ''):
                $this->between_d = array('data' => array($from, $to));
                break;
            case ($from !== '' && $to == '' && isset($from)):
                $this->between_d = array('data' => array($from, date('Y-m-d H:i:s', strtotime('+8000 week'))));
                break;
            case ($from == '' && $to !== '' && isset($to)):
                $this->between_d = array('data' => array(date('Y-m-d H:i:s', strtotime('-8000 week')), $to)); // mais de 8000
                break;
            case ($from == '' && $to == ''):
                $this->between_d = false;
                break;
            default:
                break;
        }

        switch (true) {
            //user
            case ($user !== '' && isset($user)):
                $this->equal = array('id_user' => $user);
                break;
            case ($user == ''):
                $this->equal = false;
                break;
            default:
                break;
        }
    }

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query() {
        $q = new Query();
        $q
                ->select()
                ->from($this->table);
        // check where equal
        if ($this->equal !== false) {
            $q->where_equal_to($this->equal);
        }
        // check where between value ranger
        if ($this->between !== false) {
            $q->where_between($this->between);
        }
        // check where between date ranger
        if ($this->between_d !== false) {
            $q->where_between($this->between_d);
        }

        $q
                ->order_by('id desc')
                ->run();
        $result = '';
        if ($q) {
            foreach ($q->get_selected() as $data) {
                $result.= $this->contain_table($data);
            }
            return $result;
        }
    }

    /**
     * load function jquery used for multpliples checkboxes
     * @return String
     */
    private function JS_CHECKBOXES() {
        return <<<EOF
<script language="JavaScript">
$("#select-all").click(function(event) {
    if (this.checked) {
        // Iterate each checkbox
        $(":checkbox").each(function() {
            this.checked = true;
        });
    } else {
        $(":checkbox").each(function() {
            this.checked = false;
        });
    }
});
</script> 
EOF;
    }

    /**
     * Get param via metthod GET
     * @param mixed $param Value
     * @return boolean
     */
    private static function GetParam($param) {
        $s = filter_input(INPUT_GET, $param);
        if (empty($s) || !$s) {
            return false;
        }
        return $s;
    }

    /**
     * controi classe método mágico
     * @access public
     * @return main
     */
    public function __construct() {
        $this->check_filter();
        $Object = array(
            'body_table' => $this->body_table(),
            'tools' => $this->make_tools(),
            'elements_table' => $this->Query()
        );
        return print
                $this->_LOAD_REQUIRED_LISTING() .
                $this->modal_datepicker_alt() .
                $this->MAKE_LISTING_MODE($Object) .
                $this->JS_CHECKBOXES();
    }

}
