<?php

namespace Manager\Receipt;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class ReceiptHTML extends requireds {

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
     * Discount value
     * @var float
     */
    var $discount = NULL;

    /**
     * Expense value for after subtracting
     * @var integer 
     */
    var $Expense = 0;

    /**
     * html header of table
     * @return string
     */
    protected function body_table() {
        return '<thead>
                    <tr>
                        <td class="center uniformjs"><input id="select-all" type="checkbox" name="delete" /></td>
                        <th>#ID</th>
                        <th>Cliente</th>
                        <th>Itens</th>
                        <th>Tipo</th>
                        <th title="Valor total bruto">Bruto</th>
                        <th title="Valor total lucro">Lucro</th>
                        <th>Cadastrado</th>
                        <th>Ação</th>
                    </tr>
                </thead>';
    }

    /**
     * check what button call
     * @param String $value Value array loc_action
     * @param Integer $id ID reg
     * @return Object
     */
    private function check_buttons($value, $id) {
        $result = false;
        switch ($value) {
            case 'prev':
                $result = Buttons::button_ver(FILENAME . $this->loc_action['prev'] . $id);
                break;
            case 'alt':
                $result = Buttons::button_alt(FILENAME . $this->loc_action['alt'] . $id);
                break;
            case 'del':
                $result = Buttons::button_delete(FILENAME . $this->loc_action['del'] . $id);
                break;
        }
        return $result;
    }

    /**
     * Build Buttons actions
     * @param type $id
     * @return String
     */
    private function build_buttons($id) {
        $result = '';
        foreach ($this->loc_action as $value => $key) {
            if ($key !== false) {
                $result.= ' ' . $this->check_buttons($value, $id) . ' ';
            }
        }
        return $result;
    }

    /**
     * Check data is empty
     * @param mixed $data
     * @param string $txt
     * @return mixed
     */
    private function check_field($data, $txt = '<font color="red"><b>Não Encontrado !</b></font>') {
        if (isset($data) && $data !== '') {
            return $data;
        }
        return $txt;
    }

    private function CheckStyle($style) {
        if ($style == 'Products') {
            return 'Venda Produto';
        } else {
            return 'Venda Serviço';
        }
    }

    /**
     * Check style itens
     * @param int $total
     * @param ENUM $style
     * @return string
     */
    private function CheckStyleItens($total, $style) {
        if ($style == 'Products') {
            if ($total == 1) {
                return '1 Produto';
            }
            return $total . ' Produtos';
        }
        if ($total == 1) {
            return '1 Serviço';
        }
        return $total . ' Serviços';
    }

    /**
     * html conteudo da tabela
     * @access protected
     * @return string
     */
    protected function contain_table(array $fetch) {
        $n = \Func::array_table('clientes', array('id' => $fetch['id_cliente']), 'nome');
        if ($fetch['id_cliente'] == NULl) {
            $name = '<font color="red"><b>Não Encontrado!</b></font>';
        } else {
            $name = isset($n) ? $n : '<font color="red"><b>Não Encontrado!</b></font>';
        }



        $bruteValue = \Func::_sum_values('recibos_itens', 'valor_original', array('id_recibo' => $fetch['id']));
        $EarningValue = \Func::_sum_values('recibos_itens', 'valor_lucro', array('id_recibo' => $fetch['id']));

        $desc = $fetch['discount'];

        if ($desc) {
            $value_discount = ($bruteValue * $desc) / 100;
            $EarningValue = $bruteValue - $value_discount;
        }
        $formatBruteValue = Func::FloatToReal($bruteValue);
        $formatEarningValue = Func::FloatToReal($EarningValue);

        $data = \makeNiceTime::MakeNew($fetch['data']);
        $date_str = strtotime($fetch['data']);
        $date_formated = $this->dias_da_semana[date('w', $date_str)] . ", " . strftime("%d/%m/%Y ás %H:%M", $date_str);

        $total = \Func::_contarReg('recibos_itens', array('id_recibo' => $fetch['id']));
        return <<<EOFPAGE
        <tr class="gradeX">
            <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="{$fetch['id']}" /></td>
            <td>#{$fetch['id']}</td>
            <td>{$name}</td>
            <td data-order="{$total}">{$this->CheckStyleItens($total, $fetch['style'])}</td>
            <td>{$this->CheckStyle($fetch['style'])}</td>
            <td data-order="{$bruteValue}">R$: {$formatBruteValue}</td>
            <td data-order="{$EarningValue}">R$: {$formatEarningValue}</td>
            <td data-order="{$date_str}"><a title="$date_formated">{$data}</a></td>
            <td class="right actions">{$this->build_buttons($fetch['id'])}</td>
        </tr>
EOFPAGE;
    }

