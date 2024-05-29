<?php

namespace Manager\Users;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class UsersHTML extends requireds {

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
     * html header of table
     * @return string
     */
    protected function body_table() {
        return '<thead>
                    <tr>
                        <td class="center uniformjs"><input id="select-all" type="checkbox" name="delete" /></td>
                        <th>#ID</th>
                        <th>Login</th>
                        <th>Nome</th>
                        <th>Ocupação</th>
                        <th>E-mail</th>
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
     * Check CPF or CPNJ
     * @param int $cpnj
     * @param int $cpf
     * @return string
     */
    private function verify_cpf_or_cpnj($cpnj, $cpf) {
        if (isset($cpnj)) {
            return $cpnj;
        } elseif (isset($cpf)) {
            return $cpf;
        }
        return '<font style="color: red">Não encontrado</font>';
    }

    /**
     * * verifica se existe um registro
     * * @access private
     * * @return string
     */
    private function verify_result($data) {
        return isset($data) ? $data : '<font style="color: red">Não Encontrado</font>';
    }

    /**
     * html conteudo da tabela
     * @access protected
     * @return string
     */
    protected function contain_table(array $fetch) {
        $name = GetInfo::_name($fetch['user_id']);
        $account_type = GetInfo::_user_cargo($fetch['user_id']);


        $data = \makeNiceTime::MakeNew($fetch['user_creation_timestamp']);
        $date_str = strtotime($fetch['user_creation_timestamp']);
        $date_formated = $this->dias_da_semana[date('w', $date_str)] . ", " . strftime("%d/%m/%Y ás %H:%M", $date_str);

        return <<<EOFPAGE
<tr class="gradeX">
    <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="{$fetch['user_id']}" /></td>
    <td>#{$fetch['user_id']}</td>
    <td>{$fetch['user_name']}</td>
    <td>{$name}</td>
    <td>{$account_type}</td>
    <td><a href="mailto:{$fetch['user_email']}">{$this->verify_result($fetch['user_email'])}</a></td>	
    <td data-order="{$date_str}"><a title="$date_formated">{$data}</a></td>	
    <td class="right actions">{$this->build_buttons($fetch['user_id'])}</td>
</tr>
EOFPAGE;
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
        $return.= Func::ToolsCall($this->ids_tools) . '
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
     * verifica se existe dados
     * @access private
     * @param Unknow $dados dados obtidos 
     * @return string
     */
    private function verify_dados($dados) {
        return !$dados ? '<span class="badge bg-important">Não Encontrado</span>' : $dados;
    }

    /**
     * Tables preview mode
     * @param array $param Query result
     * @return String
     */
    private function _make_list_mode_tables(array $param) {
        $data = strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($param['user_creation_timestamp']));
        return '            
                <tr>
                    <th>#ID:</th>
                    <td>#' . $param['user_id'] . '</td>
                </tr>
                <tr>
                    <th>Nome:</th>
                    <td>' . $param['user_first_name'] . '</td>
                </tr>
                <tr>
                    <th>Sobrenome:</th>
                    <td>' . $param['user_last_name'] . '</td>
                </tr>
               
                <tr>
                    <th>Ocupação:</th>
                    <td>' . GetInfo::_user_cargo($param['user_id']) . '</td>
                </tr>
                <tr>
                    <th>Login:</th>
                    <td>' . $param['user_name'] . '</td>
                </tr>
                <tr>
                    <th>E-mail:</th>
                    <td>' . $this->verify_dados($param['user_email']) . '</td>
                </tr>
                <tr>
                    <th>IP:</th>
                    <td>' . $this->verify_dados($param['user_registration_ip']) . '</td>
                </tr>
                <tr>
                    <th>Registrado : </th>
                    <td>' . $data . '</td>
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
                                    <span class="da-panel-title">' . \Func::array_table('notifier', array('id' => $id), 'title') . '</span>
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
     * loop indication user
     * @param mixed $data If false shows all, if you have an id, check what is selected
     * @return string
     */
    private function loop_account_type($data = false) {
        $result = '';
        foreach (GetInfo::$names_acc_type as $k => $v) {
            if ($data !== false) {
                $result.= '<option value="' . $k . '"' . $this->_check_selected($k, $data) . '>' . $v['Name'] . '</option>';
            } else {
                $result.= '<option value="' . $k . '">' . $v['Name'] . '</option>';
            }
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
     * Left Elements Insert mode
     * @return String
     */
    private function LEFT_ELEMENTS_INSER_NEW() {
        return <<<EOF
<div class="col-md-6">     
    
   <div class="form-group">
        <label class="control-label col-md-4">Nome<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input class="form-control" id="firstname" name="firstname" type="text" required />
        </div>
    </div>
        
    <div class="form-group">
        <label class="control-label col-md-4">Sobrenome</label>
        <div class="col-md-6">
            <input class="form-control" id="lastname" name="lname" type="text" />
        </div>
    </div>
        
    <div class="form-group">
        <label class="control-label col-md-4">Usuário<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input class="form-control " id="username" name="username" type="text" required />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-4">Email<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input class="form-control" id="email" name="email" type="email" required />
        </div>
    </div>
        
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Cargo<span id="field-required">*</span></label>
        <div class="col-md-6">
               <select id="e1" name="business" class="populate" style="width: 210px">{$this->loop_account_type()}</select>
        </div>
    </div>  
               
    <div class="form-group">
        <label class="control-label col-md-4">Senha<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input class="form-control " id="password" name="password" type="password" />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-4">Confirmar Senha<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input class="form-control " id="confirm_password" name="confirm_password" type="password" />
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

    private function RIGHT_ELEMENTS_INSER_NEW() {
        $url = URL;
        return <<<EOF
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-3">Avatar</label>
        <div class="col-md-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-new thumbnail" style="width: auto; height: auto;">
                    <img src="{$url}public/avatars/default.jpg" style="" alt="" />
                </div>
                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height:80px; line-height: 20px;"></div>
                <div>
                    <span class="btn btn-white btn-file">
                        <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Enviar Imagem</span>
                        <span class="fileupload-exists"><i class="fa fa-undo"></i> Mudar</span>
                        <input type="file" name="img" id="img" class="default"/>
                    </span>
                    <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remover</a>
                </div>
            </div>
        </div>
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
     * isso é pra enviar um bug com a mascara jquery
     * então verifica se existe . que no caso é float (fração) caso não ache acressenta 00
     * @access private
     * @param $num
     * @return integer
     */
    private function tratar_numero($num) {
        return (strpos($num, '.') == false) ? $num . '00' : $num;
    }

    /**
     * Left Elements Update mode
     * @param array $data
     * @return String
     */
    private function LEFT_ELEMENTS_Update($data) {
        return <<<EOF
<div class="col-md-6">     
   <div class="form-group">
        <label class="control-label col-md-4">Nome<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input value="{$data['user_first_name']}" class="form-control" id="firstname" name="firstname" type="text" required />
        </div>
    </div>
        
    <div class="form-group">
        <label class="control-label col-md-4">Sobrenome</label>
        <div class="col-md-6">
            <input value="{$data['user_last_name']}" class="form-control" id="lastname" name="lname" type="text" />
        </div>
    </div>
        
    <div class="form-group">
        <label class="control-label col-md-4">Usuário<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input value="{$data['user_name']}" class="form-control " id="username" name="username" type="text" required />
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-4">Email<span id="field-required">*</span></label>
        <div class="col-md-6">
            <input value="{$data['user_email']}" class="form-control" id="email" name="email" type="email" required />
        </div>
    </div>
        
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Cargo<span id="field-required">*</span></label>
        <div class="col-md-6">
               <select id="e1" name="business" class="populate" style="width: 210px">{$this->loop_account_type($data['user_account_type'])}</select>
        </div>
    </div>  
               
    <div class="form-group">
        <label class="control-label col-md-4">Mudar Senha</label>
        <div class="col-md-6">
            <input id="c_pass" name="c_pass" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
        </div>
        <button data-original-title="Mudar Senha" data-content="Marque caso queira mudar a sua senha" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
    </div>
    <id id="change_password">           
        <div class="form-group">
            <label class="control-label col-md-4">Senha<span id="field-required">*</span></label>
            <div class="col-md-6">
                <input class="form-control " id="password" name="password" type="password" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-4">Confirmar Senha<span id="field-required">*</span></label>
            <div class="col-md-6">
                <input class="form-control " id="confirm_password" name="confirm_password" type="password" />
            </div>
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
        $resulution = null;
        $url = URL;
        $img = ($data['user_has_avatar'] == true) ? \GetInfo::_foto($data['user_id']) : URL . 'public/avatars/default.jpg';
        return <<<EOF
<div class="col-md-4">
    <div class="form-group">
        <label class="control-label col-md-3">Avatar</label>
        <div class="col-md-9">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-new thumbnail" style="width: auto; height: auto;">
                    <img src="{$img}" style="" alt="" />
                </div>
                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height:80px; line-height: 20px;"></div>
                <div>
                    <span class="btn btn-white btn-file">
                        <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Enviar Imagem</span>
                        <span class="fileupload-exists"><i class="fa fa-undo"></i> Mudar</span>
                        <input type="file" name="img" id="img" class="default"/>
                    </span>
                    <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remover</a>
                </div>
            </div>
        </div>
    </div>
</div>
EOF;
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
        $URL = URL . FILENAME . DS . Url::getURL($this->URL_ACTION) . DS . $dados ['user_id'] . DS;
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
class requireds extends UsersConfig {

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
EOF;
    }

    /**
     * Load all JS for page Insert mode
     * @return string
     */
    private function JS_REQUIRED_UPDATE($data) {
        $h = isset($data['cnpj']) ? 'cnpj' : 'cpf';
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
<script>
    $("#change_password").hide();
    $("#c_pass").change(function () {
        if ($(this).attr("checked")) {

            $("#change_password").fadeIn();
            return;
        } else {
            $("#change_password").fadeOut();
        }
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
