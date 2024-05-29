<?php

namespace Manager\Fonts;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class FontsHTML extends requireds {

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
                        <th>Título</th>
                        <th>Banco</th>
			<th>Agência</th>	
			<th>Conta</th>
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

    /**
     * html conteudo da tabela
     * @access protected
     * @return string
     */
    protected function contain_table(array $fetch) {

        $data = \makeNiceTime::MakeNew($fetch['data']);
        $date_str = strtotime($fetch['data']);
        $date_formated = $this->dias_da_semana[date('w', $date_str)] . ", " . strftime("%d/%m/%Y ás %H:%M", $date_str);
        return <<<EOFPAGE
        <tr class="gradeX">
            <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="{$fetch['Id']}" /></td>
            <td>{$fetch['titulo']}</td>
            <td>{$this->check_field($fetch['banco'])}</td>
	    <td>{$this->check_field($fetch['agencia'])}</td>	
	    <td>{$this->check_field($fetch['conta'])}</td>
	    <td data-order="{$date_str}"><a title="$date_formated">{$data}</a></td>	
            <td class="right actions">{$this->build_buttons($fetch['Id'])}</td>
        </tr>
EOFPAGE;
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
                $result.= '<li><a title="' . $this->msg['manager'] . ' ' . $dados['name'] . '" href="' . $dados['link'] . '">' . $this->msg['manager'] . ' ' . $dados['name'] . '</a></li>';
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
                                    ' . Func::ToolsCall($this->ids_tools) . '
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
     * Tables preview mode
     * @param array $param Query result
     * @return String
     */
    private function _make_list_mode_tables(array $param) {
        if ($param['banco'] == 'Banco Do Brasil') {
            $result = '<tr>
                <th>Convênio:</th>
                <td>' . $this->check_field($param['Convenio']) . '</td>
            </tr>';
        } elseif ($param['banco'] == 'Itau' &&
                $param['carteira'] == 107 ||
                $param['carteira'] == 122 ||
                $param['carteira'] == 142 ||
                $param['carteira'] == 143 ||
                $param['carteira'] == 196 ||
                $param['carteira'] == 198
        ) {
            $result = '
    	    <tr>
                <th>Código do Cliente:</th>
                <td>' . $this->check_field($param['codigoCliente']) . '</td>
            </tr>
		<tr>
                <th>Numero do Documento:</th>
                <td>' . $this->check_field($param['numeroDocumento']) . '</td>
            </tr>';
        } else {
            $result = NULL;
        }
        return '            
            <tr>
                <th>#ID:</th>
                <td>#' . $param['Id'] . '</td>
            </tr>
            <tr>
                <th>Nome:</th>
                <td>' . $param['titulo'] . '</td>
            </tr>
            <tr>
                <th>Conta:</th>
                <td>' . $param['conta'] . '</td>
            </tr>
            <tr>
                <th>Agência:</th>
                <td>' . $param['agencia'] . '</td>
            </tr>
	   <tr>
                <th>Banco:</th>
                <td>' . $param['banco'] . '</td>
            </tr>
          	' . $result . '
            <tr>
                <th>Cadastrado:</th>
                <td>' . strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($param['data'])) . '</td>
            </tr>';
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
                                    <span class="da-panel-title">Visualizar : ' . \Func::array_table($this->table, array('id' => $id), 'titulo') . '</span>
                                </div>

                                <div class="da-panel-content">
                                    <table class="da-table da-detail-view">
                                        <tbody>' . $this->_make_list_mode_tables($data) . '</tbody>
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
     * Left Elements Insert mode
     * @return String
     */
    private function LEFT_ELEMENTS_INSER_NEW() {
        return <<<EOF
        <div class="col-md-6">        
            <div class="form-group">
                <label for="nome" class="control-label col-md-5">Título<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div>
       
            <div class="form-group">
                <label for="firstname" class="control-label col-lg-5">Banco</label>
                <div class="col-md-6">
                         <select id="e1" name="bank" style="width:100%" class="populate">
				<option value="Banco Do Brasil">Banco Do Brasil</option>
				<option value="Bradesco">Bradesco</option>
				<option value="Caixa">Caixa</option>
				<option value="Itau">Itau</option>
				<option value="Santander">Santander</option>
                        </select>  
                </div>
            </div>    

            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Agência<span id="field-required">*</span></label>
                <div class="col-md-6">
                         <input type="text" name="agence" id="agence" class="form-control">
                </div>
		<button data-original-title="Numero da Agência" id="help_agence" data-content="" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div>  

            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Conta<span id="field-required">*</span></label>
                <div class="col-md-6">
                        <input class="form-control" id="account" type="text" name="account" required />
                </div>
		<button data-original-title="Numero da Conta" id="help_account" data-content="" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div> 
	<div id="convenio">
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Convênio<span id="field-required">*</span></label>
                <div class="col-md-6">
                      <input type="text" name="codAgreement" id="codAgreement" class="form-control">
                </div>
		<button data-original-title="Convênio" id="cod_agreement" data-content="Até 4, 6 ou 7 dígitos" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div> 
	</div> 
        <div id="wallet">
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Carteira<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input type="number" name="codWallet" id="codWallet" class="form-control">
                </div>
		<button data-original-title="Carteira" id="cod_wallet" data-content="Quantidade de Parcelas." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div>  
	</div>
	<div id="cod_client">
	   <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Código do Cliente<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input type="number" name="codClient"  class="form-control">
                </div>
		<button data-original-title="Código do Cliente" id="cod_client" data-content="Código do cliente." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div> 
	</div>
	<div id="document_number">
	    <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Numero do Documento<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input type="number" name="numDocument" class="form-control">
                </div>
		<button data-original-title="Numero do Documento" id="num_document" data-content="Número do documento." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
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
        <div class="col-md-10">        
            <div class="form-group">
                <label for="nome" class="control-label col-md-5">Título<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" id="cname" name="name" value="{$data['titulo']}" minlength="2" type="text" required />
                </div>
            </div>
       
            <div class="form-group">
                <label for="firstname" class="control-label col-lg-5">Banco</label>
                <div class="col-md-6">
                         <select id="e1" name="bank" style="width:100%" class="populate">
				<option value="Banco Do Brasil" {$this->_check_selected("Banco Do Brasil", $data['banco'])}>Banco Do Brasil</option>
				<option value="Bradesco" {$this->_check_selected("Bradesco", $data['banco'])}>Bradesco</option>
				<option value="Caixa" {$this->_check_selected("Caixa", $data['banco'])}>Caixa</option>
				<option value="Itau" {$this->_check_selected("Itau", $data['banco'])}>Itau</option>
				<option value="Santander" {$this->_check_selected("Santander", $data['banco'])}>Santander</option>
                        </select>  
                </div>
            </div>    

            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Agência<span id="field-required">*</span></label>
                <div class="col-md-6">
                         <input type="text" name="agence" id="agence" value="{$data['agencia']}" class="form-control">
                </div>
		<button data-original-title="Numero da Agência" id="help_agence" data-content="" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div>  

            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Conta<span id="field-required">*</span></label>
                <div class="col-md-6">
                        <input class="form-control" id="account" type="text" value="{$data['conta']}" name="account" required />
                </div>
		<button data-original-title="Numero da Conta" id="help_account" data-content="" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div> 
	<div id="convenio">
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Convênio<span id="field-required">*</span></label>
                <div class="col-md-6">
                      <input type="text" name="codAgreement" id="codAgreement" value="{$data['Convenio']}" class="form-control">
                </div>
		<button data-original-title="Convênio" id="cod_agreement" data-content="Até 4, 6 ou 7 dígitos" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div> 
	</div> 
        <div id="wallet">
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Carteira<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input type="number" name="codWallet" id="codWallet" value="{$data['carteira']}" class="form-control">
                </div>
		<button data-original-title="Carteira" id="cod_wallet" data-content="Quantidade de Parcelas." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div>  
	</div>
	<div id="cod_client">
	   <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Código do Cliente<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input type="number" name="codClient" value="{$data['codigoCliente']}"  class="form-control">
                </div>
		<button data-original-title="Código do Cliente" id="cod_client" data-content="Código do cliente." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div> 
	</div>
	<div id="document_number">
	    <div class="form-group">
                 <label for="firstname" class="control-label col-lg-5">Numero do Documento<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input type="number" name="numDocument" value="{$data['numeroDocumento']}" class="form-control">
                </div>
		<button data-original-title="Numero do Documento" id="num_document" data-content="Número do documento." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button> 
            </div> 
	           
        </div>
</div>


EOF;
    }

    /**
     * Full HTML for page Insert New
     * @param array $dados Query Result
     * @return string
     */
    protected function HTML_Update($dados) {
        $URL = URL . FILENAME . DS . Url::getURL($this->URL_ACTION) . DS . $dados['Id'] . DS;
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
class requireds extends FontsConfig {

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

<script type="text/javascript">
    $(document).ready(function() {
        $("select").change(function () {
            $("select option:selected").each(function () {
                if ($(this).attr("value") == "Banco Do Brasil") {
                    $("#convenio").fadeIn();
                    $("#wallet").fadeIn();
        
                    $("#help_agence").attr("data-content",'Agencia até 4 dígitos');
                    $("#help_account").attr("data-content",'Numero da conta até 8 dígitos');
        
                    $("#cod_client").fadeOut();
                    $("#document_number").fadeOut();
        
                }
                if ($(this).attr("value") == "Bradesco") {
                    $("#wallet").fadeIn();
        
                    $("#help_agence").attr("data-content",'Agencia até 4 dígitos');
                    $("#help_account").attr("data-content",'Numero da conta até 7 dígitos');
                    $("#cod_wallet").attr("data-content",'Só pode ser usado os números 3, 6 ou 9');
        
                    $("#convenio").fadeOut();
                    $("#cod_client").fadeOut();
                    $("#document_number").fadeOut();
                }
                if ($(this).attr("value") == "Caixa") {
                    $("#codWallet")
                    .replaceWith('<select id="codWallet" name="codWallet" class="form-control">' +
                      '<option value="SR">Sem Registro</option>' +
                      '<option value="RG">Registrada</option>' +
                    '</select>');
                    
                    $("#help_agence").attr("data-content",'Agencia');
                    $("#help_account").attr("data-content",'Numero da conta');
                    $("#cod_wallet").attr("data-content",'Exemplo SR');
                    
                    $("#wallet").fadeIn();
                    $("#convenio").fadeOut();
                    $("#cod_client").fadeOut();
                    $("#document_number").fadeOut();
                }
        
                if ($(this).attr("value") == "Itau") {
                    $("#wallet").fadeIn();
        
                    $("#help_agence").attr("data-content",'Agencia até 4 dígitos');
                    $("#help_account").attr("data-content",'Numero da conta até 5 dígitos');
                    $("#cod_wallet").attr("data-content",'Até 3 dígitos Caso os números sejam 107, 122, 142, 143, 196 ou 198  campos código do cliente e numero do documento são campos obrigatórios');
        
                     $("#codWallet").change(function () {
                             if ($(this).attr("value") == 107 || 
                                 $(this).attr("value") == 122 ||
                                 $(this).attr("value") == 142 ||
                                 $(this).attr("value") == 143 ||
                                 $(this).attr("value") == 196 ||
                                 $(this).attr("value") == 198
                                ) {
                                $("#cod_client").fadeIn();
                                $("#document_number").fadeIn();
        
                                $("#convenio").fadeOut();
                            }else{
                                $("#convenio").fadeOut();
                                $("#cod_client").fadeOut();
                                $("#document_number").fadeOut();
                            }
                     });
        
        
                }
                if ($(this).attr("value") == "Santander") {
                    $("#wallet").fadeIn();
                    $("#help_agence").attr("data-content",'Agencia até 4 dígitos');
                    $("#help_account").attr("data-content",'Numero da conta até 7 dígitos');
                    $("#cod_wallet").attr("data-content",'Só pode ser usado os números 101, 102 ou 201');
                    $("#convenio").fadeOut();
                    $("#cod_client").fadeOut();
                    $("#document_number").fadeOut();
                }
            });
        }).change();
    });
</script>
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

<script type="text/javascript">
    $(document).ready(function() {
        $("select").change(function () {
            $("select option:selected").each(function () {
                if ($(this).attr("value") == "Banco Do Brasil") {
                    $("#convenio").fadeIn();
                    $("#wallet").fadeIn();
        
                    $("#help_agence").attr("data-content",'Agencia até 4 dígitos');
                    $("#help_account").attr("data-content",'Numero da conta até 8 dígitos');
        
                    $("#cod_client").fadeOut();
                    $("#document_number").fadeOut();
        
                }
                if ($(this).attr("value") == "Bradesco") {
                    $("#wallet").fadeIn();
        
                    $("#help_agence").attr("data-content",'Agencia até 4 dígitos');
                    $("#help_account").attr("data-content",'Numero da conta até 7 dígitos');
                    $("#cod_wallet").attr("data-content",'Só pode ser usado os números 3, 6 ou 9');
        
                    $("#convenio").fadeOut();
                    $("#cod_client").fadeOut();
                    $("#document_number").fadeOut();
                }
                if ($(this).attr("value") == "Caixa") {
                    $("#codWallet")
                    .replaceWith('<select id="codWallet" name="codWallet" class="form-control">' +
                      '<option value="SR">Sem Registro</option>' +
                      '<option value="RG">Registrada</option>' +
                    '</select>');
                    $("#help_agence").attr("data-content",'Agencia');
                    $("#help_account").attr("data-content",'Numero da conta');
                    $("#cod_wallet").attr("data-content",'Exemplo SR');
        
                    $("#wallet").fadeIn();
                    $("#convenio").fadeOut();
                    $("#cod_client").fadeOut();
                    $("#document_number").fadeOut();
                }
        
                if ($(this).attr("value") == "Itau") {
                    $("#wallet").fadeIn();
        
                    $("#help_agence").attr("data-content",'Agencia até 4 dígitos');
                    $("#help_account").attr("data-content",'Numero da conta até 5 dígitos');
                    $("#cod_wallet").attr("data-content",'Até 3 dígitos Caso os números sejam 107, 122, 142, 143, 196 ou 198  campos código do cliente e numero do documento são campos obrigatórios');
        
                     $("#codWallet").change(function () {
                             if ($(this).attr("value") == 107 || 
                                 $(this).attr("value") == 122 ||
                                 $(this).attr("value") == 142 ||
                                 $(this).attr("value") == 143 ||
                                 $(this).attr("value") == 196 ||
                                 $(this).attr("value") == 198
                                ) {
                                $("#cod_client").fadeIn();
                                $("#document_number").fadeIn();
        
                                $("#convenio").fadeOut();
                            }else{
                                $("#convenio").fadeOut();
                                $("#cod_client").fadeOut();
                                $("#document_number").fadeOut();
                            }
                     });
        
        
                }
                if ($(this).attr("value") == "Santander") {
                    $("#wallet").fadeIn();
                    $("#help_agence").attr("data-content",'Agencia até 4 dígitos');
                    $("#help_account").attr("data-content",'Numero da conta até 7 dígitos');
                    $("#cod_wallet").attr("data-content",'Só pode ser usado os números 101, 102 ou 201');
                    $("#convenio").fadeOut();
                    $("#cod_client").fadeOut();
                    $("#document_number").fadeOut();
                }
            });
        }).change();
    });
</script>
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
    private function CSS_REQUIRED_PREVIEW() {
        return <<<EOF
                <link href="css/preview.css" rel="stylesheet" />
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
    protected function _REQUIRED_PREVIEW_MODE() {
        return $this->JS_REQUIRED_PREVIEW() . $this->CSS_REQUIRED_PREVIEW();
    }

}
