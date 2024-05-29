<?php

include("../application/config/config.php");
include("../vendor/offboard/Class-Query/autoload.php");

class ExpenseCron {

    /**
     * Table output
     * @var string 
     */
    private $table_output = 'output_others';

    /**
     * Table cron
     * @var string 
     */
    private $table_cron = 'cron_output';

    /**
     * Days of week
     * @var array 
     */
    private $dias_da_semana = array(
        'Domingo',
        'Segunda-Feira',
        'Terça-Feira',
        'Quarta-Feira',
        'Quinta-Feira',
        'Sexta-Feira',
        'Sábado'
    );

    /**
     * link mysqli connection
     * @var array 
     */
    private $mysqi;

    /**
     * Format to timestamp
     * remove seconds
     * @var string 
     */
    private $format = 'Y-m-d H:i';

    /**
     * Magic Metthod
     * 
     * @return void
     */
    public function __construct() {
        $this->Select();
    }

    /**
     * Select registry
     * @return boolean
     */
    private function Select() {
        $q = new Query();
        $q
                ->select()
                ->from($this->table_cron)
                ->run();

        $total = $q->get_selected_count();
        $data = $q->get_selected();

        if (!($data && $total > 0)) {
            return false;
        }

        foreach ($data as $row) {
            $this->CheckSend($row);
        }
    }

    /**
     * checks already registered
     * @param array $data Result Query
     * 
     * @return boolean
     */
    private function CheckSend(array $data) {
        switch ($data['cron_time']) {
            case 'monthly':
                $result = $this->CheckSendMonthly($data);
                break;
            case 'weekly':
                $result = $this->CheckSendWeekly($data);
                break;
            default: // daily
                $result = $this->CheckSendDaily($data);
                break;
        }
        return $result;
    }

    /**
     * Check reg exist
     * @param string $table
     * @param string $where
     * @return boolean
     */
    private function countReg($id) {
        $q = new Query();
        $q
                ->select()
                ->from($this->table_output)
                ->where_equal_to(
                        array(
                            'id_cron' => $id,
                            'DATE(data) = DATE(NOW())'
                        )
                )
                ->run();

        $total = $q->get_selected_count();
        $data = $q->get_selected();

        if (!($data && $total > 0)) {
            return false;
        }
        return true;
    }

    /**
     * Verifies that has sent when weekly
     * @param array $data Result Query
     * 
     * @return boolean
     */
    private function CheckSendWeekly(array $data) {
        if ($data['weekly_day'] == date('w')) {
            $r = $this->countReg($data['id']);
            if (!$r) {
                return false;
            }
            $this->InsertExpense($data);
            return true;
        }
        return false;
    }

    /**
     * Verifies that has sent when daily
     * @param array $data Result Query
     * 
     * @return boolean
     */
    private function CheckSendDaily(array $data) {
        $cron_time = date($this->format, strtotime($data['daily_hour']));

        if (strtotime(date($this->format)) == strtotime($cron_time)) {
            $r = $this->countReg($data['id']);
            if (!$r) {
                return false;
            }
            $this->InsertExpense($data);
            return true;
        }
        return false;
    }

    /**
     * Verifies that has sent when monthly
     * @param array $data Result Query
     * 
     * @return boolean
     */
    private function CheckSendMonthly(array $data) {
        if ($data['monthly_day'] == date('d')) {
            $r = $this->countReg($data['id']);
            if (!$r) {
                return false;
            }
            $this->InsertExpense($data);
            return true;
        }
        return false;
    }

    private function GetIntervalDays(array $data) {
        switch ($data['cron_time']) {
            case 'monthly':
                $result = $data['monthly_day'];
                break;
            case 'weekly':
                $result = $data['weekly_day'];
                break;
            default: // daily
                $result = $data['daily_hour'];
                break;
        }
    }

    /**
     * Check value is null
     * @param string $data
     * @return string
     */
    private function IsNULL($data) {
        if ($data == NULL || empty($data)) {
            return NULL;
        }
        return $data;
    }

    /**
     * Insert Expense
     * @param array $data Result Query
     * 
     * @return boolean
     */
    private function InsertExpense(array $data) {
        $q = new Query;
        $q
                ->insert_into(
                        $this->table_output, array(
                    'id_user' => $data['id_user'],
                    'cron' => true,
                    'id_cron' => $data['id'],
                    'metthod' => $data['metthod'],
                    'card_name' => $this->IsNULL($data['card_name']),
                    'card_agence' => $this->IsNULL($data['card_agence']),
                    'card_number' => $this->IsNULL($data['card_number']),
                    'cheque_number' => $this->IsNULL($data['cheque_number']),
                    'title' => $data['title'],
                    'descri' => $this->IsNULL($data['descri']),
                    'value' => $data['value'],
                    'status' => $data['status'],
                        )
                )
                ->run();
        if (!$q) {
            echo 'não foi possivel inserir o registro :' . $data['id'];
            return false;
        }
        return true;
    }

}

// start cron job
new ExpenseCron();
