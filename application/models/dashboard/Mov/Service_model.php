<?php

use Query as Query;
use Developer\Tools\Url as Url;
use Developer\Tools\GetInfo as GetInfo;

class ServiceModel extends ServiceHTML {

    //Function to check if the request is an AJAX request
    private static function is_ajax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function __Construct($param = NULL, $element = NULL) {

        if ($param == 'loaded') {
            parent::__construct();
        } elseif ($param == 'load_element') {
            if ($element == 'loadModalBillet') {
                return print $this->ModalElementsBillet();
            }
        }
    }

}

class ServiceHTML {

    public function __construct() {
        print $this->BuildForm();
    }

    private function loop_Services() {
        $q = new Query;
        $q
                ->select()
                ->from('servicos')
                ->group_by('id desc')
                ->run();

        $result = '';

        if ($q) {
            $data = $q->get_selected();
            foreach ($data as $dados) {
                if ($dados['comissao'] !== NULL) {
                    $comi = $dados['comissao'] . '% total R$: ' . ($dados['valor'] * $dados['comissao']) / 100;
                } else {
                    $comi = '<strong>Sem Comissão !</strong>';
                }
                $result.= ' <input type="hidden" pcomi="' . $comi . '" pdescri="' . Func::str_truncate($dados['descri'], 100) . '" pprice="' . $dados['valor'] . '" pname="' . Func::str_truncate($dados['titulo'], 60) . '" pid="' . $dados['id'] . '">';
            }

            return $result;
        }
    }

    /**
     * faz um loop com todos os funcionários
     * @access private
     * @return string
     */
    private function loop_users($table = 'clientes') {
        // check account type for saller
        if (ACCOUNT_TYPE_FOR_SALLER == $_SESSION['user_account_type'] && $table !== 'clientes') {
            return '<option value="' . Session::get('user_id') . '" selected>' . GetInfo::_name(Session::get('user_id')) . '</option>';
        }

        $q = new Query;
        $q
                ->select()
                ->from($table)
                ->order_by('nome asc')
                ->run();

        $result = '<option value="nothing" selected>--Nenhum--</option>';

        if ($q) {
            $data = $q->get_selected();
            foreach ($data as $dados) {
                $result.= '<option value="' . $dados['id'] . '">' . Func::FirstAndLastName($dados['nome']) . '</option>';
            }
            return $result;
        }
    }

    private static function ComissionStatusDay() {
        if (STATUS_DAY_CLOSE == false) {
            $comission = <<<EOF
                            <div class="form-group">
                                <label class="control-label col-md-4">Estado<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                   <input name="status_out" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">       
                                </div>
                                <button data-original-title="Estado da comissão." data-content="Se habilitado marca como pago a comissão do serviço. Caso o produto seja comissionável." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>
EOF;
        } else {
            $comission = '<input name="status_out" type="hidden" value="0">';
        }
        return $comission;
    }

    /**
     * Generate Plots
     * @param int $min Min value
     * @param int $max Max value
     * @return string
     */
    private static function GeneratePlots($min = 2, $max = 12) {
        $result = '';
        for ($i = $min; $i <= $max; $i++) {
            $result.= '<option value="' . $i . '">' . $i . 'x</option>';
        }
        return $result;
    }

    /**
     * verifies that it is in demonstration mode and shows a custom help message
     * @return string
     */
    private static function CustomHelperGenerateSlips() {
        if (DEMOSTRATION == true) {
            $result = '<button data-original-title="Gerar Boletos (MODO DE DEMOSTRAÇÂO NÂO FUNCIONA)." data-content="Se habilitado envia para o email do cliente o boleto do pagamento, se a compra for dividida você precisa escolher o dia do mês que será gerado os boletos automáticamente." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>';
        } else {
            $result = '<button data-original-title="Gerar Boletos." data-content="Se habilitado envia para o email do cliente o boleto do pagamento, se a compra for dividida você precisa escolher o dia do mês que será gerado os boletos automáticamente." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>';
        }

        return $result;
    }

    /**
     * show all months in year
     * @return string
     */
    private static function ShowAllMonths($selected = false) {
        $result = '';
        $current_month = date('m');
        for ($i = 1; $i <= 12; $i++) {
            $month = strftime("%B", mktime(0, 0, 0, $i));
            if ($selected == true && $current_month == $i) {
                $result.= sprintf("<option value='%d' selected>%s</option>", $i, ucfirst($month));
            } else {
                $result.= sprintf("<option value='%d'>%s</option>", $i, ucfirst($month));
            }
        }
        return $result;
    }

