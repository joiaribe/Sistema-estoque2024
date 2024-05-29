<?php

namespace Mov\Income;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;

/**
 * HTML used in all class
 */
class IncomeHTML extends requireds {

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
     * html header of table
     * @return string
     */
    protected function body_table() {
        return '<thead>
                    <tr>
                        <td class="center uniformjs"><input id="select-all" type="checkbox" name="delete" /></td>
                        <th>Estado</th>
                        <th>Título</th>
                        <th>Pagamento</th>
                        <th>Valor</th>
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

    private function check_status($status) {
        return ($status == true) ?
                '<span class="badge bg-success">Confirmado</span>' : '  <span class="badge bg-important">Pendente</span>';
    }

    /**
     * html conteudo da tabela
     * @access protected
     * @return string
     */
    protected function contain_table(array $fetch) {
        $value = 'R$ : ' . number_format($fetch['value'], 2, ',', '.');
        $data = \makeNiceTime::MakeNew($fetch['data']);
        $date_str = strtotime($fetch['data']);
        $date_formated = $this->dias_da_semana[date('w', $date_str)] . ", " . strftime("%d/%m/%Y ás %H:%M", $date_str);
        return <<<EOFPAGE
        <tr class="gradeX">
            <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="{$fetch['id']}" /></td>
            <td>{$this->check_status($fetch['status'])}</td>
            <td data-search="{$fetch['title']}">{$fetch['title']}</td>
            <td>{$fetch['metthod']}</td>
            <td data-order="{$fetch['value']}">{$value}</td>
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

    protected function modal_datepicker() {
        $url = URL . FILENAME;
        $date_now = date('m-d-Y');
        return <<<EOFPAGE
<!-- Modal -->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Filtrar Registros {$this->msg['plural']}</h4>
            </div>
            <div class="modal-body">
<form action="{$url}/filter" method="get" id="form_filter" class="form-horizontal ">
    <div class="form-group">
        <label class="control-label col-md-3">Valores</label>
        <div class="col-md-5">
            <div class="input-group input-large">
                <span class="input-group-addon btn-white"><i class="fa fa-usd"></i></span>
                <input type="text" value="" class="form-control" name="min" placeholder="Min">
                <span class="input-group-addon btn-white"><i class="fa fa-usd"></i></span>
                <input type="text" value="" class="form-control" name="max" placeholder="Max">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3">Período</label>
        <div class="col-md-6">
            <div class="input-group input-large">
                <input type="text" value="" class="form-control dpd1" name="from">
                <span class="input-group-addon">Até</span>
                <input type="text" value="" class="form-control dpd2" name="to">
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
EOFPAGE;
    }

    /**
     * Modal Filter when filter is active
     * @return string
     */
    protected function modal_datepicker_alt() {
        $date_now = date('m-d-Y');
        $min = filter_input(INPUT_GET, 'min');
        $max = filter_input(INPUT_GET, 'max');
        $from = filter_input(INPUT_GET, 'from');
        $to = filter_input(INPUT_GET, 'to');
        $user = filter_input(INPUT_GET, 'poster');
        $url = URL . FILENAME;
        return <<<EOFPAGE
<!-- Modal -->
<div class="modal fade" id="filter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Filtrar Registros {$this->msg['plural']}</h4>
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
            <div class="input-group input-large" data-date-format="dd/mm/yyyy">
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
                          {$this->loop_users($user)}
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
EOFPAGE;
    }

    /**
     * build tools for listing mode
     * @return string
     */
    protected function make_tools() {
        $full_url = URL . FILENAME;
        $return = <<<EOFPAGE
            <form action="{$full_url}/delete_all" id="delete_broadcast" method="POST">  
                        <div class="clearfix">                       
                            <div class="btn-group pull-right">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">Ferramentas <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
EOFPAGE;
        if ($this->loc_action['add'] !== false) {
            $return.= <<<EOFPAGE
                    <li><a title="Adicionar novo(a) {$this->msg['singular']}" href="{$full_url}{$this->loc_action['add']}">Add Novo</a></li>
                    <li><a data-toggle="modal" title="Filtrar {$this->msg['plural']}" href="{$full_url}#filter">Filtrar {$this->msg['plural']}</a></li>
                    <li class="divider"></li>
EOFPAGE;
        }
        $return.= <<<EOFPAGE
        {$this->tools_call($this->ids_tools)}
                                    <li class="divider"></li>
                                    <li><a title="Exclua múltiplas {$this->msg['plural']}" href="javascript:del_all()">Excluir {$this->msg['plural']}</a></li>
                                </ul>

    
                            </div>
                        </div>   
              <br>
EOFPAGE;
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
                                        <tbody>' . $Object['elements_table'] .
                '</tbody>
                                    </table>
                                </form>    
                                </div>
                            </div>
                        </section>';
    }

    /**
     * check sex user
     * @param str $sex
     * @return string
     */
    private function sexo($sex) {
        return $sex == 'M' ? 'Masculino' : 'Feminino';
    }

    /**
     * Check indication name user
     * @param int $id
     * @return string
     */
    private function verify_indicacao($id, $msg = '<font color="red">Sem Indicação</font>') {

        return isset($id) ? \Func::array_table('clientes', array('id' => $id), 'nome') : $msg;
    }

    /**
     * Html when metthod is debit card
     * @param array $data Query Result
     * @return string
     */
    private function html_preview_mode_debit(array $data) {
        return <<<EOF
            <tr>
                <th>Cartão:</th>
                <td>{$data['card_name']}</td>
            </tr>
            <tr>
                <th>Numero:</th>
                <td>{$data['card_number']}</td>
            </tr>
            <tr>
                <th>Agência:</th>
                 <td>{$data['card_agence']}</td>
            </tr>
EOF;
    }

    /**
     * Html when metthod is credit card
     * @param array $data Query Result
     * @return string
     */
    private function html_preview_mode_card(array $data) {
        return <<<EOF
            <tr>
                <th>Cartão:</th>
                <td>{$data['card_name']}</td>
            </tr>
            <tr>
                <th>Numero:</th>
                <td>{$data['card_number']}</td>
            </tr>
EOF;
    }

    /**
     * Html when metthod is check
     * @param array $data Query Result
     * @return string
     */
    private function html_preview_mode_check(array $data) {
        return <<<EOF
            <tr>
                <th>Cheque:</th>
                <td>{$data['cheque_number']}</td>
            </tr>
EOF;
    }

    /**
     * Check metthod name to preview table columns
     * @param array $data Query Result
     */
    private function check_metthod_payment(array $data) {
        switch ($data['metthod']) {
            case 'Cartão de Crédito':
                $result = $this->html_preview_mode_card($data);
                break;
            case 'Cheque':
                $result = $this->html_preview_mode_check($data);
                break;
            case 'Débito Automático':
                $result = $this->html_preview_mode_debit($data);
                break;
            default :
                $result = false;
                break;
        }
        return $result;
    }

    /**
     * Tables preview mode
     * @param array $param Query result
     * @return String
     */
    private function _make_list_mode_tables(array $param) {
        $name = GetInfo::_name($param['id_user']);
        $value = 'R$ : ' . number_format($param['value'], 2, ',', '.');
        $data = strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($param['data']));
        return <<<EOFPAGE
            <tr>
                <th>#ID:</th>
                <td>#{$param['id']}</td>
            </tr>
           <tr>
                <th>Cadastrado(a):</th>
                <td>{$name}</td>
            </tr>
            <tr>
                <th>Estado:</th>
                <td>{$this->check_status($param['status'])}</td>
            </tr>
            <tr>
                <th>Título:</th>
                <td>{$param['title']}</td>
            </tr>
            <tr>
                <th>Valor:</th>
                <td>{$value}</td>
            </tr>
             <tr>
                <th title="Método de Pagamento">Pagamento:</th>
                <td>{$param['metthod']}</td>
            </tr>
            {$this->check_metthod_payment($param)}
            <tr>
                <th>Descrição:</th>
                <td>{$this->check_field($param['descri'], '<font color="red"><b>Sem Descrição !</b></font>')}</td>
            </tr>
       
            <tr>
                <th>Cadastrado:</th>
                <td>{$data}</td>
            </tr>
EOFPAGE;
    }

    /**
     * Make row HTML listing mode
     * @param array $data Data Query
     * @return String
     */
    protected function MAKE_PREVIEW_MODE(array $data) {
        $id = Url::getURL($this->URL_ACTION + 1);
        return '
<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Visualizar ' . $this->msg['singular'] . '
                <span class="tools pull-right">
                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" class="fa fa-times"></a>
                </span>
            </header>
            <div class="panel-body">
                <div class="adv-table">
                    <!-- Content Area -->
                    <div id="da-content-area">
                        <div class="grid_4">
                            <div class="da-panel collapsible">
                                <div class="da-panel-header">
                                    <span class="da-panel-title">' . \Func::array_table('notifier', array('id' => $id), 'title') . '</span>
                                </div>

                                <div class="da-panel-content">
                                    <table class="da-table da-detail-view">
                                        <tbody>' . $this->_make_list_mode_tables($data) .
                '</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>	
            </div>
            <!-- // Content END -->
    </div>
</div>';
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
                ->from($this->table)
                ->group_by('id_user')
                ->run();
        $result = '<option value="">-- Todos --</option>';
        if ($q) {
            $users = $q->get_selected();
            foreach ($users as $user) {
                if ($data !== false) {
                    $result.= '<option value="' . $user['id'] . '"' . $this->_check_selected($user['id'], $data) . '>' . \GetInfo::_name($user['id_user']) . '</option>';
                } else {
                    $result.= '<option value="' . $user['id'] . '">' . \GetInfo::_name($user['id_user']) . '</option>';
                }
            }
            return $result;
        }
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
     * Right Elements Insert mode
     * @return String
     */
    private function RIGHT_ELEMENTS_INSER_NEW() {
        $now = date('d-m-Y H:m');
        return <<<EOF
<div class="col-md-5">
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4" title="Método de Pagamento">Pagamento<span id="field-required">*</span></label>
        <div class="col-md-6">
                 <select id="e2" name="metthod" class="populate" style="width: 210px">
                    <option value="Dinheiro">Dinheiro</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Débito Automático">Débito Automático</option>
                    <option value="Cheque">Cheque</option>
                 </select>
        </div>
    </div>
<div id="only_card">      
        <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Cartão<span id="field-required">*</span></label>
        <div class="col-md-6">
               <select id="e1" name="card_name" class="populate" style="width: 210px">
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
         <label for="firstname" class="control-label col-lg-4">Numero<span id="field-required">*</span></label>
        <div class="col-md-6">
              <input type="text" name="card_number" class="form-control">
        </div>
    </div> 
</div> 
<div id="only_ag">       
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Agência<span id="field-required">*</span></label>
        <div class="col-md-6">
              <input type="text" name="agencia" class="form-control">
        </div>
    </div>
</div>  
<div id="only_check">
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Numero<span id="field-required">*</span></label>
        <div class="col-md-6">
              <input type="text" name="cheque_number" class="form-control">
        </div>
    </div>  
</div>  
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Valor<span id="field-required">*</span></label>
        <div class="col-md-6">
              <input type="text" name="valor" class="form-control money">
        </div>
    </div> 
        
    <div class="form-group">
        <label class="control-label col-md-4">Data<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input size="16" type="text" name="horario" value="{$now}" class="form_datetime form-control">
        </div>
    </div>   

</div>
EOF;
    }

    /**
     * Left Elements Insert mode
     * @return String
     */
    private function LEFT_ELEMENTS_INSER_NEW() {
        return <<<EOF
        <div class="col-md-5">   
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Estado<span id="field-required">*</span></label>
                <div class="col-md-6">
                         <input type="checkbox" id="icheck" name="icheck" checked data-on-label="X" data-off-label="O">
                          <span class="help-block status_txt"></span>
                </div>
            </div> 
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Título<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div> 
         
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Descrição</label>
                <div class="col-md-6">
                     <textarea class="form-control" id="ccomment" name="comment"></textarea>
                </div>
            </div> 
</div>
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

    /**
     * Buttons Form
     * @param string $main Name main buttom
     * @return String
     */
    private function Button_Form($main = 'Adicionar') {
        return <<<EOF
        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-6">
                <button class="btn btn-primary" type="submit">{$main}</button>

    
                <button class="btn btn-default" type="reset">Cancelar</button>
            </div>
        </div>
EOF;
    }

    /**
     * Full HTML for page Insert New
     * @return string
     */
    protected function HTML_Insert_New() {
        $URL = URL . FILENAME . DS . Url::getURL($this->URL_ACTION) . DS;
        return <<<EOF
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">Adicionar {$this->msg['singular']}
                                <span class="tools pull-right">
                                    <a class="fa fa-chevron-down" href="javascript:;"></a>
                                    <a class="fa fa-times" href="javascript:;"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                <div class="form">
                                    <form class="cmxform form-horizontal" id="signupForm" method="post" enctype="multipart/form-data" action="{$URL}new">
                                        {$this->LEFT_ELEMENTS_INSER_NEW()}
                                        {$this->RIGHT_ELEMENTS_INSER_NEW()}
                                        <div class="clearfix"></div>
                                        {$this->Button_Form()}
                                    </form>
                                </div>

    
                            </div>
                        </section>
                    </div>
                </div>
EOF;
    }

    /**
     * Left Elements Update mode
     * @param array $data
     * @return String
     */
    private function LEFT_ELEMENTS_Update($data) {
        return <<<EOF
<div class="col-md-5">
           <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Estado<span id="field-required">*</span></label>
                <div class="col-md-6">
                         <input type="checkbox" id="icheck" name="icheck" {$this->_check_checked(true, $data['status'])} data-on-label="X" data-off-label="O">
                          <span class="help-block status_txt"></span>
                </div>
            </div> 
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Título<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" value="{$data['title']}" id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div> 
         
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Descrição</label>
                <div class="col-md-6">
                     <textarea class="form-control" id="ccomment" name="comment">{$data['descri']}</textarea>
                </div>
            </div> 
</div>
EOF;
    }

    /**
     * Right Elements Update mode
     * @param array $data
     * @return String
     */
    private function RIGHT_ELEMENTS_Update($data) {
        $date = date('d-m-Y H:m', strtotime($data['data']));

        return <<<EOF
<div class="col-md-5">
   <div class="form-group">
         <label for="firstname" class="control-label col-lg-4" title="Método de Pagamento">Pagamento<span id="field-required">*</span></label>
        <div class="col-md-6">
                 <select id="e2" name="metthod" class="populate" style="width: 210px">
                    <option {$this->_check_selected('Dinheiro', $data['metthod'])} value="Dinheiro">Dinheiro</option>
                    <option {$this->_check_selected('Cartão de Crédito', $data['metthod'])} value="Cartão de Crédito">Cartão de Crédito</option>
                    <option {$this->_check_selected('Débito Automático', $data['metthod'])} value="Débito Automático">Débito Automático</option>
                    <option {$this->_check_selected('Cheque', $data['metthod'])} value="Cheque">Cheque</option>
                 </select>
        </div>
    </div>
<div id="only_card">      
        <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Cartão<span id="field-required">*</span></label>
        <div class="col-md-6">
               <select id="e1" name="card_name" class="populate" style="width: 210px">
                    <option {$this->_check_selected('American Express', $data['card_name'])} value="American Express">American Express</option>
                    <option {$this->_check_selected('Diners Club', $data['card_name'])} value="Diners Club">Diners Club</option>
                    <option {$this->_check_selected('MasterCard', $data['card_name'])} value="MasterCard">MasterCard</option>
                    <option {$this->_check_selected('Visa', $data['card_name'])} value="Visa">Visa</option>
                    <option {$this->_check_selected('Maestro', $data['card_name'])} value="Maestro">Maestro</option>
                    <option {$this->_check_selected('Amex', $data['card_name'])} value="Amex">Amex</option>
                    <option {$this->_check_selected('Outros', $data['card_name'])} value="Outros">Outros</option>
                 </select>
        </div>
    </div>     
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Numero<span id="field-required">*</span></label>
        <div class="col-md-6">
              <input type="text" value="{$data['card_number']}" name="card_number" class="form-control">
        </div>
    </div> 
</div> 
<div id="only_ag">       
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Agência<span id="field-required">*</span></label>
        <div class="col-md-6">
              <input type="text" value="{$data['card_agence']}" name="agencia" class="form-control">
        </div>
    </div>
</div>  
<div id="only_check">
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Numero<span id="field-required">*</span></label>
        <div class="col-md-6">
              <input type="text" value="{$data['cheque_number']}" name="cheque_number" class="form-control">
        </div>
    </div>  
</div>  
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Valor<span id="field-required">*</span></label>
        <div class="col-md-6">
              <input type="text" value="{$this->tratar_numero($data['value'])}" name="valor" class="form-control money">
        </div>
    </div>    
              
    <div class="form-group">
        <label class="control-label col-md-4">Data<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input size="16" type="text" name="horario" value="{$date}" class="form_datetime form-control">
        </div>
    </div>  

</div>
EOF;
    }

    /**
     * isso é pra enviar um bug com a mascara jquery
     * então verifica se existe . que no caso é float (fração) caso não ache acressenta 00
     * @access private
     * @param $num
     * @return float
     */
    private function tratar_numero($num) {
        return (strpos($num, '.') == false) ? $num . '00' : $num;
    }

    /**
     * Center Elements Update mode
     * @param array $data
     * @return String
     */
    private function CENTER_ELEMENTS_Update($data) {
        return <<<EOFPAGE
<div class="col-md-12">
    <div class="form-group">
        <label for="firstname" class="control-label col-lg-2">Descrição</label>
        <div class="col-md-12">
             <textarea class="wysihtml5 form-control" name="text" rows="9" required>{$data['Obs']}</textarea>
        </div>
    </div>
</div>
EOFPAGE;
    }

    /**
     * Full HTML for page Insert New
     * @param array $dados Query Result
     * @return string
     */
    protected function HTML_Update($dados) {
        $URL = URL . FILENAME . DS . Url::getURL($this->URL_ACTION) . DS . $dados['id'] . DS;
        return <<<EOF
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">Alterar {$this->msg['singular']}
                                <span class="tools pull-right">
                                    <a class="fa fa-chevron-down" href="javascript:;"></a>
                                    <a class="fa fa-times" href="javascript:;"></a>
                                </span>
                            </header>
                            <div class="panel-body">
                                <div class="form">
                                    <form class="cmxform form-horizontal" id="signupForm" method="post" enctype="multipart/form-data" action="{$URL}update">
                                        {$this->LEFT_ELEMENTS_Update($dados)}
                                        {$this->RIGHT_ELEMENTS_Update($dados)}
                                        <div class="clearfix"></div>
                                        {$this->Button_Form('Alterar')}
                                    </form>

    
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
EOF;
    }

}

/**
 * Class Required CSS and Javascript
 * @todo put in array the path files
 */
class requireds extends IncomeConfig {

    /**
     * Load JS for page listing mode
     * @return string
     */
    private function JS_REQUIRED_LISTING() {

        return <<<EOF
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
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

<script src="js/jquery-tags-input/jquery.tagsinput.js"></script>

<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<!--dynamic table-->
<script type="text/javascript" language="javascript" src="js/advanced-datatable/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/data-tables/DT_bootstrap.js"></script>

<script src="js/advanced-form.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>
<!--dynamic table initialization -->
<script src="js/dynamic_table_init.js"></script><!--dynamic table-->
<!-- Jquery Mask -->
<script type="text/javascript" src="js/mask/jquery.mask.js"></script>
<script type="text/javascript" src="js/mask.js"></script>
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
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-datepicker/css/datepicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-timepicker/css/timepicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-daterangepicker/daterangepicker-bs3.css" />

        
<link rel="stylesheet" type="text/css" href="js/bootstrap-datetimepicker/css/datetimepicker.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />
EOF;
    }

    /**
     * Load all CSS required for page Insert Mode
     * @return String
     */
    private function CSS_REQUIRED_INSERT() {
        return <<<EOF
    <link rel="stylesheet" href="css/bootstrap-switch.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-fileupload/bootstrap-fileupload.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />

    <link rel="stylesheet" type="text/css" href="js/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-timepicker/css/timepicker.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-colorpicker/css/colorpicker.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-datetimepicker/css/datetimepicker.css" />

    <link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />
    <link rel="stylesheet" type="text/css" href="js/jquery-tags-input/jquery.tagsinput.css" />

    <link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
    <link href="css/jquery.tagit.css" rel="stylesheet" type="text/css">
    <link href="css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">
    
        
EOF;
    }

    /**
     * Load all JS for page Insert mode
     * @return string
     */
    private function JS_REQUIRED_INSERT() {
        return <<<EOF
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<script src="js/bootstrap-switch.js"></script>

<script type="text/javascript" src="js/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="js/bootstrap-fileupload/bootstrap-fileupload.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

<script type="text/javascript" src="js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="js/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>

<script type="text/javascript" src="js/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.quicksearch.js"></script>

<script type="text/javascript" src="js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>

<script src="js/jquery-tags-input/jquery.tagsinput.js"></script>

<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<script src="js/toggle-init.js"></script>

<script src="js/advanced-form.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>  
<!--this page script-->

        
<script src="js/validation-init.js"></script>
<!-- The real deal -->
<script src="js/tag-it.js" type="text/javascript" charset="utf-8"></script>
<script type='text/javascript'>    
    $(".status_txt").text('Pago');
    $("#icheck").change(function() {
        if ($(this).attr("checked")) {
            $(".status_txt").text('Pago');
            return;
        }else{
            $(".status_txt").text('Pendente');
        }
    });

   $("#only_check").hide();    
   $("#only_card").hide();    
   $("#only_ag").hide();    

   $(document).ready(function(){
        $("select").change(function(){
            $( "select option:selected").each(function(){
                if($(this).attr("value")=="Cheque"){
                    $("#only_card").fadeOut(); 
                    $("#only_ag").fadeOut();
                    $("#only_check").fadeIn();
                    $('#only_card').find('input:text').val(''); 
                    $('#only_ag').find('input:text').val(''); 
                }
                if($(this).attr("value")=="Cartão de Crédito"){
                    $("#only_ag").fadeOut();
                    $("#only_card").fadeIn();
                    $('#only_ag').find('input:text').val(''); 
                }
                if($(this).attr("value")=="Débito Automático"){
                    $("#only_check").fadeOut();
                    $("#only_ag").fadeIn();
                    $("#only_card").fadeIn();
                    $('#only_check').find('input:text').val(''); 
                }
                if($(this).attr("value")=="Dinheiro"){
                    $("#only_check").fadeOut();
                    $("#only_ag").fadeOut();
                    $("#only_card").fadeOut();
                    $('#only_check').find('input:text').val(''); 
                    $('#only_ag').find('input:text').val(''); 
                    $('#only_card').find('input:text').val(''); 
                }
            });
        }).change();
    });
</script>
<!-- Jquery Mask -->
<script type="text/javascript" src="js/mask/jquery.mask.js"></script>
<script type="text/javascript" src="js/mask.js"></script>
EOF;
    }

    /**
     * Load all JS for page Insert mode
     * @return string
     */
    private function JS_REQUIRED_UPDATE($data) {
        # echo 'link : ' . $data['link'];

        return <<<EOF
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<script src="js/bootstrap-switch.js"></script>

<script type="text/javascript" src="js/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="js/bootstrap-fileupload/bootstrap-fileupload.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

<script type="text/javascript" src="js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="js/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>

<script type="text/javascript" src="js/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.quicksearch.js"></script>

<script type="text/javascript" src="js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>

<script src="js/jquery-tags-input/jquery.tagsinput.js"></script>

<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<script src="js/toggle-init.js"></script>

<script src="js/advanced-form.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>

        
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>   
<!--this page script-->
<script src="js/validation-init.js"></script>
<script type='text/javascript'>    
    $(".status_txt").text('Pago');
    $("#icheck").change(function() {
        if ($(this).attr("checked")) {
            $(".status_txt").text('Pago');
            return;
        }else{
            $(".status_txt").text('Pendente');
        }
    });

   $("#only_check").hide();    
   $("#only_card").hide();    
   $("#only_ag").hide();    

   $(document).ready(function(){
        $("select").change(function(){
            $( "select option:selected").each(function(){
                if($(this).attr("value")=="Cheque"){
                    $("#only_card").fadeOut(); 
                    $("#only_ag").fadeOut();
                    $("#only_check").fadeIn();
                    $('#only_card').find('input:text').val(''); 
                    $('#only_ag').find('input:text').val(''); 
                }
                if($(this).attr("value")=="Cartão de Crédito"){
                    $("#only_ag").fadeOut();
                    $("#only_card").fadeIn();
                    $('#only_ag').find('input:text').val(''); 
                }
                if($(this).attr("value")=="Débito Automático"){
                    $("#only_check").fadeOut();
                    $("#only_ag").fadeIn();
                    $("#only_card").fadeIn();
                    $('#only_check').find('input:text').val(''); 
                }
                if($(this).attr("value")=="Dinheiro"){
                    $("#only_check").fadeOut();
                    $("#only_ag").fadeOut();
                    $("#only_card").fadeOut();
                    $('#only_check').find('input:text').val(''); 
                    $('#only_ag').find('input:text').val(''); 
                    $('#only_card').find('input:text').val(''); 
                }
            });
        }).change();
    });
</script>
<!-- Jquery Mask -->
<script type="text/javascript" src="js/mask/jquery.mask.js"></script>
<script type="text/javascript" src="js/mask.js"></script>
EOF;
    }

    /**
     * Load all JS for page Preview mode
     * @return string
     */
    private function JS_REQUIRED_PREVIEW() {
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
EOF;
    }

    /**
     * Load CSS for page Preview mode
     * @return string
     */
    private function

    CSS_REQUIRED_PREVIEW() {
        return <<<EOF
                <link href="css/preview.css" rel="stylesheet" />
EOF;
    }

    /**
     * Load required files for page Insert New
     * @return Object
     */
    protected function

    _LOAD_REQUIRED_INSERT() {
        return $this->CSS_REQUIRED_INSERT() . $this->JS_REQUIRED_INSERT();
    }

    protected function

    _LOAD_REQUIRED_UPDATE($data) {
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
    protected function _REQUIRED_PREVIEW_MODE() {
        return $this->JS_REQUIRED_PREVIEW() . $this->CSS_REQUIRED_PREVIEW();
    }

}
