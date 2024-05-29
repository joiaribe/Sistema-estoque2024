<?php

namespace notifier\Notifier;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class NotifierHTML extends requireds {

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
                        <th>Descrição</th>
                        <th>Data</th>
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
     * verifica se existe um registro
     * @access private
     * @param Integer $data Resultado da coluna lida
     * @param Integer $id Id do registro
     * @return string
     */
    private function verify_estado($data, $id) {
        return ($data == 1) ? '<a href="' . URL . FILENAME . '/mark/' . $id . '/unread" title="Marcar como não lida"><span class="label label-success label-mini">Lida</span></a>' : '<a href="' . URL . FILENAME . '/mark/' . $id . '/read" title="Marcar como lida"><span class="label label-danger label-mini">Não Lida</span></a>';
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

        return '        <tr class="gradeX">
                                <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="' . $fetch['id'] . '" /></td>
                                <td>' . $this->verify_estado($fetch['lida'], $fetch['id']) . '</td>
                                <td>' . Func::str_reduce($fetch['title'], 40) . '</td>
                                <td>' . Func::str_truncate($fetch['text'], 50) . '</td>
                                <td data-order="' . $date_str . '"><a title="' . $date_formated . '">' . $data . '</a></td>
                                <td class="right actions">
                                ' . $this->build_buttons($fetch['id']) . '                      
                                </td>
                            </tr>';
    }

