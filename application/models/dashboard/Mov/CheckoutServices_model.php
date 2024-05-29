<?php

use Developer\Tools\GetInfo as GetInfo;
use Developer\Tools\Url as Url;
use Query as Query;
use Dashboard\Call_JS as Call_JS;
use DateTime as DateTime;
use Func as Func;

/**
 * This class checks are in the checkout page, or if there was the record is inserted
 * if the checkout page calls the parent
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 *
 * @version 2.1
 * @access public
 * @package HTMLCheckoutServicesModel
 * @todo finish generate booklet
 * */
class CheckoutServicesModel extends HTMLCheckoutServicesModel {

    /**
     * Return lasted ID recibo inserted for preview
     * @var integer 
     */
    var $id_recibo = NULL;

    /**
     * Location if success action
     * @var String
     */
    var $location_success = 'Reports/receipt/preview/';

    /**
     * Location if errors occurred in action
     * @var String
     */
    var $location_error = 'Mov/services';

    /**
     * Magic Method
     * @return Void
     */
    public function __construct() {
        $param = Url::getURL(3);
        if (isset($param) && $param == 'Insert') {
            # If you are in demo mode does not accept generate booklet
            $this->CheckDemostration();
            # Insert data on database
            $this->InsertOnDatabase();
        } else {
            # Show HTML checkout
            parent::__construct();
        }
    }

    /**
     * Inserts the data in the database
     * inserts revenue, expenditure and receipts
     * @return void
     */
    private function InsertOnDatabase() {
        $qnt = $_POST['qnt_Product'];
        $id = $_POST['id_Product'];
        $expense = $_POST['expense'];
        $value = $_POST['value'];
        // Insert Receipt
        $r1 = $this->InsertReceipt();
        foreach ($id as $id_service) {
            $amount = $qnt[$id_service];
            $valor = Func::negativeToPositive($value[$id_service]);

            // Check Expense, in this case is commissioner of service
            $Ex = !($expense[$id_service] == 0) ? $this->InsertExpense($expense[$id_service], $id_service, $amount) : true;

            // Insert Earning
            $EA = $this->InsertEarnings($valor, $id_service, $amount);
            // Insert Receipt itens
            $ri = $this->InsertReceiptItens($id_service, $amount);
        }
        // Insert Expense only discount if there exists
        $ExP = $this->ExpenseDiscount();
        // generate payment installments
        $plot = $this->GeneratePlots();
        // Check errors occurred
        $this->CheckErros($Ex, $EA, $r1, $ri, $ExP, $plot);
    }

    /**
     * Check demostration mode for gerate billets
     * @return boolean 
     */
    private function CheckDemostration() {
        $status = $this->CheckGenerateBillet();
        if (!$status) {
            return false;
        }
        // Check demostration mode is active
        Func::CheckDemostrationMode();
        return true;
    }

    /**
     * checks if any error occurred...
     * @param boolean $Ex Expense insert result
     * @param boolean $EA Earning insert result
     * @param boolean $r1 Receipt insert result
     * @param boolean $ri Receipt Itens insert result
     * @param boolean $ExP Expense Discount insert result
     * @param boolean $plot Payment installments insert result
     * @return void
     */
    private function CheckErros($Ex, $EA, $r1, $ri, $ExP, $plot) {
        $erro = URL . 'dashboard' . DS . $this->location_error;
        $success = URL . 'dashboard' . DS . $this->location_success . $this->id_recibo;
        switch (true) {
            case (!$Ex):
                $msg = ' Ocorreu um erro ao inserir a saida do serviço !';
                Call_JS::alerta($msg);
                Call_JS::retornar($erro);
                die($msg);
                break;
            case (!$EA):
                $msg = ' Ocorreu um erro ao inserir a entrada do serviço !';
                Call_JS::alerta($msg);
                Call_JS::retornar($erro);
                die($msg);
                break;
            case (!$r1 || !$ri):
                $msg = ' Ocorreu um erro ao inserir o recibo do serviço !';
                Call_JS::alerta($msg);
                Call_JS::retornar($erro);
                die($msg);
                break;
            case (!$ExP):
                $msg = ' Ocorreu um erro ao inserir o valor do desconto da saida do serviço !';
                Call_JS::alerta($msg);
                Call_JS::retornar($erro);
                die($msg);
                break;
            case (!$plot):
                $msg = ' Ocorreu um erro ao inserir as parcelas de pagamento !';
                Call_JS::alerta($msg);
                Call_JS::retornar($erro);
                die($msg);
                break;
            default:
                $msg = ' Novo serviço adicionado com sucesso !';
                Call_JS::alerta($msg);
                Call_JS::retornar($success);
                die($msg);
                break;
        }
    }

    /**
     * Check card name
     * @return mixed
     */
    private function check_card_name() {
        $metthod = filter_input(INPUT_POST, 'metthod');
        $card = filter_input(INPUT_POST, 'card_name');
        if ($metthod !== 'Cartão de Crédito' && $metthod !== 'Débito Automático') {
            return NULL;
        } else {
            return $card;
        }
    }