    /**
     * Verify access of account type
     * @param String $data Access Data Currenty
     * @return boolean
     */
    private function CheckVisible($data) {
        $myAccountType = \GetInfo::_user_cargo(NULL, FALSE);
        $total = \Func::_contarReg('menu_sub_access', array('id_sub_menu' => $data['id']));
        // is admin ?
        if ($myAccountType == 0) {
            return true;
        }
        // check access
        if ($total > 0) {
            return true;
        }
    }

    /**
     * make a loop in tools
     * @param array $ids
     * @return string
     */
    private function tools_call(array $ids) {
        $result = '';
        $q = new Query;
        $q
                ->select()
                ->from('menu_sub')
                ->where_in(
                        array('id' => $ids)
                )
                ->run();
        if ($q) {
            $data = $q->get_selected();
            foreach ($data as $dados) {
                $url = URL . $dados['link'];
                if ($this->CheckVisible($dados)) {
                    $result.= '<li><a title="' . $this->msg['manager'] . ' ' . $dados['name'] . '" href="' . $url . '">' . $this->msg['manager'] . ' ' . $dados['name'] . '</a></li>';
                }
            }
            return $result;
        }
    }

    /**
     * build tools for listing mode
     * @return string
     */
    protected function make_tools() {
        $return = '
            <form action="' . URL . FILENAME . '/delete_all" id="delete_broadcast" method="POST">  
                        <div class="clearfix">                       
                            <div class="btn-group pull-right">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">Ferramentas <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">';
        if ($this->loc_action['add'] !== false) {
            $return.= '<li><a title="Adicionar novo(a) ' . $this->msg['singular'] . '" href="' . URL . FILENAME . $this->loc_action['add'] . '">Add Novo</a></li>'
                    . '<li class="divider"></li>';
        }
        $return.='
                                    ' . $this->tools_call($this->ids_tools) . '
                                    <li class="divider"></li>
                                    <li><a title="Exclua múltiplas ' . $this->msg['plural'] . '" href="javascript:del_all()">Excluir ' . $this->msg['plural'] . '</a></li>
                                </ul>
                            </div>
                        </div>   
              <br>';

        return $return;
    }

    /**
     * Make row HTML listing mode
     * @param array $Object
     * @return String
     */
    protected function MAKE_LISTING_MODE(array $Object) {
        return '<div class="row">
                    <div class="col-sm-12">
                        <section class="panel">
                            <header class="panel-heading">
                                ' . $this->msg['manager'] . ' ' . $this->msg['plural'] . '
                                <span class="tools pull-right">
                                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                                    <a href="javascript:;" class="fa fa-times"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                <div class="adv-table">
                                ' . $Object['tools'] . '
                                    <table  class="display table table-bordered table-striped table-condensed checkboxs js-table-sortable" id="dynamic-table">
                                        ' . $Object['body_table'] . '
                                        <tbody>' . $Object['elements_table'] . '</tbody>
                                    </table>
                                </form>    
                                </div>
                            </div>
                        </section>';
    }

    /**
     * Get Info
     * @param string $fetch
     * @return mixed
     */
    private function FetchData($fetch) {
        return Func::array_table('ConfigureInfos', array('id' => 1), $fetch);
    }

