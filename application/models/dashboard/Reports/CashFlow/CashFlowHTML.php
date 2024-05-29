<?php

namespace Reports\CashFlow;

use Dashboard\Buttons as Buttons;
use Query as Query;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class CashFlowHTML extends requireds {

    /**
     * Data loop marcador
     * @var Str 
     */
    var $data_mar = null;

    /**
     * Data loop fornecedor
     * @var Str 
     */
    var $data_for = null;

    /**
     * total amount products
     * @var array 
     */
    var $total_amout = array();

    /**
     * class style button
     * @var array 
     */
    private $class_buttons = array(
        'default',
        'primary',
        'success',
        'info',
        'warning',
        'danger'
    );

    /**
     * date temporary
     * @var array 
     */
    var $date_tmp = array();

    /**
     * Total amount brute
     * @var float 
     */
    protected $total_brute = 0;

    /**
     * total amount expense
     * @var float 
     */
    protected $total_expense = 0;

    /**
     * total amount expense
     * @var float 
     */
    protected $sumary_reports = array();

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

    protected function contain_table(array $param, $table) {
        $name = isset($param['title']) ? $param['title'] : $param['name'];
        $status = $param['status'] == false ? '<span class="label label-danger label-mini">Pendente</span>' : '<span class="label label-success label-mini">Pago</span>';
        $value = number_format($param['value'], 2, ",", ".");
        switch ($table) {
            case 'input_others':
                $type = 'Receita';
                break;
            case 'input_product':
                $type = 'Venda Produtos';
                break;
            case 'input_servico':
                $type = 'Venda Serviços';
                break;
            case 'output_others':
                $type = 'Despesa';
                break;
            case 'output_servico':
                $type = 'Comissão Serviço';
                break;
            case 'output_product':
                $type = 'Despesa Produto';
                break;
            default:
                $type = 'unknow';
                break;
        }
        return <<<EOF
<tr class="{$table}">
    <td><a href="#">{$type}</a></td>
    <td class="hidden-phone">{$name}</td>
    <td> R$ {$value} </td>
    <td>{$status}</td>
</tr>
EOF;
    }

    private function CalcReportsEach($tale) {
        if (isset($this->sumary_reports[$tale])) {

            return number_format($this->sumary_reports[$tale], 2, ",", ".");
        }
        return "0,00";
    }

    /**
     * Show top employee
     * @return type
     */
    private function ShowTable($elements) {
        $brute = number_format($this->total_brute, 2, ",", ".");
        $expense = number_format($this->total_expense, 2, ",", ".");
        $name = WEB_SITE_CEO_NAME;
        $gain = number_format($this->total_brute - $this->total_expense, 2, ",", ".");


        return <<<EOF
<div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                         Fluxos de  {$this->msg['plural']}
                        <span class="tools pull-right">
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                            <a href="javascript:;" class="fa fa-cog"></a>
                            <a href="javascript:;" class="fa fa-times"></a>
                        </span>
                    </header>
                    <div class="panel-body">
                        <table id="testTable" class="table  table-hover general-table">
                            <thead>
                                <tr>
                                    <th> Tipo</th>
                                    <th class="hidden-phone">Título</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>{$elements}
                                
                             </tbody>
                            
                        </table>
                            <div class="row">
                            <div class="col-md-5 col-xs-7 payment-method">
                                <h4>Resumo do Relátório</h4>
                                <p> Receitas R$  {$this->CalcReportsEach('input_others')}</p>
                                <p> Serviços R$  {$this->CalcReportsEach('input_servico')}</p>
                                <p> Produtos R$  {$this->CalcReportsEach('input_product')}</p>
                                
                            
                                <br>
                                <p> Despesas R$ {$this->CalcReportsEach('output_others')} </p>
                                <p> Comissões R$  {$this->CalcReportsEach('output_servico')}</p>
                                <p> Despesas Produtos R$  {$this->CalcReportsEach('output_product')}</p>
                                <h3 class="inv-label itatic">{$name}</h3>
                            </div>
                            
                            <div class="col-md-6 col-xs-5 invoice-block pull-right">
                                <ul class="unstyled amounts">
                                    <li>Bruto : {$brute}</li>
                                    <li>Despesas : -{$expense} </li>
                                    <li class="grand-total">Total : R$ {$gain}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    
EOF;
    }

    protected function GetParams() {
        $filter = \Url::getURL(3);
        if (isset($filter) && $filter == 'filter') {
            return '/filter' .
                    '?min=' . self::GetParam('min') .
                    '&max=' . self::GetParam('max') .
                    '&from=' . self::GetParam('from') .
                    '&to=' . self::GetParam('to') .
                    '&poster=' . self::GetParam('poster');
        }
    }

    /**
     * Query result timeline
     * @param String $table Table name
     * @return string
     */
    private function QueryTimeline($table) {
        $q = new Query();
        $q
                ->select()
                ->from($table)
                ->where_equal_to(
                        array(
                            'status' => true
                        )
                )
                ->order_by('data desc')
                ->run();
        $data = $q->get_selected();
        return $data;
    }

    /**
     * Get group timeline
     * @param array $data Data query result
     * @return string
     */
    private function GroupTimeline($data) {
        $date = $data['data'];
        if (!in_array($date, $this->date_tmp)) {
            $this->date_tmp[] = $date;
            $time = strftime('%d/%m/%Y', strtotime($date));
            $url = URL . FILENAME;
            return <<<EOF
<article class="timeline-item alt" id="{$date}">
    <div class="text-right">
        <div class="time-show">
            <a href="{$url}#{$date}" class="btn btn-default">{$time}</a>
        </div>
    </div>
</article>
EOF;
        }
    }

    /**
     * Create a timeline text
     * @param string $table Table name used
     * @param array $data Query result
     * @return string
     */
    private function TimelineText($table, array $data) {
        $name = isset($data['name']) ? $data['name'] : $data['title'];
        $client_id = isset($data['id_client']) ? $data['id_client'] : NULL;
        $client = isset($client_id) ? \Func::FirstAndLastName(\Func::array_table('clientes', array('id' => $client_id), 'nome')) : 'Sem cliente';
        switch ($table) {
            case 'input_product':
                $result = sprintf('%2$s comprou um novo produto %1$s', $client, $name);
                break;
            case 'input_servico':
                $result = sprintf('%2$s vendeu o serviço %s para %1$s', $client, $name);
                break;
            default:
                $result = sprintf('Adicionou uma nova receita %s', $name);
                break;
        }
        return $result;
    }

    /**
     * Check icons
     * @param string $table Table name used
     * @return string
     */
    protected function CheckIcon($table) {
        switch ($table) {
            case 'input_product':
                $result = '<i class="fa fa-shopping-cart"></i>';
                break;
            case 'input_servico':
                $result = '<i class="fa fa-globe"></i>';
                break;
            default:
                $result = '<i class="fa fa-usd"></i>';
                break;
        }
        return $result;
    }

    /**
     * loop timeline
     * @return string
     */
    private function LoopTimeline() {
        $products = $this->QueryTimeline('input_product');
        $services = $this->QueryTimeline('input_servico');
        $others = $this->QueryTimeline('input_others');


        $o_products = $this->QueryTimeline('output_product');
        $o_services = $this->QueryTimeline('output_servico');
        $o_others = $this->QueryTimeline('output_others');

        $arr = array_merge($products, $services, $others, $o_others, $o_products, $o_services);
        $colors = array('green', 'red', 'yellow', 'blue', 'purple', 'light-green');
        $i = 0;
        $result = '';

        foreach ($arr as $k => $v) {
            #$v = $v;
            $n = rand(0, 5);
            $i++;
            $date = \makeNiceTime::MakeNew($v['data']);
            $div = ($i % 2 == 0) ? 'alt' : NULL;
            $time = strftime(' %H:%M:%S', strtotime($v['data']));
            $class = $colors[$n];



            $result.= <<<EOF
{$this->GroupTimeline($v)}
<article class="timeline-item {$div}">
    <div class="timeline-desk">
        <div class="panel">
            <div class="panel-body">
                <span class="arrow-alt"></span>
                <span class="timeline-icon {$class}">
                  {$this->CheckIcon($k)}  
                </span>
                <span class="timeline-date">{$time}</span>
                <h1 class="{$class}">{$date} R$ {$v['value']}</h1>
                <p>{$this->TimelineText($k, $v)}</p>
            </div>
        </div>
    </div>
</article>
EOF;
        }
        return $result;
    }

    /**
     * load timeline html
     * @return string
     */
    private function LoadTimeline() {
        $url = URL . FILENAME;
        return <<<EOF
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Timeline</h4>
            </div>
            <div class="modal-body">
        <!-- page start-->
        <div class="row">
            <div class="col-sm-12">
                <div class="timeline">
                    <article class="timeline-item alt">
                        <div class="text-right">
                            <div class="time-show first">
                                <a href="{$url}#" class="btn btn-primary">Hoje</a>
                            </div>
                        </div>
                    </article>
                   {$this->LoopTimeline()}
                </div>
            </div>
        </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->
EOF;
    }

    private static function GetParam($param) {
        $value = filter_input(INPUT_GET, $param);
        if (isset($value)) {
            return $value;
        }
        return false;
    }

    /**
     * loop indication user
     * @param mixed $data If false shows all, if you have an id, check what is selected
     * @return string
     */
    private function loop_users($data = false) {
        $q = new Query;
        $q
                ->select()
                ->from('users')
                ->run();
        $result = '<option value="">-- Todos --</option>';
        if ($q) {
            $users = $q->get_selected();
            foreach ($users as $user) {
                #   if ($data !== false) {
                $result.= '<option value="' . $user['user_id'] . '"' . $this->_check_selected($user['user_id'], self::GetParam('poster')) . '>' . \GetInfo::_name($user['user_id']) . '</option>';
                # } else {
                #     $result.= '<option value="' . $user['user_id'] . '">' . \GetInfo::_name($user['user_id']) . '</option>';
                # }
            }
            return $result;
        }
    }

    /**
     * Show info main user
     * @param int $inbox Total inbox message unread
     * @param int $notifier Total messa notifier unread
     * @return string
     */
    private function LoadModalFilter() {
        $url = URL . FILENAME;
        $min = self::GetParam('min');
        $max = self::GetParam('max');
        $from = self::GetParam('from');
        $to = self::GetParam('to');
        return <<<EOF
<!-- Modal -->
<div class="modal fade" id="ModalFilter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Filtrar Registros Despesas</h4>
            </div>
            <div class="modal-body">
<form action="{$url}/filter" method="get" id="form_filter" class="form-horizontal ">
    <div class="form-group">
        <label class="control-label col-md-3">Valores</label>
        <div class="col-md-5">
            <div class="input-group input-large">
                <span class="input-group-addon btn-white"><i class="fa fa-usd"></i></span>
                <input type="text" value="{$min}" class="form-control" name="min" placeholder="Min">
                <span class="input-group-addon btn-white"><i class="fa fa-usd"></i></span>
                <input type="text" value="{$max}" class="form-control" name="max" placeholder="Max">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3">Período</label>
        <div class="col-md-6">
            <div class="input-group input-large">
                <input type="text" value="{$from}" class="form-control dpd1" name="from">
                <span class="input-group-addon">Até</span>
                <input type="text" value="{$to}" class="form-control dpd2" name="to">
            </div>
            <span class="help-block">Escolha um intervalo de datas</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">Usuário</label>
        <div class="col-md-6">
            <div class="input-group input-large">
                   <select id="e1" name="poster" class="populate " style="width: 250px;">
                          {$this->loop_users()}
                    </select>
            </div>
            <span class="help-block">Usuário que adicionou a Receita</span>
        </div>
    </div>
            </div>
            <div class="modal-footer">
                 <button class="btn btn-success" type="submit">Filtrar</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
            </div>

       </form>                   
        </div>
    </div>
</div>
<!-- modal -->
EOF;
    }

    /**
     * Main widget
     * @access protected
     * @return EOF
     */
    protected function Widget($elements) {
        $url = URL . FILENAME . $this->GetParams();

        return <<<EOF
{$this->LoadTimeline()}
{$this->LoadModalFilter()}
<!-- page start-->
<div class="row">
    <div class="col-md-9" id="PrintArea">
        {$this->ShowTable($elements)}
    </div>
         <div class="col-md-3">
        <section class="panel">
                    <header class="panel-heading">
                        Ferramentas
                            <span class="tools pull-right">
                                <a class="fa fa-chevron-down" href="javascript:;"></a>
                                <a class="fa fa-cog" href="javascript:;"></a>
                                <a class="fa fa-times" href="javascript:;"></a>
                            </span>
                    </header>
                    <div class="panel-body">
                        <div class="row m-bot20">
                            <div class="col-md-4 col-xs-4">Filtrar</div>
                            <div class="col-md-5 col-xs-3">
                                <button data-target="#ModalFilter" data-toggle="modal" type="button" class="btn btn-xs btn-info" title=""><i class="fa fa-filter"></i></button>
                            </div>
                        </div>
                        <div class="row m-bot20">
                            <div class="col-md-4 col-xs-4">Timeline</div>
                            <div class="col-md-5 col-xs-4">
                                 <button data-target="#myModal" data-toggle="modal" type="button" class="btn btn-xs btn-warning" title="Visualizar timeline com todos os dados"><i class="fa fa-sort-amount-asc"></i></button>
                            </div>
                        </div>
        
                        <div class="row m-bot20">
                            <div class="col-md-4 col-xs-4">Exporta</div>
                            <div class="col-md-5 col-xs-3">
                                 <!-- /btn-group -->
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-xs" type="button"><i class="fa fa-print"></i></button>
                                        <ul role="menu" class="dropdown-menu">
                                            <li><a href="javascript:void(0);" onclick="javascript:demoFromHTML('PrintArea');">PDF</a></li>
                                            <li><a href="javascript:void(0);" onclick="$('#testTable').tableExport({type:'excel',escape:'false'});">Excel</a></li>
                                            <!-- <li><a href="javascript:void(0);" onclick="$('#PrintArea').tableExport({type:'png',escape:'false'});">PNG</a></li> -->
                                            <li><a href="javascript:void(0);" onclick="$('#PrintArea').tableExport({type:'powerpoint',escape:'false'});">PowerPoint</a></li>
                                            <li><a href="javascript:void(0);" onclick="$('#PrintArea').tableExport({type:'doc',escape:'false'});">Word</a></li>
                                            <li><a href="javascript:void(0);" onclick="$('#testTable').tableExport({type:'txt',escape:'false'});">TXT</a></li>
                                            <li><a href="javascript:void(0);" onclick="$('#testTable').tableExport({type:'xml',escape:'false'});">XML</a></li>
                                            <li class="divider"></li>
                                            <li><a onclick="javascript:void(0);" id="printBtn">Imprimir</a></li>
                                        </ul>
                                    </div>
                                <!-- /btn-group -->
                            </div>
                        </div>

        
                    </div>
                </section>
                    <section class="panel">
                    <header class="panel-heading">
                        Dados Listados
                            <span class="tools pull-right">
                                <a class="fa fa-chevron-down" href="javascript:;"></a>
                                <a class="fa fa-cog" href="javascript:;"></a>
                                <a class="fa fa-times" href="javascript:;"></a>
                            </span>
                    </header>
                    <div class="panel-body">
                        <div class="row m-bot20">
                            <div class="col-md-5 col-xs-4">Serviços</div>
                            <div class="col-md-5 col-xs-5">
                                <input id="check_service" type="checkbox" checked data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>" class="switch-mini">
                            </div>
                        </div>

                        <div class="row m-bot20">
                            <div class="col-md-5 col-xs-4">Produtos</div>
                            <div class="col-md-5 col-xs-5">
                                <input id="check_products" type="checkbox" checked data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>" class="switch-mini">
                            </div>
                        </div>
        
                        <div class="row m-bot20">
                            <div class="col-md-5 col-xs-4">Receitas</div>
                            <div class="col-md-5 col-xs-5">
                                <input id="check_earning" type="checkbox" checked data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>" class="switch-mini">
                            </div>
                        </div>

                        <div class="row m-bot20">
                            <div class="col-md-5 col-xs-4">Despesas</div>
                            <div class="col-md-5 col-xs-5">
                                <input id="check_expense" type="checkbox" checked data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>" class="switch-mini">
                            </div>
                        </div>
        
                        <div class="row m-bot20">
                            <div class="col-md-5 col-xs-4">Comissões</div>
                            <div class="col-md-5 col-xs-5">
                                <input id="check_comissions" type="checkbox" checked data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>" class="switch-mini">
                            </div>
                        </div>
                        <div class="row m-bot20">
                            <div class="col-md-5 col-xs-4">Despesa Estoque</div>
                            <div class="col-md-5 col-xs-5">
                                <input id="check_stock" type="checkbox" checked data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>" class="switch-mini">
                            </div>
                        </div>
                    </div>
                </section>
</div>
        
</div>
<!-- page end-->
EOF;
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
class requireds extends CashFlowConfig {

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
     * Limit in days
     * @var integer 
     */
    var $limit_in_days = 31;

    /**
     * Get current date and add x days
     * @return Timestamp
     */
    private function get_date($i, $format = 'Y-m-d  ') {
        $date = date('Y-m-d H:i:s');
        $xmasDay = new DateTime("$date - $i day");
        return $xmasDay->format($format);
    }

    /**
     * Load JS for page listing mode
     * @return string
     */
    private function JS_REQUIRED_MAIN() {

        $title = WEB_SITE_CEO_NAME . ' Fluxo de Caixa ' . strftime('%d de %B , %Y');
        return <<<EOF
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<script src="js/bootstrap-switch.js"></script>

<script type="text/javascript" src="js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="js/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>

<script type="text/javascript" src="js/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.quicksearch.js"></script>
        
<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>
        
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>

<script src="js/jquery.customSelect.min.js" ></script>


<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<!--toggle initialization-->
<script src="js/toggle-init.js"></script>

<script src="js/advanced-form.js"></script>
        
<script type="text/javascript" src="js/jspdf.debug.js"></script>
        
        
<script type="text/javascript" src="js/tableExport.js"></script>
<script type="text/javascript" src="js/jquery.base64.js"></script>
        
<!-- <script type="text/javascript" src="js/html2canvas.js"> -->
        
<script>
        
$('#check_service').change(function(){
  if($(this).prop("checked")) {
    $('.input_servico').show();
  } else {
    $('.input_servico').hide();
  }
}); 
        
$('#check_products').change(function(){
  if($(this).prop("checked")) {
    $('.input_product').show();
  } else {
    $('.input_product').hide();
  }
});   
        
$('#check_earning').change(function(){
  if($(this).prop("checked")) {
    $('.input_others').show();
  } else {
    $('.input_others').hide();
  }
});
        
$('#check_expense').change(function(){
  if($(this).prop("checked")) {
    $('.output_others').show();
  } else {
    $('.output_others').hide();
  }
}); 
        
$('#check_comissions').change(function(){
  if($(this).prop("checked")) {
    $('.output_servico').show();
  } else {
    $('.output_servico').hide();
  }
});   
        
$('#check_stock').change(function(){
  if($(this).prop("checked")) {
    $('.output_product').show();
  } else {
    $('.output_product').hide();
  }
});
         
</script>
        
<script type="text/javascript">
        
$("#printBtn").click(function(){
    printcontent($("#PrintArea").html());
});
        
function printcontent(content)
{
    var mywindow = window.open('', '', '');
    mywindow.document.write('<html><title>{$title}</title><body>');
    mywindow.document.write('<style type="text/css">table { page-break-inside:auto }tr{ page-break-inside:avoid; page-break-after:auto }</style>');
    mywindow.document.write('<link rel="stylesheet" href="css/style.css" />');
    mywindow.document.write('<link rel="stylesheet" href="css/style-responsive.css" />');
    mywindow.document.write('<link rel="stylesheet" href="bs3/css/bootstrap.min.css" />');
    mywindow.document.write(content);
    mywindow.document.write('</body></html>');
    mywindow.document.close();
    mywindow.print();
    return true;
}
    </script>
    
<script>
function demoFromHTML(id) {
    var pdf = new jsPDF('p', 'pt', 'letter');
    // source can be HTML-formatted string, or a reference
    // to an actual DOM element from which the text will be scraped.
    source = $('#PrintArea')[0];

    // we support special element handlers. Register them with jQuery-style 
    // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
    // There is no support for any other type of selectors 
    // (class, of compound) at this time.
    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#bypassme': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };
    margins = {
        top: 80,
        bottom: 60,
        left: 40,
        width: 522
    };
    // all coords and widths are in jsPDF instance's declared units
    // 'inches' in this case
    pdf.fromHTML(
    source, // HTML string or DOM elem ref.
    margins.left, // x coord
    margins.top, { // y coord
        'width': margins.width, // max width of content on PDF
        'elementHandlers': specialElementHandlers
    },

    function (dispose) {
        // dispose: object with X, Y of the last line add to the PDF 
        //          this allow the insertion of new lines after html
        pdf.save('Test.pdf');
    }, margins);
}
</script>

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
     * Load CSS for page listing mode
     * @return string
     */
    private function CSS_REQUIRED_MAIN() {
        return <<<EOF
 <!--Core CSS -->
<link rel="stylesheet" href="css/bootstrap-switch.css" />
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-datepicker/css/datepicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-timepicker/css/timepicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-daterangepicker/daterangepicker-bs3.css" />

        
<link rel="stylesheet" type="text/css" href="js/bootstrap-datetimepicker/css/datetimepicker.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />
EOF;
    }

    /**
     * Load required files for page listing
     * @return Object
     */
    protected function _LOAD_REQUIRED_MAIN() {
        return $this->JS_REQUIRED_MAIN() . $this->CSS_REQUIRED_MAIN();
    }

}