    /**
     * Insert Expense on database
     * Only Discount
     * 
     * @return boolean
     */
    private function ExpenseDiscount() {
        $des = (self::GetParam('discount', 'bool') !== false && self::GetParam('discount') !== 0) ? self::GetParam('discount') : false;
        $value = \Func::_sum_values('recibos_itens', 'valor_lucro', array('id_recibo' => self::GetParam('IDR')));

        if ($des) {
            // calculate value of discount
            $value = number_format(($value * $des) / 100, 2);

            $date = $this->format_date($_POST['horario']);
            $name = "Desconto de $des% ID do Recibo:#" . self::GetParam('IDR');
            $s = filter_input(INPUT_POST, 'status_out');
            $status = isset($s) ? true : false;

            $q = new Query;
            $q
                    ->insert_into(
                            $this->table_output, array(
                        'status' => $status,
                        'id_user' => Session::get('user_id'),
                        'discount' => true,
                        'name' => $name,
                        'id_receipt' => self::GetParam('IDR'),
                        'qnt' => NULL,
                        'value' => Func::negativeToPositive($value),
                        'metthod' => filter_input(INPUT_POST, 'metthod'),
                        'data' => $date
                            )
                    )
                    ->run();
            if (!$q) {
                return false;
            }
        }
        return true;
    }