    /**
     * Show info cliente
     * @return string
     */
    private function Info_Client($id_client) {
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

        if ($id_client !== NULL) {
            if (isset($id_client)) {
                $result.= <<<EOF
                    <h4>Recibo de:</h4>
                    <h2>{$name_client}</h2>
EOF;
            } else {
                $result.= <<<EOF
                    <h4>Recibo</h4>
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
                    <h4>Recibo:</h4>
                    <h2><font color="red"><strong>Desconhecido</strong></font></h2><p> Não Encontradado !
EOF;
        }
        $result.= '</p></div>';
        return $result;
    }

    private function LoopProducts($id) {
        $q = new Query;
        $q
                ->select()
                ->from('recibos_itens')
                ->where_equal_to(
                        array(
                            'id_recibo' => $id
                        )
                )
                ->order_by('id asc')
                ->run();
        $result = '';
        $i = 1;
        foreach ($q->get_selected() as $value) {
            $total = number_format(($value['valor_original'] / $value['qnt']), 2, ',', '.');
            $subtotal = number_format(($value['valor_original']), 2, ',', '.');

            $result .= '	
                    <!-- Cart item -->
                        <tr>
                            <td>' . $i++ . '</td>
                            <td>
                                <h4>' . $value['nome'] . '</h4>
                                <p>' . $value['descri'] . '</p>
                            </td>
                            <td class="text-center">R$ ' . $total . '</td>
                            <td class="text-center">' . $value['qnt'] . '</td>
                            <td class="text-center">R$ ' . $subtotal . '</td>
                        </tr>
                    <!-- // Cart item END -->';
        }
        return $result;
    }

    /**
     * Get discount
     * @param int $id ID receipt
     * @return string
     */
    private function getDiscount($id, $brute, $return = NULL) {
        $desc = \Func::array_table('recibos', array('id' => $id), 'discount');

        if ($desc) {
            $value = \Func::_sum_values('recibos_itens', 'valor_original', array('id_recibo' => $id));
            $value_discount = ($value * $desc) / 100;
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
     * Get expense value
     * @param int $id ID receipt
     * @return string
     */
    private function getExpenseValue($id, $brute, $return = NULL) {

        $ExpenseValue = \Func::_sum_values('recibos_itens', 'valor_despesa', array('id_recibo' => $id));
        $id_product = \Func::array_table('recibos_itens', array('id_recibo' => $id), 'id_product');

        $before = \Func::negativeToPositive($brute - ($this->getDiscount($id, $brute, true) + $ExpenseValue));

        if ($return !== NULL) {
            return $ExpenseValue;
        } else {

            if (isset($id_product)) {
                $title = 'Despesa de produto';
                $name = 'Despesas';
            } else {
                $title = 'Despesa de comissões. Valor atual com as comissões R$ ' . \Func::FloatToReal($before);
                $name = 'Comissões';
            }


            if ($ExpenseValue !== 0) {
                $value = number_format($ExpenseValue, 2, ",", ".");
                return <<<EOF
            <li title="{$title}">{$name} : R$ -{$value}</li>
EOF;
            }
        }
    }

    /**
     * Make row HTML listing mode
     * @param array $data Data Query
     * @return String
     */
    protected function MAKE_PREVIEW_MODE(array $data) {
        $date = strftime('%d de %B, %Y %H:%M', strtotime($data['data']));
        $bruteValue = \Func::_sum_values('recibos_itens', 'valor_original', array('id_recibo' => $data['id']));
        #$EarningValue = \Func::_sum_values('recibos_itens', 'valor_original', array('id_recibo' => $id));

        $expense = $this->getExpenseValue($data['id'], $bruteValue);
        $discount = $this->getDiscount($data['id'], $bruteValue);

        $sub_total = number_format(($bruteValue - ($this->getExpenseValue($data['id'], $bruteValue, 1) + $this->getDiscount($data['id'], $bruteValue, 1))), 2, ",", ".");

        return <<<EOFPAGE
	<!-- page start-->
	<div class="row" id="print-receipt">
	    <div class="col-md-12">
		<section class="panel">
		    <div class="panel-body invoice">
        <div class="PrintArea area1 all">
		        <div class="invoice-header">
		            <div class="invoice-title col-md-3 col-xs-2">
		                <h1>Recibo</h1>
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
		            {$this->Info_Client($data['id_cliente'])}
		            <div class="col-md-4 col-sm-5 pull-right">
		                <div class="row">
		                    <div class="col-md-4 col-sm-5 inv-label">Fatura :</div>
		                    <div class="col-md-8 col-sm-7">#{$data['id']}</div>
		                </div>
		                <br>
		                <div class="row">
		                    <div class="col-md-4 col-sm-5 inv-label">Data :</div>
		                    <div class="col-md-8 col-sm-7">{$date}</div>
		                </div>
		                <br>
		                <div class="row">
		                    <div class="col-md-12 inv-label">
		                        <h3>Total Devido</h3>
		                    </div>
		                    <div class="col-md-12">
		                        <h1 class="amnt-value">R$ {$bruteValue}</h1>
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
		            <tbody>{$this->LoopProducts($data['id'])}</tbody>
		        </table>
		        <div class="row">
		            {$this->PaymentMethod($data)}
                             {$this->FormPayment($data)}
		            <div class="col-md-4 col-xs-5 invoice-block pull-right">
		                <ul class="unstyled amounts">
		                    <li title="valor total sem descontos">Subtotal : R$ {$bruteValue}</li>
                                     {$discount}
                                     
		                  
		                </ul>
		            </div>
		        </div>
        </div>
                        <div class="text-center invoice-btn">
                            <buttom id="button-print" class="btn btn-primary btn-lg button b1"><i class="fa fa-print"></i> Imprimir </buttom>
                        </div>
		    </div>
		</section>
	    </div>
	</div>
	<!-- page end-->
EOFPAGE;
    }

    /**
     * Check form payment
     * @return string
     */
    private function FormPayment($data) {
        return <<<EOF
<div class="col-md-3 col-xs-4 payment-method">
    <h4>Forma de Pagamento</h4>
        {$this->check_form_payment($data)}
</div>
EOF;
    }

    /**
     * payment method
     * @param array $data Query Result
     * @return string
     */
    private function PaymentMethod(array $data) {
        $Check = $this->check_metthod_payment($data);
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
     */
    private function check_form_payment(array $data) {
        $total = \Func::_sum_values('recibos_itens', 'valor_original', array('id_recibo' => $data['id']));

        if ($data['discount']) {
            $total = $total - (($total * $data['discount']) / 100);
        }

        switch ($data['Payment_method']) {
            case 'parcelado':
                $result = '<p><i class="fa fa-sitemap"></i> <strong> Parcelado</strong></p>'
                        . '<p>Parcelas : ' . $data['installments'] . 'x de R$ : ' . number_format(($total / $data['installments']), 2, ",", ".") . '</p>';
                break;
            case 'entrada_e_parcelas':
                $plot = ($total - $data['entry_value']) / $data['installments'];

                $result = '<p><i class="fa fa-sign-in"></i> <strong> Entrada & Parcelado</strong></p>'
                        . '<p>Entrada : R$' . $data['entry_value'] . '</p>'
                        . '<p>Parcelas : ' . $data['installments'] . 'x de R$ : ' . number_format($plot, 2, ",", ".") . '</p>';

                break;
            case 'porcentagem_e_Parcelas':
                $value = number_format(($total * $data['percent_entry'] / 100), 2, ",", ".");

                $plot = ($total - $value) / $data['installments'];
                $result = '<p><i class="fa fa-share-square-o"></i> <strong>Porcentagem & Parcelado</strong></p>'
                        . '<p>Porcentagem : ' . $data['percent_entry'] . '%</p>'
                        . '<p>Valor da entrada : R$ ' . $value . '</p>'
                        . '<p>Parcelas : ' . $data['installments'] . 'x de R$ : ' . Func::FormatToReal($plot) . '</p>';
                break;
            default :
                $result = '<p><i class="fa fa-money"></i> <strong>À Vista</strong></p>';
                break;
        }
        return $result;
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
                        . '<p>Agência : ' . $data['card_agence'] . '</p>';
                break;
            default :
                $result = '<p><i class="fa fa-money"></i> <strong>Dinheiro</strong></p>';
                break;
        }
        return $result;
    }

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
     * Check field is checked
     * @param mixed $default Current value
     * @param mixed $value Value needed to check
     * @return string
     */
    private function _check_checked($default, $value) {
        if ($default == $value) {
            return ' checked';
        }
    }

    /**
     * Check field is selected
     * @param mixed $default Current value
     * @param mixed $value Value needed to select
     * @return string
     */
    private function _check_selected($default, $value) {
        if ($default == $value) {
            return ' selected';
        }
    }

}

/**
 * Class Required CSS and Javascript
 * @todo put in array the path files
 */
class requireds extends ReceiptConfig {

    /**
     * Load JS for page listing mode
     * @return string
     */
    private function JS_REQUIRED_LISTING() {
        return <<<EOF
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

<!--dynamic table-->
<script type="text/javascript" language="javascript" src="js/advanced-datatable/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/data-tables/DT_bootstrap.js"></script>
<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<!--dynamic table initialization -->
<script src="js/dynamic_table_init.js"></script>
EOF;
    }

    /**
     * Load CSS for page listing mode
     * @return string
     */
    private function CSS_REQUIRED_LISTING() {
        return <<<EOF
<!--dynamic table-->
<link href="js/advanced-datatable/css/demo_page.css" rel="stylesheet" />
<link href="js/advanced-datatable/css/demo_table.css" rel="stylesheet" />
<link rel="stylesheet" href="js/data-tables/DT_bootstrap.css" />
<link href="css/glyphicons.css" rel="stylesheet" />
<link href="css/estilo.css" rel="stylesheet" />
EOF;
    }

    /**
     * Load all JS for page Preview mode
     * @return string
     */
    private function JS_REQUIRED_PREVIEW($data) {
        return <<<EOF
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
        
<!--Print documment script -->
<script src="js/printThis.js"></script>
<script>

    $(document).ready(function(){
        $("#button-print").click(function(){
             $.print(".PrintArea");
        });
    });
        /*
    $(document).ready(function(){
        $("#button-print").click(function(){
            var print = "";
            $("input.selPA:checked").each(function(){
                print += (print.length > 0 ? "," : "") + "div.PrintArea." + $(this).val();
            });
       
            var options = { 
                mode : "iframe", 
                extraCss: "css/print.css",
                //popTitle : "Recibo #{$data['id']}"
                popClose : true,  
                extraHead : '<meta charset="utf-8" />,<meta http-equiv="X-UA-Compatible" content="IE=edge"/>' 
                };
            $('div.PrintArea.area1').printArea( options );
        });
    });*/
/*
$(function () {
    $("#button-print").click(function () {        
        $('#print-receipt').printThis({
                pageTitle: 'Recibo #{$data['id']}', // add title to print page
        });
    });
});*/
</script>
        
EOF;
    }

    /**
     * Load required files for page Insert New
     * @return Object
     */
    protected function _LOAD_REQUIRED_INSERT() {
        return $this->CSS_REQUIRED_INSERT() . $this->JS_REQUIRED_INSERT();
    }

    protected function _LOAD_REQUIRED_UPDATE($data) {
        return $this->CSS_REQUIRED_INSERT() . $this->JS_REQUIRED_UPDATE($data);
    }

    /**
     * Load required files for page listing
     * @return Object
     */
    protected function _LOAD_REQUIRED_LISTING() {
        return $this->JS_REQUIRED_LISTING() . $this->CSS_REQUIRED_LISTING();
    }

    /**
     * Load JS for page preview mode
     * @return string
     */
    protected function _REQUIRED_PREVIEW_MODE($data) {
        return $this->JS_REQUIRED_PREVIEW($data);
    }

}
