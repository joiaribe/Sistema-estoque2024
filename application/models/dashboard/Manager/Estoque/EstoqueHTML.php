<?php

namespace Manager\Estoque;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class EstoqueHTML extends requireds {

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
                        <th>Título</th>
                        <th>Marcador</th>
                        <th>Fornecedor</th>
                        <th>Estoque</th>
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
     * get all fornecedores
     * @access protected
     * @return void
     */
    protected function loop_fornecedor($action = 'insert', $default = NULL) {
        $q = new Query;
        $q
                ->select()
                ->from('fornecedores')
                ->run();
        $result = '';
        if ($q) {
            $data = $q->get_selected();
            foreach ($data as $dados) {
                if ($action == 'insert') {
                    $result.= '<option value="' . $dados['id'] . '">' . $dados['empresa'] . '</option>';
                } else {
                    $result.= '<option ' . $this->_check_selected($default, $dados['id']) . ' value="' . $dados['id'] . '">' . $dados['empresa'] . '</option>';
                }
            }
            $this->data_for = $result;
        }
    }

    /**
     * get all marcador
     * @access protected
     * @return void
     */
    protected function loop_marcadores($action = 'insert', $default = NULL) {
        $q = new Query;
        $q
                ->select()
                ->from('marcador')
                ->group_by('id')
                ->run();
        $result = '';
        if ($q) {
            $data = $q->get_selected();
            foreach ($data as $dados) {
                if ($action == 'insert') {
                    $result.= '<option value="' . $dados['id'] . '">' . $dados['nome'] . '</option>';
                } else {
                    $result.= '<option' . $this->_check_selected($default, $dados['id']) . ' value="' . $dados['id'] . '">' . $dados['nome'] . '</option>';
                }
            }
            $this->data_mar = $result;
        }
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
     * html conteudo da tabela
     * @access protected
     * @return string
     */
    protected function contain_table(array $fetch) {
        $nome = '<a title="' . $fetch['nome'] . '">' . \Func::str_truncate($fetch['nome'],20) . '</a>';
        $mar = \Func::array_table('marcador', array('id' => $fetch['marcador']), 'nome');
        $for = \Func::array_table('fornecedores', array('id' => $fetch['id_fornecedor']), 'empresa');
        $valor = number_format($fetch['valor'], 2, ",", ".");
        $data = \makeNiceTime::MakeNew($fetch['data']);
        $date_str = strtotime($fetch['data']);
        $date_formated = $this->dias_da_semana[date('w', $date_str)] . ", " . strftime("%d/%m/%Y ás %H:%M", $date_str);
        return <<<EOFPAGE
<tr class="gradeX">
    <td class="center uniformjs"><input type="checkbox" name="checkbox[]" value="{$fetch['id']}" /></td>
    <td data-search="{$fetch['nome']}">{$nome}</td>
    <td>{$this->check_field($mar)}</td>
    <td>{$this->check_field($for)}</td>
    <td>{$fetch['quantidade']}</td>
    <td data-order="{$fetch['valor']}">R$: {$valor}</td>
    <td data-order="{$date_str}"><a title="$date_formated">{$data}</a></td>
    <td class="right actions">{$this->build_buttons($fetch['id'])}</td>
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
                    . '<li><a title="Filtrar resultados ' . $this->msg['singular'] . '" href="' . URL . FILENAME . $this->loc_action['add'] . '">Filtrar ' . $this->msg['singular'] . '</a></li>'
                    . '<li class="divider"></li>';
        }
        $return.= Func::ToolsCall($this->ids_tools) . '
                                    <li><a title="Gerenciar marcadores" href="' . URL . 'dashboard/Manager/marker">Gerenciar Marcadores</a></li>    
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
        $qnt = ($param['quantidade'] == 1) ? $param['quantidade'] . ' Unidade' : $param['quantidade'] . ' Unidades';
        $mar = \Func::array_table('marcador', array('id' => $param['marcador']), 'nome');
        $for = \Func::array_table('fornecedores', array('id' => $param['id_fornecedor']), 'empresa');
        $valor = $param['valor_original'];
        $valorB = $param['valor'];
        $lucro = number_format($valorB - $valor);
        $img = ($param['foto'] == NULL) ? 'http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=Sem+imagem' : URL . 'public/images/produtos/' . $param['foto'];
        return '            
            <tr>
                <th>#ID:</th>
                <td>#' . $param['id'] . '</td>
            </tr>
            <tr>
                <th>Nome:</th>
                <td>' . $param['nome'] . '</td>
            </tr>
            <tr>
                <th>Fornecedor:</th>
                <td><a href="" title="Clique arqui para ver detalhes do fornecedor.">' . $this->check_field($for) . '</a></td>
            </tr>
            <tr>
                <th>Marcador:</th>
                <td><a href="" title="Clique arqui para ver detalhes do marcador.">' . $this->check_field($mar) . '</a></td>
            </tr>

           <tr>
                <th>Quantidade:</th>
                <td>' . $qnt . '</td>
            </tr>
            <tr>
                <th>Lucro por venda:</th>
                <td><a href="" title="Valor comprado : R$ ' . $valorB . ' | Valor vendido : R$: ' . $valor . '">R$ : ' . $lucro . '</a></td>
            </tr>
             
            <tr>
                <th>Foto:</th>
                <td> <img width="230" height="200" src="' . $img . '" alt="" /></td>
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
                    $result.= '<option value="' . $user['id'] . '"' . $this->_check_selected($user['id'], $data) . '>' . $user ['nome'] . '</option>';
                } else {
                    $result.= '<option value="' . $user['id'] . '">' . $user ['nome'] . '</option>';
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
        $resulution = null;
        $url = URL;
        foreach ($this->resolution_min as $value) {

            if (!isset($resulution)) {
                $resulution.= $value;
            } else {
                $resulution.= 'x' . $value;
            }
        }
        return <<<EOF
<div class="col-md-5">
        
            <div class="form-group">
                <label for="nome" class="control-label col-md-3">Descrição</label>
                <div class="col-md-9">
                      <textarea class="form-control" id="ccomment" name="comment"></textarea>
                </div>
            </div>   
        
            <div class="form-group">
                <label class="control-label col-md-3">Foto</label>
                <div class="col-md-9">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                            <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=Sem+imagem" alt="" />
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                        <div>
                            <span class="btn btn-white btn-file">
                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Enviar Imagem</span>
                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Mudar</span>
                                <input type="file" name="img" id="img" class="default"/>
                            </span>
                            <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remover</a>
                        </div>
                    </div>
                    <span class="label label-danger">Atenção!</span>
                    <span> Resolução Recomendada {$resulution} pixels.</span>
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
        <div class="col-md-6">              
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Nome<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div>
          
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Fornecedor<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e1" name="for" class="populate" style="width: 210px">{$this->data_for}</select>
                </div>
            </div>  
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Marcador<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e2" name="mar" class="populate" style="width: 210px">{$this->data_mar}</select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-4">Quantidade<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <div id="spinner1">
                        <div class="input-group input-small">
                            <input type="text" name="qnt" class="spinner-input form-control" maxlength="3" readonly>
                            <div class="spinner-buttons input-group-btn btn-group-vertical">
                                <button type="button" class="btn spinner-up btn-xs btn-default">
                                    <i class="fa fa-angle-up"></i>
                                </button>
                                <button type="button" class="btn spinner-down btn-xs btn-default">
                                    <i class="fa fa-angle-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                    <button data-original-title="Estoque" data-content="Quantidade de produtos disponíveis." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>   
            </div>
                                    
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Valor Entrada<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control money" name="valor" minlength="2" type="text" required />
                </div>
                   <button data-original-title="Valor Comprado" data-content="Valor por unidade que foi comprada pelo fornecedor." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Valor Saída<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control money" name="money" minlength="2" type="text" required />
                       <span class="help-block"></span>
                </div>
                       <button data-original-title="Valor Vendido" data-content="Valor por unidade que será vendida." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Auto Despesa<span id="field-required">*</span></label>
                <div class="col-md-6">
                     <input name="auto" id="auto_pay" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
                </div>
                    <button data-original-title="Auto Despesa" data-content="Se Habilitado insere a despesa total calculando a quantidade e o valor comprado, caso esteja desabilitado o valor sempre será descontado toda vez que fizer uma nova movimentação de vendas de produtos." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
            </div>
                       
                       
           <div id="change_auto">
               <div class="form-group">
                                <label for="firstname" class="control-label col-lg-4" title="Método de Pagamento">Pagamento<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <select id="e0" name="metthod" class="populate" style="width: 210px">
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
                                        <select id="e9" name="card_name" class="populate" style="width: 210px">
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
                 <label for="firstname" class="control-label col-lg-4">Estado<span id="field-required">*</span></label>
                 <div class="col-md-6">
                      <input name="status" checked type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
                 </div>
                     <button data-original-title="Estado de Pagamento" data-content="Se Habilitado marca a despesa como paga." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
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
        $this->loop_marcadores('up', $data['marcador']);
        $this->loop_fornecedor('up', $data['id_fornecedor']);
        return <<<EOF
        <div class="col-md-5">
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Nome<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input class="form-control" value="{$data['nome']}"  id="cname" name="name" minlength="2" type="text" required />
                </div>
            </div>
          
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Fornecedor<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e1" name="for" class="populate" style="width: 210px">{$this->data_for}</select>
                </div>
            </div>  
            <div class="form-group">
                 <label for="firstname" class="control-label col-lg-4">Marcador<span id="field-required">*</span></label>
                <div class="col-md-6">
                       <select id="e2" name="mar" class="populate" style="width: 210px">{$this->data_mar}</select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-4">Quantidade<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <div id="spinner1">
                        <div class="input-group input-small">
                            <input value="{$data['quantidade']}"  type="text" name="qnt" class="spinner-input form-control" maxlength="3" readonly>
                            <div class="spinner-buttons input-group-btn btn-group-vertical">
                                <button type="button" class="btn spinner-up btn-xs btn-default">
                                    <i class="fa fa-angle-up"></i>
                                </button>
                                <button type="button" class="btn spinner-down btn-xs btn-default">
                                    <i class="fa fa-angle-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                   <button data-original-title="Estoque" data-content="Quantidade de produtos disponíveis." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>   
            </div>
                                    
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Valor Entrada<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input value="{$this->tratar_numero($data['valor_original'])}"  class="form-control money" name="valor" minlength="2" type="text" required />
                </div>
                    <button data-original-title="Valor Comprado" data-content="Valor por unidade que foi comprada pelo fornecedor." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
            </div>
            <div class="form-group">
                <label for="nome" class="control-label col-md-4">Valor Saída<span id="field-required">*</span></label>
                <div class="col-md-6">
                    <input value="{$this->tratar_numero($data['valor'])}"  class="form-control money" name="money" minlength="2" type="text" required />
                </div>
                    <button data-original-title="Valor Vendido" data-content="Valor por unidade que será vendida." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
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
        foreach ($this->resolution_min as $value) {

            if (!isset($resulution)) {
                $resulution.= $value;
            } else {
                $resulution.= 'x' . $value;
            }
        }
        $img = ($data['foto'] !== NULL) ? URL . 'public/images/produtos/' . $data['foto'] : 'http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=Sem+imagem';
        return <<<EOF
<div class="col-md-5">
            <div class="form-group">
                <label for="nome" class="control-label col-md-3">Descrição</label>
                <div class="col-md-9">
                      <textarea class="form-control" id="ccomment" name="comment">{$data['descri']}</textarea>
                </div>
            </div>  
            <div class="form-group">
                <label class="control-label col-md-3">Foto</label>
                <div class="col-md-9">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                            <img src="{$img}" alt="" />
                        </div>
                        <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                        <div>
                            <span class="btn btn-white btn-file">
                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Enviar Imagem</span>
                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Mudar</span>
                                <input type="file" name="img" id="img" class="default"/>
                            </span>
                            <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remover</a>
                        </div>
                    </div>
                    <span class="label label-danger">Atenção!</span>
                    <span> Resolução Recomendada {$resulution} pixels.</span>
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
class requireds extends EstoqueConfig {

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
<!-- Jquery Mask -->
<script type="text/javascript" src="js/mask/jquery.mask.js"></script>
<script type="text/javascript" src="js/mask.js"></script>
<script type='text/javascript'>
        
    $("#change_auto").hide();
    $("#auto_pay").change(function () {
        if ($(this).attr("checked")) {
            $("#change_auto").fadeIn();
            $("#only_check").hide();
            $("#only_card").hide();
            $("#only_ag").hide();

            $(document).ready(function () {
                $("select").change(function () {
                    $("select option:selected").each(function () {
                        if ($(this).attr("value") == "Cheque") {
                            $("#only_card").fadeOut();
                            $("#only_ag").fadeOut();
                            $("#only_check").fadeIn();
                            $('#only_card').find('input:text').val('');
                            $('#only_ag').find('input:text').val('');
                        }
                        if ($(this).attr("value") == "Cartão de Crédito") {
                            $("#only_ag").fadeOut();
                            $("#only_card").fadeIn();
                            $('#only_ag').find('input:text').val('');
                        }
                        if ($(this).attr("value") == "Débito Automático") {
                            $("#only_check").fadeOut();
                            $("#only_ag").fadeIn();
                            $("#only_card").fadeIn();
                            $('#only_check').find('input:text').val('');
                        }
                        if ($(this).attr("value") == "Dinheiro") {
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
            return;
        } else {
            $("#change_auto").fadeOut();
        }
    });
</script>
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