    /**
     * Insert Expense on database
     * @param float $expense Value expense of service
     * @param integer $id_service Id of service
     * @param integer $qnt Amount value
     * @return boolean
     */
    private function InsertExpense($expense, $id_service, $qnt) {
        if ($expense !== 0) {
            $date = $this->format_date($_POST['horario']);
            $name = Func::array_table($this->table_main, array('id' => $id_service), 'titulo');
            $descri = Func::array_table($this->table_main, array('id' => $id_service), 'descri');
            $s = filter_input(INPUT_POST, 'status_out');
            $status = isset($s) ? true : false;
            $id_client = $_POST['client'] == 'nothing' ? NULL : $_POST['client'];
            $id_employee = $_POST['employee'] == 'nothing' ? NULL : $_POST['employee'];

            $q = new Query;
            $q
                    ->insert_into(
                            $this->table_output, array(
                        'status' => $status,
                        'id_user' => Session::get('user_id'),
                        'id_service' => $id_service,
                        'id_employee' => $id_employee,
                        'id_client' => $id_client,
                        'id_receipt' => self::GetParam('IDR'),
                        'name' => $name,
                        'descri' => $descri,
                        'qnt' => $qnt,
                        'value' => Func::negativeToPositive($expense),
                        'metthod' => filter_input(INPUT_POST, 'metthod'),
                        'card_name' => $this->check_card_name('card_name'),
                        'card_agence' => $this->check_field('agencia'),
                        'card_number' => $this->check_field('card_number'),
                        'cheque_number' => $this->check_field('cheque_number'),
                        'data' => $date
                            )
                    )
                    ->run();
            if (!$q) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    /**
     * Check generate billet mode
     * @return boolean
     */
    private function CheckGenerateBillet() {
        $date_close = self::GetParam('date_close');
        $font_payment = self::GetParam('final_font_payment');

        if (self::GetParam('status_billet', 'bool')) {
            if (!isset($date_close, $font_payment)) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Check generate billet are enabled
     * @return NULL|Int
     */
    private function CheckPaymentDue() {
        $status = $this->CheckGenerateBillet();
        if (!$status) {
            return NULL;
        }
        return date('d', strtotime(self::GetParam('date_close')));
    }

    /**
     * Get value correct for each payment installments
     * @param int $plots payment installments
     * @return float
     */
    private function ValuePlots($plots) {
        $desc = \Func::array_table('recibos', array('id' => self::GetParam('IDR')), 'discount');
        $value = \Func::_sum_values('recibos_itens', 'valor_original', array('id_recibo' => self::GetParam('IDR')));

        // calculate value and subtract the brute value by the discount if have
        if ($desc) {
            $value = $value - (($value * $desc) / 100);
        }

        $metthod = self::GetParam('type_metthod');
        switch ($metthod) {
            case 'parcelado':
                $result = $value;
                break;
            case 'entrada_e_parcelas':
                $result = $value - Func::RealToFloat(self::GetParam('value_entry'));
                break;
            case 'porcentagem_e_Parcelas':
                $result = $value - ($value * self::GetParam('percent_entry') / 100);
                break;
            default:
                // will never get here, because now can not generate payment installments if the method is in sight
                $result = NULL;
                break;
        }

        $final = Func::RealToFloat($result / $plots);
        return number_format($final, 2);
    }

    /**
     * Insert Billets
     * @param integer $id_output_service Id output service
     * @param float $value Value with discount if have
     * @return boolean
     */
    private function GeneratePlots() {
        $plots = self::GetParam('plots');
        $value = $this->ValuePlots($plots);

        // erros collections
        $erros = array();

        for ($i = 1; $i <= $plots; $i++) {
            // generate billets
            $this->GenerateBillet($value, $i);

            $q = new Query;
            $q
                    ->insert_into('input_servico_plots', array(
                        'id_receipt' => self::GetParam('IDR'),
                        'plot' => $i,
                        'plot_value' => $value
                            )
                    )
                    ->run();
            if (!$q) {
                $erros[$i] = true;
            }
        }
        if (!in_array(true, $erros)) {
            return true;
        }
        return false;
    }

    /**
     *  Insert Earning when it sees
     * @param float $valor
     * @param integer $id_service
     * @param integer $qnt
     * @return boolean
     */
    private function InsertExpenseAvista($valor, $id_service, $qnt) {
        $date = $this->format_date($_POST['horario']);
        $name = Func::array_table($this->table_main, array('id' => $id_service), 'titulo');
        $descri = Func::array_table($this->table_main, array('id' => $id_service), 'descri');
        $s = $_POST['status'];
        $status = isset($s) ? true : false;


        $id_client = $_POST['client'] == 'nothing' ? NULL : $_POST['client'];
        $id_employee = $_POST['employee'] == 'nothing' ? NULL : $_POST['employee'];
        $des = (self::GetParam('discount', 'bool') !== false && self::GetParam('discount') !== 0) ? self::GetParam('discount') : false;

        // calculate discout
        if ($des) {
            $valor = ($valor * $des) / 100;
        }

        $q = new Query;
        $q
                ->insert_into(
                        $this->table_input, array(
                    'status' => $status,
                    'id_user' => Session::get('user_id'),
                    'id_service' => $id_service,
                    'id_employee' => $id_employee,
                    'id_client' => $id_client,
                    'id_font' => NULL,
                    'id_receipt' => self::GetParam('IDR'),
                    'name' => $name,
                    'descri' => $descri,
                    'qnt' => $qnt,
                    'discount' => $des,
                    'generate_billet' => $this->CheckGenerateBillet(),
                    'payment_due' => $this->CheckPaymentDue(),
                    'value' => Func::negativeToPositive($valor),
                    'Payment_method' => self::GetParam('type_metthod'),
                    'metthod' => self::GetParam('metthod'),
                    'card_name' => $this->check_card_name('card_name'),
                    'card_agence' => $this->check_field('agencia'),
                    'card_number' => $this->check_field('card_number'),
                    'cheque_number' => $this->check_field('cheque_number'),
                    'data' => $date
                        )
                )
                ->run();

        if (!$q) {
            return false;
        }
        return true;
    }

    /**
     *  Insert Earning when there is installments
     * @param integer $id_service
     * @param integer $qnt
     * @return boolean
     */
    private function InsertExpenseParcelado($id_service, $qnt) {
        $date = $this->format_date($_POST['horario']);
        $name = Func::array_table($this->table_main, array('id' => $id_service), 'titulo');
        $descri = Func::array_table($this->table_main, array('id' => $id_service), 'descri');
        $s = $_POST['status'];
        $status = isset($s) ? true : false;

        $id_client = $_POST['client'] == 'nothing' ? NULL : $_POST['client'];
        $id_employee = $_POST['employee'] == 'nothing' ? NULL : $_POST['employee'];
        $des = (self::GetParam('discount', 'bool') !== false && self::GetParam('discount') !== 0) ? self::GetParam('discount') : false;
        $font = $this->CheckGenerateBillet() ? self::GetParam('final_font_payment') : NULL;
        $q = new Query;
        $q
                ->insert_into(
                        $this->table_input, array(
                    'status' => $status,
                    'id_user' => Session::get('user_id'),
                    'id_service' => $id_service,
                    'id_employee' => $id_employee,
                    'id_client' => $id_client,
                    'id_font' => $font,
                    'id_receipt' => self::GetParam('IDR'),
                    'name' => $name,
                    'descri' => $descri,
                    'qnt' => $qnt,
                    'discount' => $des,
                    'generate_billet' => $this->CheckGenerateBillet(),
                    'installments' => self::GetParam('plots'),
                    'payment_due' => $this->CheckPaymentDue(),
                    'value' => NULL,
                    'Payment_method' => self::GetParam('type_metthod'),
                    'metthod' => self::GetParam('metthod'),
                    'card_name' => $this->check_card_name('card_name'),
                    'card_agence' => $this->check_field('agencia'),
                    'card_number' => $this->check_field('card_number'),
                    'cheque_number' => $this->check_field('cheque_number'),
                    'data' => $date
                        )
                )
                ->run();

        if (!$q) {
            return false;
        }


        // everything worked returns true
        return true;
    }

    /**
     *  Insert Earning when there plot and incoming payment
     * @param float $valor
     * @param integer $id_service
     * @param integer $qnt
     * @return boolean
     */
    private function InsertExpenseParcelaPlusEntry($valor, $id_service, $qnt) {
        $date = $this->format_date($_POST['horario']);
        $name = Func::array_table($this->table_main, array('id' => $id_service), 'titulo');
        $descri = Func::array_table($this->table_main, array('id' => $id_service), 'descri');
        $s = $_POST['status'];
        $status = isset($s) ? true : false;

        $id_client = $_POST['client'] == 'nothing' ? NULL : $_POST['client'];
        $id_employee = $_POST['employee'] == 'nothing' ? NULL : $_POST['employee'];
        $des = (self::GetParam('discount', 'bool') !== false && self::GetParam('discount') !== 0) ? self::GetParam('discount') : false;

        // calculate discout
        if ($des) {
            $valor = ($valor * $des) / 100;
        }

        $entry = $valor - Func::RealToFloat(self::GetParam('value_entry'));
        $font = $this->CheckGenerateBillet() ? self::GetParam('final_font_payment') : NULL;
        $q = new Query;
        $q
                ->insert_into(
                        $this->table_input, array(
                    'status' => $status,
                    'id_user' => Session::get('user_id'),
                    'id_service' => $id_service,
                    'id_employee' => $id_employee,
                    'id_client' => $id_client,
                    'id_font' => $font,
                    'id_receipt' => self::GetParam('IDR'),
                    'name' => $name,
                    'descri' => $descri,
                    'qnt' => $qnt,
                    'discount' => $des,
                    'generate_billet' => $this->CheckGenerateBillet(),
                    'installments' => self::GetParam('plots'),
                    'payment_due' => $this->CheckPaymentDue(),
                    'value' => Func::negativeToPositive($entry),
                    'Payment_method' => self::GetParam('type_metthod'),
                    'metthod' => self::GetParam('metthod'),
                    'card_name' => $this->check_card_name('card_name'),
                    'card_agence' => $this->check_field('agencia'),
                    'card_number' => $this->check_field('card_number'),
                    'cheque_number' => $this->check_field('cheque_number'),
                    'data' => $date
                        )
                )
                ->run();

        if (!$q) {
            return false;
        }

        // everything worked returns true
        return true;
    }

    /**
     *  Insert Earning when there plot and percentage payout entry
     * @param float $valor
     * @param integer $id_service
     * @param integer $qnt
     * @return boolean
     */
    private function InsertExpensePercentPlusEntry($valor, $id_service, $qnt) {
        $date = $this->format_date($_POST['horario']);
        $name = Func::array_table($this->table_main, array('id' => $id_service), 'titulo');
        $descri = Func::array_table($this->table_main, array('id' => $id_service), 'descri');
        $s = $_POST['status'];
        $status = isset($s) ? true : false;

        $id_client = $_POST['client'] == 'nothing' ? NULL : $_POST['client'];
        $id_employee = $_POST['employee'] == 'nothing' ? NULL : $_POST['employee'];
        $des = (self::GetParam('discount', 'bool') !== false && self::GetParam('discount') !== 0) ? self::GetParam('discount') : false;

        // calculate discout
        if ($des) {
            $valor = ($valor * $des) / 100;
        }


        // make percent
        $entry = ($valor * self::GetParam('percent_entry')) / 100;

        $final_value = number_format($valor - $entry, 2);
        $font = $this->CheckGenerateBillet() ? self::GetParam('final_font_payment') : NULL;
        $q = new Query;
        $q
                ->insert_into(
                        $this->table_input, array(
                    'status' => $status,
                    'id_user' => Session::get('user_id'),
                    'id_service' => $id_service,
                    'id_employee' => $id_employee,
                    'id_client' => $id_client,
                    'id_font' => $font,
                    'id_receipt' => self::GetParam('IDR'),
                    'name' => $name,
                    'descri' => $descri,
                    'qnt' => $qnt,
                    'discount' => $des,
                    'generate_billet' => $this->CheckGenerateBillet(),
                    'installments' => self::GetParam('plots'),
                    'payment_due' => $this->CheckPaymentDue(),
                    'value' => Func::negativeToPositive($final_value),
                    'Payment_method' => self::GetParam('type_metthod'),
                    'metthod' => self::GetParam('metthod'),
                    'card_name' => $this->check_card_name('card_name'),
                    'card_agence' => $this->check_field('agencia'),
                    'card_number' => $this->check_field('card_number'),
                    'cheque_number' => $this->check_field('cheque_number'),
                    'data' => $date
                        )
                )
                ->run();

        if (!$q) {
            return false;
        }

        // everything worked returns true
        return true;
    }

    /**
     * Insert Earnings on database
     * @param float $valor Service value
     * @param integer $id_service Id of service
     * @param integer $qnt Amout value
     * @return boolean
     */
    private function InsertEarnings($valor, $id_service, $qnt) {
        $metthod = self::GetParam('type_metthod');
        switch ($metthod) {
            case 'parcelado':
                $this->InsertExpenseParcelado($id_service, $qnt);
                break;
            case 'entrada_e_parcelas':
                $this->InsertExpenseParcelaPlusEntry($valor, $id_service, $qnt);
                break;
            case 'porcentagem_e_Parcelas':
                $this->InsertExpensePercentPlusEntry($valor, $id_service, $qnt);
                break;
            default:
                $this->InsertExpenseAvista($valor, $id_service, $qnt);
                break;
        }
        return true;
    }

    /**
     * Get date initial and insert interval
     * @param string $date_close Day start
     * @param type $i Month additional
     * @return datetime Returns timestamp format
     */
    private function CheckInterval($date_close, $i) {
        $start = strtotime($date_close);
        if ($i !== 1) {
            return date("Y-m-d", strtotime("+$i month", $start));
        }

        return date("Y-m-d", $start);
    }

    /**
     * Generate Billets data
     * @param float $value Value amount
     * @return boolean
     */
    private function GenerateBillet($value, $i) {
        $date_close = self::GetParam('date_close');
        $font_payment = self::GetParam('final_font_payment');

        $id_client = $_POST['client'] == 'nothing' ? NULL : $_POST['client'];
        $id_employee = $_POST['employee'] == 'nothing' ? NULL : $_POST['employee'];
        $status_billet = self::GetParam('status_billet', 'bool');

        // chec generate billets are active and type payment
        if (!$status_billet) {
            $erros = false;
        } else {
            // insert new plot
            $q = new Query;
            $q
                    ->insert_into('ProgrammerBillet', array(
                        'id_poster' => Session::get('user_id'),
                        'id_client' => $id_client,
                        'id_funcionario' => $id_employee,
                        'id_receipt' => self::GetParam('IDR'),
                        'id_font' => $font_payment,
                        'plots' => $i,
                        'value' => $value,
                        'data_send' => $this->CheckInterval($date_close, $i)
                            )
                    )
                    ->run();

            $erros = !$q ? true : false;
        }

        if (!$erros) {
            return false;
        }

        return true;
    }

    /**
     * Insert new Receipt on database
     * @return Void
     */
    private function InsertReceipt() {
        $date = $this->format_date($_POST['horario']);
        $s = $_POST['status'];
        $status = isset($s) ? true : false;
        $id_client = $_POST['client'] == 'nothing' ? NULL : $_POST['client'];
        $id_employee = $_POST['employee'] == 'nothing' ? NULL : $_POST['employee'];

        $des = (self::GetParam('discount', 'bool') !== false && self::GetParam('discount') !== 0) ? self::GetParam('discount') : false;


        $entry_value = self::GetParam('type_metthod') == 'entrada_e_parcelas' ? Func::RealToFloat(self::GetParam('value_entry')) : NULL;
        $percent_entry = self::GetParam('type_metthod') == 'porcentagem_e_Parcelas' ? self::GetParam('percent_entry') : NULL;
        $font = $this->CheckGenerateBillet() ? self::GetParam('final_font_payment') : NULL;


        $q = new Query;
        $q
                ->insert_into(
                        'recibos', array(
                    'status' => $status,
                    'id_user' => Session::get('user_id'),
                    'id_employee' => $id_employee,
                    'id_cliente' => $id_client,
                    'id_font' => $font,
                    'discount' => $des,
                    'generate_billet' => $this->CheckGenerateBillet(),
                    'installments' => self::GetParam('plots'),
                    'payment_due' => $this->CheckPaymentDue(),
                    '_interval' => $this->check_interval('interval'),
                    '_interval_period' => $this->check_interval('period'),
                    'Payment_method' => self::GetParam('type_metthod'),
                    'entry_value' => $entry_value,
                    'percent_entry' => $percent_entry,
                    'style' => $this->ReciptEnum,
                    'metthod' => self::GetParam('metthod'),
                    'card_name' => $this->check_card_name('card_name'),
                    'card_agence' => $this->check_field('agencia'),
                    'card_number' => $this->check_field('card_number'),
                    'cheque_number' => $this->check_field('Fcheque_number'),
                    'data' => $date
                        )
                )
                ->run();
        if (!$q) {
            return false;
        }
        $this->id_recibo = $q->get_insert_id();
        return true;
    }

    /**
     * Check field
     * @param mixed $value
     * @return mixed
     */
    private function check_field($value) {
        $field = filter_input(INPUT_POST, $value);
        return (isset($field) AND $field !== '') ? $field : NULL;
    }

    /**
     * Format date from timestamp to dd-mm-yyyy hh:mm
     * @param Datetime $data
     * @return Datetime
     */
    private function format_date($data) {
        try {
            $dateTime = new DateTime($data);
            return $dateTime->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            die("Error : " . $e->getMessage());
        }
    }

    /**
     * Insert Receipt Itens on database
     * @return Void
     */
    private function InsertReceiptItens($id_service, $qnt) {
        $brute = Func::_sum_values($this->table_main, 'valor', array('id' => $id_service)) * $qnt;
        $discount = Func::array_table($this->table_output, array('id_service' => $id_service, 'discount' => true), 'value');
        $despesa = Func::_sum_values($this->table_output, 'value', array('id_service' => $id_service, 'discount' => false));

        $sub_valor = $brute - ($discount + $despesa);

        $date = $this->format_date($_POST['horario']);
        $name = Func::array_table($this->table_main, array('id' => $id_service), 'titulo');
        $descri = Func::array_table($this->table_main, array('id' => $id_service), 'descri');


        $q = new Query;
        $q
                ->insert_into(
                        'recibos_itens', array(
                    'id_recibo' => $this->id_recibo,
                    'id_service' => $id_service,
                    'nome' => $name,
                    'descri' => $descri,
                    'valor_original' => Func::negativeToPositive($brute),
                    'valor_despesa' => Func::negativeToPositive($despesa),
                    'valor_lucro' => Func::negativeToPositive($sub_valor),
                    'qnt' => $qnt,
                    'data' => $date
                        )
                )
                ->run();
        if (!$q) {
            return false;
        }
        return true;
    }

}

/**
 * HTML page checkout
 * 
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 *
 * @version 2.1
 * @access public
 * @package HTMLCheckoutServicesModel
 * @todo added description and plots payments
 * */
class HTMLCheckoutServicesModel {

    /**
     * Return last id of recibo
     * @var integer 
     */
    protected $IDR;

    /**
     * Table main where has all information needed
     * @var string 
     */
    protected $table_main = 'servicos';

    /**
     * Table ouput
     * @var string 
     */
    protected $table_output = 'output_servico';

    /**
     * Table input
     * @var string 
     */
    protected $table_input = 'input_servico';

    /**
     * Recipt Enum column
     * @var string 
     */
    protected $ReciptEnum = 'Services';

    /**
     * Build CheckoutServices HTML
     * @return Void
     */
    public function __construct() {
        $product_list = filter_input(INPUT_POST, 'products_selected');
        $param = Url::getURL(3);
        if (($product_list !== false) && $param !== 'Insert') {
            Call_JS::alerta(' Carrinho vázio !');
            Call_JS::retornar(URL . 'dashboard/Mov/services');
            die(' Carrinho vázio !');
        }
        // inserts temporary receipt
        $this->GetIDR();
        print $this->HTML() . $this->JS_REQUIRED();
    }

    /**
     * Get Info
     * @param string $fetch Column name
     * @return mixed
     */
    private function FetchData($fetch) {
        return Func::array_table('ConfigureInfos', array('id' => 1), $fetch);
    }

    /**
     * JS Required
     * @return String
     */
    private function JS_REQUIRED() {
        return <<<EOFPAGE
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>


<!--common script init for all pages-->
<script src="js/scripts.js"></script>
EOFPAGE;
    }

    /**
     * Show info cliente
     * @return string
     */
    private function Info_Client() {
        $id_client = filter_input(INPUT_POST, 'client');
        $name_client = GetInfo::_name($id_client);
        $criter = array('id' => $id_client);
        $email = Func::array_table('clientes', $criter, 'Email');
        $End = Func::array_table('clientes', $criter, 'End');
        $Num = Func::array_table('clientes', $criter, 'Num');
        $Fone = Func::array_table('clientes', $criter, 'Fone');
        $Bairro = Func::array_table('clientes', $criter, 'Bairro');
        $Cep = Func::array_table('clientes', $criter, 'Cep');
        $UF = Func::array_table('clientes', $criter, 'UF');
        $Cidade = Func::array_table('clientes', $criter, 'Cidade');
        $result = '<div class="col-md-4 col-sm-4 pull-left">';

        if ($id_client !== 'nothing') {
            if (isset($id_client)) {
                $result.= <<<EOF
                    <h4>Fatura de:</h4>
                    <h2>{$name_client}</h2>
EOF;
            } else {
                $result.= <<<EOF
                    <h4>Fatura</h4>
EOF;
            }
            $result.= <<<EOF
                <p>
                    {$End} {$Num} {$Bairro}<br>
                    {$Cidade} {$UF} {$Cep} <br>
EOF;
            if (isset($Fone)) {
                $result.= 'Phone: ' . $Fone . '<br>';
            }
            if (isset($email)) {
                $result.= <<<EOF
Email : <a href="mailto:{$email}">{$email}</a>
EOF;
            }
        } else {
            $result.= <<<EOF
                    <h4>Fatura:</h4>
                    <h2><font color="red"><strong>Desconhecido</strong></font></h2><p> Não Encontradado !
EOF;
        }
        $result.= '</p></div>';
        return $result;
    }

    /**
     * Get id receipt
     * @return Void
     */
    private function GetIDR() {
        $q = new Query();
        $q
                ->select('id')
                ->from('recibos')
                ->order_by('id desc')
                ->limit(1)
                ->run();
        $total = $q->get_selected_count();
        $data = $q->get_selected();
        if (!(isset($data) && $total > 0)) {
            $this->IDR = 1;
        } else {
            $id = $data['id'] + 1;
            $this->IDR = $id;
        }
    }

    /**
     * Check metthod name to preview table columns
     * @param array $data Query Result
     */
    private function check_metthod_payment(array $data) {
        switch ($data['metthod']) {
            case 'Cartão de Crédito':
                $result = '<p><i class="fa fa-credit-card"></i> <strong>Cartão de Crédito</strong></p>'
                        . '<p>Cartão : ' . $data['card_name'] . '</p>'
                        . '<p>Numero : ' . $data['card_number'] . '</p>';
                break;
            case 'Cheque':
                $result = '<p><i class="fa fa-edit"></i> <strong> Cheque</strong></p>'
                        . '<p>Numero : ' . $data['cheque_number'] . '</p>';
                break;
            case 'Débito Automático':
                $result = '<p><i class="fa fa-credit-card"></i> <strong>Débito Automático</strong></p>'
                        . '<p>Cartão : ' . $data['card_name'] . '</p>'
                        . '<p>Numero : ' . $data['card_number'] . '</p>'
                        . '<p>Agência : ' . $data['agencia'] . '</p>';
                break;
            default :
                $result = '<p><i class="fa fa-money"></i> <strong>Dinheiro</strong></p>';
                break;
        }
        return $result;
    }

    /**
     * payment method
     * @return string
     */
    private function PaymentMethod() {
        $Check = $this->check_metthod_payment($_POST);
        $result = <<<EOF
<div class="col-md-4 col-xs-5 payment-method">
    <h4>Método de pagamento</h4>
                
                
EOF;
        $result.= $Check . '</div>';
        return $result;
    }

    /**
     * Check metthod name to preview table columns
     * @param array $data Query Result
     * 
     * @return string
     */
    private function check_form_payment(array $data, $brute, $total) {
        switch ($data['type_metthod']) {
            case 'parcelado':
                $result = '<p><i class="fa fa-sitemap"></i> <strong> Parcelado</strong></p>'
                        . '<p>Parcelas : ' . $data['plots'] . 'x de R$ : ' . number_format(($total / $data['plots']), 2, ",", ".") . '</p>';
                break;
            case 'entrada_e_parcelas':
                $plot = ($total - $data['value_entry']) / $data['plots'];
                $result = '<p><i class="fa fa-sign-in"></i> <strong> Entrada & Parcelado</strong></p>'
                        . '<p>Entrada : R$' . $data['value_entry'] . '</p>'
                        . '<p>Parcelas : ' . $data['plots'] . 'x de R$ : ' . number_format($plot, 2, ",", ".") . '</p>';

                break;
            case 'porcentagem_e_Parcelas':
                $value = number_format(($total * $data['percent_entry']) / 100, 2, ",", ".");
                $plot = ($total - $value) / $data['plots'];
                $result = '<p><i class="fa fa-share-square-o"></i> <strong>Porcentagem & Parcelado</strong></p>'
                        . '<p>Porcentagem : ' . $data['percent_entry'] . '%</p>'
                        . '<p>Valor da entrada : R$ ' . $value . '</p>'
                        . '<p>Parcelas : ' . $data['plots'] . 'x de R$ : ' . Func::FormatToReal($plot) . '</p>';
                break;
            default :
                $result = '<p><i class="fa fa-money"></i> <strong>À Vista</strong></p>';
                break;
        }
        return $result;
    }

    /**
     * Check form payment
     * @return string
     */
    private function FormPayment($brute, $total) {
        return <<<EOF
<div class="col-md-3 col-xs-4 payment-method">
    <h4>Forma de Pagamento</h4>
        {$this->check_form_payment($_POST, $brute, $total)}
</div>
EOF;
    }

    /**
     * Loop products via POST
     * @param string $value
     * @param string $return
     * @return string
     */
    private function LoopProducts($value = NULL, $return = false) {
        $product_list = $_REQUEST['products_selected'];
        $result = '';
        $i = 1;
        if (!empty($product_list)) {
            $subtotal = 0;
            foreach ($product_list as $product) {
                $chunks = explode('|', $product);
                $product_id = $chunks[0];
                $product_qty = $chunks[1];

                // get info of product
                $prince = Func::array_table($this->table_main, array('id' => $product_id), 'valor');
                $des = Func::array_table($this->table_main, array('id' => $product_id), 'comissao');

                // service value without comission value
                $value_service_without_expense = ($product_qty * $prince);
                $subtotal = $subtotal + $value_service_without_expense;
                // check automatic expense is true
                if ($des == NULL) {
                    // value expense
                    $expense = 0;
                } else {
                    // value expense
                    $expense = (($des * $prince) / 100) * $product_qty;
                }

                // service value subtracted by the commission
                $value_service_with_expense = $expense - ($product_qty * $prince);


                $result .= '	
                    <!-- Cart item -->
                        <tr>
                            <td>' . $i++ . '</td>
                            <td>
                                <h4>' . Func::array_table($this->table_main, array('id' => $product_id), 'titulo') . '</h4>
                                <p>' . Func::array_table($this->table_main, array('id' => $product_id), 'descri') . '</p>
                            </td>
                            <td class="text-center">R$ ' . number_format($prince, 2, ',', '.') . '</td>
                            <td class="text-center">' . $product_qty . '</td>
                            <td class="text-center">R$ ' . number_format($value_service_without_expense, 2, ',', '.') . '</td>
                        </tr>
                        <input type="hidden" value="' . $value_service_with_expense . '" name="value[' . $product_id . ']">   
                        <input type="hidden" value="' . $expense . '" name="expense[' . $product_id . ']">
                        <input type="hidden" value="' . $product_id . '" name="id_Product[]">
                        <input type="hidden" value="' . $product_qty . '" name="qnt_Product[' . $product_id . ']">
                    <!-- // Cart item END -->';
            }
        }
        if ($value == NULL) {
            if (Url::getURL(3) !== 'insert') {
                return $result;
            }
        } else {
            switch ($return) {
                case 'valor':
                    $object = $subtotal;
                    break;
                case 'despesa':
                    $object = $expense * -1;
                    break;
                case 'lucro':
                    $object = $subtotal - $expense;
                    break;
                default:
                    break;
            }
            return $object;
        }
    }

    /**
     * Get discount
     * @param flot $discount Value of discount
     * @param flot $brute Value brute
     * @param flot $return Return data
     * @return string
     */
    private function getDiscount($discount, $brute, $return = NULL) {
        $desc = self::GetParam('discount');

        if ($desc) {
            $value_discount = ($brute * $desc) / 100;
        } else {
            $value_discount = 0;
        }

        if ($return !== NULL) {
            return $value_discount;
        } else {
            $final_value = number_format($value_discount, 2, ",", ".");
            if ($desc) {
                $expense = $brute - $value_discount;
                $value_discount = number_format($value_discount, 2, ",", ".");
                return <<<EOF
<li title="Despesas de descontos. Valor do desconto R$ {$final_value} porcentagem do desconto {$desc}% Valor total com desconto R$ {$expense}">Desconto : -{$value_discount}</li>
EOF;
            }
        }
    }

    /**
     * get subtotal value
     * @param flot $discount Value of discount
     * @param flot $ExpenseValue Expense value
     * @param flot $brute Brute value
     * 
     * @return flot
     */
    private function getSubTotalValue($discount, $ExpenseValue, $brute) {
        $ExpenseValue = \Func::negativeToPositive($ExpenseValue);
        $value_discount = $this->getDiscount($discount, $brute, true);
        $now = \Func::negativeToPositive($brute - ($value_discount + $ExpenseValue));
        return number_format($now, 2, ",", ".");
    }

    /**
     * Get expense value
     * @param flot $discount Value of discount
     * @param flot $ExpenseValue Expense value
     * @param flot $brute brute value
     * @param mixed $return Return data
     * 
     * @return string
     */
    private function getExpenseValue($discount, $ExpenseValue, $brute, $return = NULL) {
        $ExpenseValue = \Func::negativeToPositive($ExpenseValue);
        $value_discount = $this->getDiscount($discount, $brute, true);
        $now = \Func::negativeToPositive($brute - ($value_discount + $ExpenseValue));
        if ($return !== NULL) {
            return $ExpenseValue;
        } else {
            $title = 'Despesa de comissões. Valor atual com as comissões R$ ' . \Func::FloatToReal($now);
            $name = 'Comissões';

            if ($ExpenseValue !== 0) {
                $value = number_format($ExpenseValue, 2, ",", ".");
                return <<<EOF
            <li title="{$title}">{$name} : R$ -{$value}</li>
EOF;
            }
        }
    }

    /**
     * Check interval
     * @param string $param
     * @return mixed
     */
    protected function check_interval($param) {
        $s = self::GetParam('c_interval', 'bool');
        if (!$s) {
            return NULL;
        }
        return self::GetParam($param);
    }

    /**
     * HTML
     * @return HTML
     */
    private function HTML() {
        $data = strftime('%d de %B, %Y %H:%M', strtotime(self::GetParam('horario')));
        $status = isset($_POST['status_out']) ? $_POST['status_out'] : NULL;
        $estado = isset($_POST['status']) ? $_POST['status'] : NULL;

        $des = self::GetParam('discount');
        $value = $this->LoopProducts(true, 'valor');
        $status_billet = self::GetParam('status_billet');
        if (isset($des) && $des !== 0) {
            $discount = '<li title="Desconto">Desconto : ' . $des . '%</li>';
            $total = ($value - (($value * $des) / 100));
        } else {
            $discount = NULL;
            $total = $value;
        }
        $html_discount = $this->getDiscount($discount, $this->LoopProducts(true, 'valor'));
        $html_expense = $this->getExpenseValue($discount, $this->LoopProducts(true, 'despesa'), $this->LoopProducts(true, 'valor'));
        $subtotal = $this->getSubTotalValue($discount, $this->LoopProducts(true, 'despesa'), $this->LoopProducts(true, 'valor'));
        $url = URL;
        return <<<EOFPAGE
<!-- page start-->
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <div class="panel-body invoice">
        <form action="{$url}dashboard/Mov/CheckoutServices/Insert" method="post">
                <div class="invoice-header">
                    <div class="invoice-title col-md-3 col-xs-2">
                        <h1>Fatura</h1>
                        <img class="logo-print" src="images/recibo/{$this->FetchData('logo')}" alt="">
                    </div>
                    <div class="invoice-info col-md-9 col-xs-10">
                            <div class="pull-right">
                                <div class="col-md-6 col-sm-6 pull-left">
                                    <p>{$this->FetchData('End')} {$this->FetchData('Num')} {$this->FetchData('Bairro')}<br>
                                    </p>
                                    <p>{$this->FetchData('Cidade')}-{$this->FetchData('UF')} {$this->FetchData('Cep')}</p>
                                </div>
                                <div class="col-md-6 col-sm-6 pull-right">
                                    <p>Telefone: {$this->FetchData('Fone')} <br>
                                    <a href="">{$this->FetchData('Email')}</a></p>
                                </div>
                            </div>     
                    </div>
                </div>
                <div class="row invoice-to">
                    {$this->Info_Client()}
                    <div class="col-md-4 col-sm-5 pull-right">
                        <div class="row">
                            <div class="col-md-4 col-sm-5 inv-label">Fatura :</div>
                            <div class="col-md-8 col-sm-7">#{$this->IDR}</div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4 col-sm-5 inv-label">Data :</div>
                            <div class="col-md-8 col-sm-7">{$data}</div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 inv-label">
                                <h3>Total Devido</h3>
                            </div>
                            <div class="col-md-12">
                            <input type="hidden" value="{$this->check_interval('interval')}" name="interval">
                            <input type="hidden" value="{$this->check_interval('period')}" name="period">
                            
                            <input type="hidden" value="{$this->IDR}" name="IDR">
                            <input type="hidden" value="{$estado}" name="status">
                            <input type="hidden" value="{$_POST['metthod']}" name="metthod">
                            <input type="hidden" value="{$_POST['card_name']}" name="card_name">
                            <input type="hidden" value="{$_POST['card_number']}" name="card_number">
                            <input type="hidden" value="{$_POST['agencia']}" name="agencia">
                            <input type="hidden" value="{$_POST['cheque_number']}" name="cheque_number">
                            <input type="hidden" value="{$_POST['client']}" name="client">
                            <input type="hidden" value="{$_POST['employee']}" name="employee">
                            <input type="hidden" value="{$_POST['horario']}" name="horario">
                            <input type="hidden" value="{$status}" name="status_out">
                            <input type="hidden" value="{$des}" name="discount">
                            <input type="hidden" value="{$status_billet}" name="status_billet">
                            <input type="hidden" value="{$_POST['type_metthod']}" name="type_metthod"> 
                            <input type="hidden" value="{$_POST['plots']}" name="plots">
                            <input type="hidden" value="{$_POST['value_entry']}" name="value_entry">
                            <input type="hidden" value="{$_POST['percent_entry']}" name="percent_entry">
                            
                            <input name="date_close" value="{$_POST['date_close']}" type="hidden">
                            <input name="final_font_payment" value="{$_POST['final_font_payment']}" type="hidden"> 
                            
                                <h1 class="amnt-value">R$ {$this->LoopProducts(true, 'valor')}</h1>
                            </div>
                        </div>

                    </div>
                </div>
                <table class="table table-invoice" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>item Descrição</th>
                            <th class="text-center">Custo Unitário</th>
                            <th class="text-center">Quantidade</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>{$this->LoopProducts()}</tbody>
                </table>
                <div class="row">
                    {$this->PaymentMethod()}
                    {$this->FormPayment($value, $total)}
                    
                    <div class="col-md-4 col-xs-5 invoice-block pull-right">
                        <ul class="unstyled amounts">
                            <li title="valor total sem descontos">Subtotal : R$ {$this->LoopProducts(true, 'valor')}</li>
                            {$html_discount}
                            {$html_expense}
                            <li class="grand-total">Valor Total : R$ {$subtotal}</li>
                        </ul>
                    </div>
                </div>

                <div class="text-center invoice-btn">
                    <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-check"></i> Finalizar Fatura</button>
                    <a href="{$url}dashboard/Mov/services"  class="btn btn-danger btn-lg"><i class="fa fa-times"></i> Cancelar </a>
                </div>
            </form>          
            </div>
        </section>
    </div>
</div>
<!-- page end-->
EOFPAGE;
    }

    /**
     * Get value post metthod
     * @param mixed $param
     * @param string $returnMetthod Type data return
     * @access protected
     * @return boolean|mixed
     */
    protected static function GetParam($param, $returnMetthod = 'object') {
        $c = filter_input(INPUT_POST, $param);
        if ($c) {
            return ($returnMetthod !== 'object') ? true : $c;
        }
        return false;
    }

}