    /**
     * build tools for listing mode
     * @return string
     */
    protected function make_tools() {
        $url = URL . FILENAME . '/mark_all';
        $return = <<<EOF
            <form action="{$url}" id="mark_broadcast" method="POST">  
                        <div class="clearfix">                       
                            <div class="btn-group pull-right">
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">Marcar Como <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a title="Marcar {$this->msg['plural']} como lida" href="javascript:mark_all(' Deseja realmente marca todas como lidas ?','{$url}/1' )">Lida</a></li>
                                    <li><a title="Marcar {$this->msg['plural']} como lida" href="javascript:mark_all(' Deseja realmente marca todas como não lidas ? ','{$url}/0')">Não lida</a></li>
                                </ul>
                            </div>
                        </div>   
              <br>
EOF;

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
     * Define sex
     * @param ENUM $sex
     * @return string
     */
    private function sexo($sex) {
        switch ($sex) {
            case 'M':
                $result = 'Masculino';
                break;
            case 'F':
                $result = 'Feminino';
                break;
            case 'T':
                $result = 'Transexual';
                break;
            default:
                $result = 'Não Encontrado !';
                break;
        }
        return $result;
    }

   

    /**
     * Calculate age
     * @param date $nascimento
     * @param string $formato
     * @param string $separador
     * @return string
     */
    private function idade($nascimento, $formato, $separador) {
        //Data Nascimento
        $nascimento = explode($separador, $nascimento);

        if ($formato == "dma") {
            $ano = $nascimento[2];
            $mes = $nascimento[1];
            $dia = $nascimento[0];
        } elseif ($formato == "amd") {
            $ano = $nascimento[0];
            $mes = $nascimento[1];
            $dia = $nascimento[2];
        }

        $dia1 = $dia;
        $mes1 = $mes;
        $ano1 = $ano;

        $dia2 = date("d");
        $mes2 = date("m");
        $ano2 = date("Y");

        $dif_ano = $ano2 - $ano1;
        $dif_mes = $mes2 - $mes1;
        $dif_dia = $dia2 - $dia1;

        if (($dif_mes == 0) and ( $dia2 < $dia1)) {
            $dif_dia = (ultimoDiaMes($data1) - $dia1) + $dia2;
            $dif_mes = 11;
            $dif_ano--;
        } elseif ($dif_mes < 0) {
            $dif_mes = (12 - $mes1) + $mes2;
            $dif_ano--;
            if ($dif_dia < 0) {
                $dif_dia = (ultimoDiaMes($data1) - $dia1) + $dia2;
                $dif_mes--;
            }
        } elseif ($dif_dia < 0) {
            $dif_dia = (ultimoDiaMes($data1) - $dia1) + $dia2;
            if ($dif_mes > 0) {
                $dif_mes--;
            }
        }
        if ($dif_ano > 0) {
            $dif_ano = $dif_ano . " ano" . (($dif_ano > 1) ? "s " : " ");
        } else {
            $dif_ano = "";
        }
        if ($dif_mes > 0) {
            $dif_mes = $dif_mes . " mes" . (($dif_mes > 1) ? "es " : " ");
        } else {
            $dif_mes = "";
        }
        if ($dif_dia > 0) {
            $dif_dia = $dif_dia . " dia" . (($dif_dia > 1) ? "s " : " ");
        } else {
            $dif_dia = "";
        }

        return $dif_ano;
    }

    private function get_remetete($id) {
        if (isset($id)) {
            return GetInfo::_user_cargo($id);
        }
        return '<b><font color="red">Sistema</font></b>';
    }

    /**
     * Tables preview mode
     * @param array $param Query result
     * @return String
     */
    private function _make_list_mode_tables(array $param) {
        $assoc = $this->get_remetete($param['id_from']);
        return '            
<tr>
    <th>#ID:</th>
    <td>#' . $param['id'] . '</td>
</tr>
<tr>
    <th>Remetente:</th>
    <td>' . $assoc . '</td>
</tr>
<tr>
    <th>Título:</th>
    <td>' . $param['title'] . '</td>
</tr>
<tr>
    <th>Mensagem:</th>
    <td>' . $this->check_field($param['text']) . '</td>
</tr>
<tr>
    <th>Data : </th>
    <td>' . utf8_encode(strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($param['data']))) . '</td>
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
        $date = date('d/m/Y');
        return <<<EOF
<div class="col-md-5">

    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">CEP</label>
        <div class="col-md-6">
            <input name="CEP" type="text" id="cep" class="form-control">
        </div>
    </div> 
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Logradouro</label>
        <div class="col-md-6">
            <input name="rua" type="text" id="rua" class="form-control">
        </div>
    </div>    
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Bairro</label>
        <div class="col-md-6">
            <input name="bairro" type="text" id="bairro"  class="form-control">
        </div>
    </div> 
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Cidade</label>
        <div class="col-md-6">
            <input name="cidade" type="text" id="cidade" class="form-control">
        </div>
    </div>    
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Estado</label>
        <div class="col-md-6">
            <input name="uf" type="text" id="uf" class="form-control">
        </div>
    </div> 
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Numero</label>
        <div class="col-md-6">
            <input type="text" id="numero" name="numero" class="form-control">
        </div>
    </div>    
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Aniversário</label>
        <div class="col-md-6">
                <input type="text" name="nascimento" value="{$date}" size="16" class="form-control date">
        </div>
    </div> 
                             
                   
</div>
EOF;
    }

    /**
     * User assoc
     * @param integer $user_id
     * @return string
     */
    private function LoopUsersAssoc($user_id = NULL) {
        $q = new Query;
        $q
                ->select_from('*', 'users')
                ->where_not_equal_to(
                        array(
                            'user_account_type' => 0
                        )
                )
                ->run();
        $result = '';
        foreach ($q->get_selected() as $value) {
            $where = array(
                'user_id' => $value['user_id']
            );
            $exists = Func::_contarReg($this->table, $where);
            if (!($exists > 0) || $value['user_id'] == $user_id) {
                if ($user_id !== NULL) {
                    $result.= ' <option ' . $this->_check_selected($value['user_id'], $user_id) . ' value="' . $value['user_id'] . '">' . \GetInfo::_name($value['user_id']) . '</option>';
                } else {
                    $result.= ' <option value="' . $value['user_id'] . '">' . \GetInfo::_name($value['user_id']) . '</option>';
                }
            }
        }
        return $result;
    }

    /**
     * Left Elements Insert mode
     * @return String
     */
    private function LEFT_ELEMENTS_INSER_NEW() {
        return <<<EOF
        <div class="col-md-6"> 
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Associado<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e1" name="assoc" class="populate" style="width: 210px">
                           <option value="nothing">-- Nenhum --</option>
                            {$this->LoopUsersAssoc()} 
                        </select>
                </div>
                <button data-original-title="Associar Usuário" data-content="Se um funcionário tiver acesso ao sistema você precisa associar ao usuário já criado. Usuários já associados e administradores não serão mostrados na lista." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Nome<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Telefone</label>
                <div class="col-md-6">
                    <input class="form-control phone_with_ddd" name="tel" type="text" />
                </div>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Celular</label>
                <div class="col-md-6">
                    <input class="form-control phone_with_ddd" name="cel" type="text" />
                </div>
            </div>
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Sexo<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e2" name="sexo" class="populate" style="width: 210px">
                            <option value="F">Feminino</option>
                            <option value="M">Masculino</option>
                            <option value="T">Transexual</option>
                        </select>
                </div>
            </div>

            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">CPF</label>
                <div class="col-md-6">
                      <input type="text" name="CPF" class="form-control cpf">
                </div>
            </div>  
        
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">RG</label>
                <div class="col-md-6">
                    <input type="text" name="RG" class="form-control rg">
                </div>
            </div>  
                
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Salário</label>
                <div class="col-md-6">
                    <input class="form-control money" name="valor" minlength="2" type="text" />
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
    private function _check_selected($default, $value
    ) {
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
     * isso é pra enviar um bug com a mascara jquery
     * então verifica se existe . que no caso é float (fração) caso não ache acressenta 00
     * @access private
     * @param $num
     * @return integer
     */
    private function tratar_numero($num) {
        if ($num == NULL) {
            return NULL;
        }
        return(strpos($num, '.') == false) ? $num . '00' : $num;
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
                 <label for="firstname" class="control-label col-lg-4">Associado<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e1" name="assoc" class="populate" style="width: 210px">
                           <option value="nothing">-- Nenhum --</option>
                            {$this->LoopUsersAssoc($data['user_id'])} 
                        </select>
                </div>
                <button data-original-title="Associar Usuário" data-content="Se um funcionário tiver acesso ao sistema você precisa associar ao usuário já criado. Usuários já associados e administradores não serão mostrados na lista." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Nome<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input value="{$data['nome']}" class="form-control" id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Telefone</label>
                <div class="col-md-6">
                    <input value="{$data['Tel']}" class="form-control phone_with_ddd" name="tel" type="text" />
                </div>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Celular</label>
                <div class="col-md-6">
                    <input value="{$data['Celular']}" class="form-control phone_with_ddd" name="cel" type="text" />
                </div>
            </div>
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Sexo<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e2" name="sexo" class="populate" style="width: 210px">
                            <option value="F"{$this->_check_selected("F", $data['Sexo'])}>Feminino</option>
                            <option value="M"{$this->_check_selected("M", $data['Sexo'])}>Masculino</option>
                            <option value="T"{$this->_check_selected("T", $data['Sexo'])}>Transexual</option>
                        </select>
                </div>
            </div>

            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">CPF</label>
                <div class="col-md-6">
                      <input value="{$data['CPF']}" type="text" name="CPF" class="form-control cpf">
                </div>
            </div>  
        
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">RG</label>
                <div class="col-md-6">
                    <input value="{$data['RG']}" type="text" name="RG" class="form-control rg">
                </div>
            </div>  
                
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Salário</label>
                <div class="col-md-6">
                    <input value="{$this->tratar_numero($data['Salario'])}" class="form-control money" name="valor" minlength="2" type="text" />
                </div>
            </div>        
</div>  
EOF;
    }

    /**
     * Right Elements Update mode
     * @param arr ay $data
     * @return String
     */
    private function RIGHT_ELEMENTS_Update($data) {
        return <<<EOF
<div class="col-md-5">

    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">CEP</label>
        <div class="col-md-6">
            <input value="{$data['Cep']}" name="CEP" type="text" id="cep" class="form-control">
        </div>
    </div> 
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Logradouro</label>
        <div class="col-md-6">
            <input value="{$data['End']}" name="rua" type="text" id="rua" class="form-control">
        </div>
    </div>    
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Bairro</label>
        <div class="col-md-6">
            <input value="{$data['Bairro']}" name="bairro" type="text" id="bairro"  class="form-control">
        </div>
    </div> 
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Cidade</label>
        <div class="col-md-6">
            <input value="{$data['Cidade']}" name="cidade" type="text" id="cidade" class="form-control">
        </div>
    </div>    
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Estado</label>
        <div class="col-md-6">
            <input value="{$data['UF']}" name="uf" type="text" id="uf" class="form-control">
        </div>
    </div> 
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Numero</label>
        <div class="col-md-6">
            <input value="{$data['Num']}" type="text" id="numero" name="numero" class="form-control">
        </div>
    </div>    
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">Aniversário</label>
        <div class="col-md-6">
                <input type="text" name="nascimento" value="{$data['DtNasc']}" size="16" class="form-control date">
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
        $URL = URL . FILENAME . DS . Url::getURL($this->URL_ACTION) . DS . $dados ['id'] . DS;
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
class requireds extends NotifierConfig {

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
<script type="text/javascript">
    $(document).ready(function() {
        $("#cep").blur(function() {

            consulta = $("#cep").val()

            var url = "http://cep.correiocontrol.com.br/" + consulta + ".json";

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function(json) {
                    $("#rua").val(json.logradouro)
                    $("#bairro").val(json.bairro)
                    $("#cidade").val(json.localidade)
                    $("#uf").val(json.uf)
                    $("#numero").focus();
                },
            });//ajax

        });//função blur
    });
</script>
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
<!--
<script type="text/javascript" src="js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
-->
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
<script type="text/javascript">
    $(document).ready(function() {
        $("#cep").blur(function() {

            consulta = $("#cep").val()

            var url = "http://cep.correiocontrol.com.br/" + consulta + ".json";

            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function(json) {
                    $("#rua").val(json.logradouro)
                    $("#bairro").val(json.bairro)
                    $("#cidade").val(json.localidade)
                    $("#uf").val(json.uf)
                    $("#numero").focus();
                },
            });//ajax

        });//função blur
    });
</script>
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
<!--
<script type="text/javascript" src="js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
-->
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
    protected
            function _LOAD_REQUIRED_LISTING() {
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