    /**
     * show all months in year
     * @return string
     */
    private function GenerateFonts() {
        $q = new Query;
        $q
                ->select()
                ->from('ConfigureFonts')
                ->group_by('id desc')
                ->run();
        $result = '';
        if ($q) {
            foreach ($q->get_selected() as $dados) {
                if (GetInfo::_user_cargo(NULL, FALSE) == 1 || GetInfo::_user_cargo(NULL, FALSE) == 0) {
                    $result .= '<option value="' . $dados['Id'] . '" title="' . $dados['banco'] . ' Agencia : ' . $dados['agencia'] . ' Conta: ' . $dados['conta'] . '"> ' . $dados['titulo'] . ' </option>';
                } else {
                    $result .= '<option value="' . $dados['Id'] . '" title="' . $dados['banco'] . '"> ' . $dados['titulo'] . ' </option>';
                }
            }
        }
        return $result;
    }

    /**
     * Modal Elements Billet
     * @access protected
     * @return string
     */
    protected function ModalElementsBillet() {
        $GerateMonths = self::ShowAllMonths(true);
        $today = date('d-m-Y');
        return <<<EOF
<div class="form-group">
    <label for="nome" class="control-label col-md-4">Vencimento<span id="field-required">*</span></label>
    <div class="col-md-6">
        <input class="form-control form-control-inline input-medium default-date-picker" id="date_e" name="date_close"  size="16" type="text" value="{$today}" />
    </div>
    <button data-original-title="Data de Vencimento" data-content="Selecione a data que será iniciado o primeiro pagamento será iniciado, o dia selecionado será o dia do vencimento." data-placement="bottom" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
</div>
                               
<div class="form-group">
    <label class="control-label col-md-4">Fonte<span id="field-required">*</span></label>
    <div class="col-md-6">
        <select id="e12" name="font_payment" class="populate" style="width: 210px">
            {$this->GenerateFonts()}
        </select>
    </div>
    <button data-original-title="Fonte" data-content="Conta bancaria que o dinheiro será depositado." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>            
</div>
            
<div class="form-group">
    <label for="nome" class="control-label col-md-4">Intervalo de Vencimento<span id="field-required">*</span></label>
    <div class="col-md-6">
         <input name="c_interval" id="c_interval" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">       
    </div>
    <button data-original-title="Intervalo de Vencimento" data-content="Marque se deseja da um prazo a mais no vencimento." data-placement="bottom" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
</div>
        
<div id="interval">   
    <div class="form-group">
        <label class="control-label col-md-4">Intervalo<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input name="port" value="7" type="number" class="spinner-input form-control" maxlength="5">
        </div>
        <button data-original-title="Intervalo" data-content="O boleto é enviado para o cliente no mesmo dia do vencimento, o intervalo é adicionado ao dia do vencimento" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>            
    </div>

    <div class="form-group">
        <label class="control-label col-md-4">Período<span id="field-required">*</span></label>
        <div class="col-md-6">
            <select id="e13" name="period" class="populate" style="width: 210px">
                <option value="day">Dia(s)</option>
                 <option value="week">Semana(s)</option>
                 <option value="month">Mês/meses</option>
                 <option value="year">Ano(s)</option>
            </select>
        </div>
        <button data-original-title="Período" data-content="Qual será o período do intervalo ?" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>            
    </div>
</div>
EOF;
    }

    /**
     * Left Elements fields
     * @return string
     */
    private function LeftElements() {
        $comission = self::ComissionStatusDay();
        $CustomHelperGenerateSlips = self::CustomHelperGenerateSlips();
        $now = date('d-m-Y H:i');
        return <<<EOF
<div class="form-group">
    <label class="control-label col-md-4">Data<span id="field-required">*</span></label>
    <div class="col-md-6">
        <input size="16" type="text" name="horario" value="{$now}" class="form_datetime form-control">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4">Desconto<span id="field-required">*</span></label>
    <div class="col-md-6">
        <input name="c_discount" id="discount" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">       
    </div>
    <button data-original-title="Desconto." data-content="Se habilitado libera um campo para inserir a porcentagem do desconto." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
</div>
<!-- where the response will be displayed -->
<div id='response'></div>

<div class="form-group">
    <label class="control-label col-md-4">Gerar Boleto(s)<span id="field-required">*</span></label>
    <div class="col-md-6">
        <input name="status_billet" id="status_billet" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">                             
    </div>
    {$CustomHelperGenerateSlips}
</div>   


<div class="form-group">
    <label class="control-label col-md-4">Cliente<span id="field-required">*</span></label>
    <div class="col-md-6">
        <select id="e2" name="client" class="populate" style="width: 100%">
            {$this->loop_users()}
        </select>                               
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-4">Usuário<span id="field-required">*</span></label>
    <div class="col-md-6">
        <select id="e9" name="employee" class="populate" style="width: 100%">
            {$this->loop_users('funcionarios')}
        </select>                               
    </div>
</div>
{$comission}
EOF;
    }

