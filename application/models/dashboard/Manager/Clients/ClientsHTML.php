<?php

namespace Manager\Clients;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class ClientsHTML extends requireds {

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
                        <th>Nome</th>
                        <th>Bairro</th>
                        <th>Telefone</th>
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
            <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="{$fetch['id']}" /></td>
            <td>{$fetch['nome']}</td>
            <td>{$this->check_field($fetch['Bairro'])}</td>
            <td>{$this->check_field($fetch['Fone'])}</td>
            <td data-order="{$date_str}"><a title="$date_formated">{$data}</a></td>
            <td class="right actions">{$this->build_buttons($fetch['id'])}</td>
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
     * Tables preview mode
     * @param array $param Query result
     * @return String
     */
    private function _make_list_mode_tables(array $param) {
        $name = GetInfo::_name($param['id_user']);
        return '            
            <tr>
                <th>#ID:</th>
                <td>#' . $param['id'] . '</td>
            </tr>
           <tr>
                <th>Cadastrado(a):</th>
                <td>' . $name . '</td>
            </tr>
            <tr>
                <th>Nome:</th>
                <td>' . $param['nome'] . '</td>
            </tr>
            <tr>
                <th>Aniversário:</th>
                <td>' . strftime('%d de %B, %Y', strtotime($param['Aniversario'])) . '</td>
            </tr>
            <tr>
                <th>Indicação:</th>
                <td>' . $this->verify_indicacao($param['Indicacao']) . '</td>
            </tr>
            <tr>
                <th>Sexo:</th>
                <td>' . $this->sexo($param['Sexo']) . '</td>
            </tr>
            <tr>
                <th>Telefone:</th>
                <td>' . $this->check_field($param['Fone']) . '</td>
            </tr>

           <tr>
                <th>Estado:</th>
                <td>' . $this->check_field($param['UF']) . '</td>
            </tr>
            <tr>
                <th>Cidade:</th>
                <td>' . $this->check_field($param['Cidade']) . '</td>
            </tr>
             <tr>
                <th>Bairro:</th>
                <td>' . $this->check_field($param['Bairro']) . '</td>
            </tr>
             <tr>
                <th>Endereço:</th>
                <td>' . $this->check_field($param['End']) . '</td>
            </tr>
            <tr>
                <th>Numero:</th>
                <td>' . $this->check_field($param['Num']) . '</td>
            </tr>
            <tr>
                <th>RG:</th>
                <td>' . $this->check_field($param['Rg']) . '</td>
            </tr>
            <tr>
                <th>CPF:</th>
                <td>' . $this->check_field($param['Cpf']) . '</td>
            </tr>  
            <tr>
                <th>Descrição:</th>
                <td>' . $this->check_field($param['Obs']) . '</td>
            </tr> 
       
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
    private function loop_indicacao($data = false) {
        $q = new Query;
        $q
                ->select()
                ->from('clientes')
                ->order_by('nome asc')
                ->run();
        $result = '<option value="">-- Nenhum --</option>';
        if ($q) {
            $users = $q->get_selected();
            foreach ($users as $user) {
                if ($data !== false) {
                    $result.= '<option value="' . $user['id'] . '"' . $this->_check_selected($user['id'], $data) . '>' . $user['nome'] . '</option>';
                } else {
                    $result.= '<option value="' . $user['id'] . '">' . $user['nome'] . '</option>';
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
        $data = date('d-m-Y');
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
             <div data-date-viewmode="years" data-date-format="dd-mm-yyyy" data-date="12-02-2012"  class="input-append date dpYears">
                <input type="text" readonly="" name="nascimento" value="{$data}" size="16" class="form-control">
                      <span class="input-group-btn add-on">
                        <button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>
                      </span>
            </div>
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
                <label for="nome" class="control-label col-md-4">Nome<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div>
       
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Indicação</label>
                <div class="col-md-6">
                         <select id="e3" name="indicacao" style="width:100%" class="populate">
                            {$this->loop_indicacao()}
                        </select>  
                </div>
            </div>    
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Telefone</label>
                <div class="col-md-6">
                         <input type="text" name="tel" data-mask="(99) 9999-9999?9" class="form-control">
                </div>
            </div>  
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">E-mail</label>
                <div class="col-md-6">
                        <input class="form-control " id="cemail" type="email" name="email" required />
                </div>
            </div> 
            <div class="form-group">
                <label for="firstname" class="control-label col-lg-4">Agenda Fundo</label>
               <div class="col-md-6">
                 <div data-color-format="rgb" data-color="rgb(255, 146, 180)" class="input-append colorpicker-default color">
                   <input type="text" name="agenda" readonly="" class="form-control">
                         <span class=" input-group-btn add-on">
                             <button class="btn btn-white" type="button" style="padding: 8px">
                                 <i style="background-color: rgb(124, 66, 84);"></i>
                             </button>
                         </span>
               </div>
               </div>
           </div> 
            <div class="form-group">
                <label for="firstname" class="control-label col-lg-4">Agenda Texto</label>
               <div class="col-md-6">
                 <div data-color-format="rgb" data-color="#000000" class="input-append colorpicker-default color">
                   <input type="text" name="agenda_font" value="#000000" class="form-control">
                         <span class=" input-group-btn add-on">
                             <button class="btn btn-white" type="button" style="padding: 8px">
                                 <i style="background-color: #000000;"></i>
                             </button>
                         </span>
               </div>
               </div>
           </div> 
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Sexo<span id="field-required">*</span></label>
                <div class="col-md-6">
                         <select id="e1" name="sexo" class="populate " style="width:100%">
                            <option value="F">Feminino</option>
                            <option value="M">Masculino</option>
                        </select>
                </div>
            </div>
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">CPF</label>
                <div class="col-md-6">
                      <input type="text" name="CPF" data-mask="999.999.999-99" class="form-control">
                </div>
            </div>  
        
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">RG</label>
                <div class="col-md-6">
                    <input type="text" name="RG" data-mask="99.999.999-9" class="form-control">
                </div>
            </div>  

</div>
EOF;
    }

    /**
     * Center Elements Insert mode
     * @return String
     */
    private function Center_ELEMENTS_INSERT_NEW() {
        return <<<EOFPAGE
<div class="col-md-12">
    <div class="form-group">
        <label for="firstname" class="control-label col-lg-2">Descrição</label>
        <div class="col-md-12">
             <textarea class="wysihtml5 form-control" name="text" rows="9" required></textarea>
        </div>
    </div>
</div>
EOFPAGE;
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
                                        {$this->Center_ELEMENTS_INSERT_NEW()}
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
                <label for="nome" class="control-label col-md-4">Título<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" id="cname" name="name" value="{$data['nome']}" minlength="2" type="text" required />
                </div>
            </div>
                    
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Indicação</label>
                <div class="col-md-6">
                         <select id="e3" name="indicacao" style="width:100%" class="populate">
                            {$this->loop_indicacao($data['Indicacao'])}
                        </select>  
                </div>
            </div>    
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Telefone</label>
                <div class="col-md-6">
                         <input value="{$data['Fone']}" type="text" name="tel" data-mask="(99) 999-9999" class="form-control">
                </div>
            </div>  
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">E-mail</label>
                <div class="col-md-6">
                        <input value="{$data['Email']}" class="form-control " id="cemail" type="email" name="email" required />
                </div>
            </div> 
            <div class="form-group">
                <label for="firstname" class="control-label col-lg-4">Agenda</label>
               <div class="col-md-6">
                 <div data-color-format="rgb" data-color="{$data['agenda']}" class="input-append colorpicker-default color">
                   <input value="{$data['agenda']}" type="text" name="agenda" readonly="" class="form-control">
                         <span class=" input-group-btn add-on">
                             <button class="btn btn-white" type="button" style="padding: 8px">
                                 <i style="background-color: {$data['agenda']};"></i>
                             </button>
                         </span>
               </div>
               </div>
           </div> 
            <div class="form-group">
                <label for="firstname" class="control-label col-lg-4">Agenda Texto</label>
               <div class="col-md-6">
                 <div data-color-format="rgb" data-color="{$data['agenda_cor']}" class="input-append colorpicker-default color">
                   <input type="text" name="agenda_font" value="{$data['agenda_cor']}" class="form-control">
                         <span class=" input-group-btn add-on">
                             <button class="btn btn-white" type="button" style="padding: 8px">
                                 <i style="background-color: {$data['agenda_cor']};"></i>
                             </button>
                         </span>
               </div>
               </div>
           </div> 
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Sexo<span id="field-required">*</span></label>
                <div class="col-md-6">
                         <select id="e1" name="sexo" class="populate " style="width:100%">
                            <option value="F"{$this->_check_selected("F", $data['Sexo'])}>Feminino</option>
                            <option value="M"{$this->_check_selected("M", $data['Sexo'])}>Masculino</option>
                        </select>
                </div>
            </div>
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">CPF</label>
                <div class="col-md-6">
                      <input value="{$data['Cpf']}" type="text" name="CPF" data-mask="999.999.999-99" class="form-control">
                </div>
            </div>  
        
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">RG</label>
                <div class="col-md-6">
                    <input value="{$data['Rg']}" type="text" name="RG" data-mask="99.999.999-9" class="form-control">
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
        return <<<EOF
<div class="col-md-5">
    <div class="form-group">
         <label for="firstname" class="control-label col-lg-4">CEP</label>
        <div class="col-md-6">
            <input value="{$data['Cep']}" name="CEP" type="text" id="cep"  class="form-control">
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
             <div data-date-viewmode="years" data-date-format="dd-mm-yyyy" data-date="12-02-2012"  class="input-append date dpYears">
                <input type="text" readonly="" name="nascimento" value="{$data['Aniversario']}" size="16" class="form-control">
                      <span class="input-group-btn add-on">
                        <button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>
                      </span>
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
                                        {$this->CENTER_ELEMENTS_Update($dados)}
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
class requireds extends ClientsConfig {

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