    /**
     * Right Elements fields
     * @return string
     */
    private function RightElements() {
        $Plots = self::GeneratePlots();
        return <<<EOF
<div class="form-group">
    <label class="control-label col-md-4">Estado<span id="field-required">*</span></label>
    <div class="col-md-6">
        <input name="status" type="checkbox" checked data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">                             
    </div>
</div>   

<div class="form-group">
    <label class="control-label col-md-4">Forma de Pag.<span id="field-required">*</span></label>
    <div class="col-md-6">
      <select id="e15" name="type_metthod" class="populate" style="width: 80%">
            <option value="a_vista" selected>À Vista</option>
            <option value="parcelado">Parcelado</option>
            <option value="entrada_e_parcelas">Entrada e Parcelas</option>
            <option value="porcentagem_e_Parcelas">Porcentagem e Parcelas</option>
        </select>      
    </div>
     <button data-original-title="Forma de Pagamento" data-content="Negociação feita, o padrão está definido como á vista." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
</div>   
    

           
<input name="date_close" id="date_close" type="hidden">
<input name="final_font_payment" id="final_font_payment" type="hidden">      
        
<div class="form-group" id="div_discount_percent">
    <label class="control-label col-md-4">Porcentagem<span id="field-required">*</span></label>
    <div class="col-md-6">
        <div class="input-group">
            <input name="percent_entry" id='discount_percent' value="0" type="text" class="form-control percent">  
            <span class="input-group-addon ">%</span>

        </div>
    </div>
    <button data-original-title="Entrada Porcentagem" data-content="Porcentagem do valor pago de entrada." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>        
</div>
            
<div class="form-group" id="div_money_entry">
    <label class="control-label col-md-4">Entrada<span id="field-required">*</span></label>
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-addon ">R$</span>    
                <input name="value_entry" value="0" type="text" class="form-control money">  
        </div>
    </div>
     <button data-original-title="Valor de Entrada" data-content="Valor pago de entrada." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
</div> 
    
<div class="form-group" id="div_plots">
    <label class="control-label col-lg-4">Parcelamento<span id="field-required">*</span></label>
    <div class="col-md-6">
        <select id="e14" name="plots" title="Enum data" class="populate" style="width: 80%">
            {$Plots}
        </select>
    </div>
    <button data-original-title="Parcelamento" data-content="Quantidade de Parcelas." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>        
</div> 

<div class="form-group">
    <label class="control-label col-lg-4" title="Método de Pagamento">Pagamento<span id="field-required">*</span></label>
    <div class="col-md-6">
        <select id="e0" name="metthod" class="populate" style="width: 80%">
            <option value="Dinheiro" selected>Dinheiro</option>
            <option value="Cartão de Crédito">Cartão de Crédito</option>
            <option value="Débito Automático">Débito Automático</option>
            <option value="Cheque">Cheque</option>
        </select>
    </div>
</div>
<div id="only_card">      
    <div class="form-group">
        <label class="control-label col-lg-4">Cartão<span id="field-required">*</span></label>
        <div class="col-md-6">
            <select id="e1" name="card_name" class="populate" style="width: 80%">
                <option value="American Express">American Express</option>
                <option value="Diners Club">Diners Club</option>
                <option value="MasterCard">MasterCard</option>
                <option value="Visa">Visa</option>
                <option value="Maestro">Maestro</option>
                <option value="Amex">Amex</option>
                <option value="Outros">Outros</option>
            </select>
        </div>
    </div>     
    <div class="form-group">
        <label class="control-label col-lg-4">Numero<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input type="text" name="card_number" class="form-control">
        </div>
    </div> 
</div> 
<div id="only_ag">       
    <div class="form-group">
        <label class="control-label col-lg-4">Agência<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input type="text" name="agencia" class="form-control">
        </div>
    </div>
</div>  
<div id="only_check">
    <div class="form-group">
        <label class="control-label col-lg-4">Numero<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input type="text" name="cheque_number" class="form-control">
        </div>
    </div>  
</div>        
</div>
EOF;
    }

    private function BuildForm() {
        $url = URL;
        return <<<EOF
<form class="cmxform form-horizontal" id="checkout" method="post" enctype="multipart/form-data" action="{$url}dashboard/Mov/CheckoutServices">
    <div class="form-group">
        <div class="col-md-11">
            <!-- Smart Cart HTML Starts -->
            <div id="SmartCart" class="scMain">{$this->loop_Services()}</div>
        </div>
    </div>  
    <div class="col-md-5">    
     {$this->LeftElements()}   
    </div>

    <div class="col-md-5">
        {$this->RightElements()}
    </div>
</form>
EOF;
    }

}
