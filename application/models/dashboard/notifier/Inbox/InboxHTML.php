<?php

namespace notifier\Inbox;

use Dashboard\Buttons as Buttons;
use Developer\Tools\GetInfo as GetInfo;
use Query as Query;
use Developer\Tools\Url as Url;
use DateTime as DateTime;
use Func as Func;

/**
 * HTML used in all class
 */
class InboxHTML extends requireds {

    /**
     * Pagination
     * @param mixed $loop
     * @param integer $totalperpage
     * @param integer $total
     * @return string
     */
    protected function pagination($loop, $totalperpage, $total) {
        if ($loop !== false) {
            $param = Url::getURL($this->URL_ACTION);
            // defines if the parameter does not exist
            if (isset($param)) {
                if ($param == 1) {
                    $n = '1-' . $totalperpage;
                } else {
                    $start = ($param - 1) * $totalperpage + 1;
                    $end = $start + $totalperpage - 1;
                    if ($end > $total) {
                        $start = $start - 1;
                        $end = $total;
                    }
                    $n = $start . '-' . $end;
                }
            } else {
                $n = '1-' . $totalperpage;
            }

            return <<<EOF
        <ul class="unstyled inbox-pagination">
            <li><span>{$n} de {$total}</span></li>
            {$loop}
        </ul>
EOF;
        }
    }

    /**
     * check get param to mark checkboxes
     * @param array $data
     * @return string
     */
    private function CheckChecked(array $data) {
        $mark = filter_input(INPUT_GET, 'mark');
        if (isset($mark)) {
            switch ($mark) {
                case 'all':
                    $result = ' checked';
                    break;
                case 'read':
                    if ($data['lida'] == true) {
                        $result = ' checked';
                    }
                    break;
                case 'unread':
                    if ($data['lida'] == false) {
                        $result = ' checked';
                    }
                    break;
                default:
                    break;
            }
        }

        return isset($result) ? $result : NULL;
    }

    /**
     * html conteudo da tabela
     * @access protected
     * @return string
     */
    protected function contain_table(array $fetch) {
        $attack = (Func::_contarReg('mensagem_attack', array('id_message' => $fetch['id'])) > 0) ? '<i class="fa fa-paperclip"></i>' : NULL;
        $star = $fetch['star'] == true ? '<i class="fa fa-star inbox-started"></i>' : '<i class="fa fa-star"></i>';
        $text = Func::str_reduce($fetch['text'], 40);
        $date = '<a href="' . URL . FILENAME . '#" title="' . strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($fetch['data'])) . '">' . \makeNiceTime::MakeNew($fetch['data']) . '</a>';
        $read = $fetch['lida'] == false ? ' class="unread"' : NULL;
        $title = Func::str_reduce($fetch['title'], 30);
        $url = URL . FILENAME . $this->loc_action['prev'] . $fetch['id'];

        return <<<EOFPAGE
<tr{$read}>
    <td class="center uniformjs"><input{$this->CheckChecked($fetch)} type="checkbox" class="checkItem" name="checkbox[]" value="{$fetch['id']}" /></td>
    <td class="inbox-small-cells">{$star}</td>
    <td class="view-message dont-show"><a href="{$url}">{$title}</a></td>
    <td class="view-message"><a href="{$url}">{$text}</a></td>
    <td class="view-message inbox-small-cells">{$attack}</td>
    <td class="view-message text-right">{$date}</td>
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
     * Check active tab
     * @param string $current Current page
     * @return string
     */
    private function CheckActive($current) {
        $param = \Url::getURL($this->URL_ACTION);
        if ($param == $current || $current == 'inbox' && !isset($param)) {
            return ' class="active"';
        }
    }

    /**
     * Check it's already on page spam to change mark
     * @param string $url
     * @return string
     */
    private function CheckSpam($url) {
        $param = \Url::getURL($this->URL_ACTION);
        if (isset($param) && $param == 'spam') {
            return <<<EOF
<li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente mover para caixa de entrada ?','{$url}/spam_all/0')"><i class="fa fa-times-circle"></i> Desmarcar Spam</a></li>
EOF;
        } else {
            return <<<EOF
<li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente mover para spam ?','{$url}/spam_all/1')"><i class="fa fa-times-circle"></i> Spam</a></li>
EOF;
        }
    }

    /**
     * Check it's already on page important to change mark
     * @param string $url
     * @return string
     */
    private function CheckImportant($url) {
        $param = \Url::getURL($this->URL_ACTION);
        if (isset($param) && $param == 'important') {
            return <<<EOF
<li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente mover para caixa de entrada ?','{$url}/important_all/0')"><i class="fa fa-inbox"></i> Caixa de Entrada</a></li>
EOF;
        } else {
            return <<<EOF
<li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente mover para importantes ?','{$url}/important_all/1')"><i class="fa fa-info-circle"></i> Importantes</a></li>
EOF;
        }
    }

    /**
     * Check it's already on page trash to change mark
     * @param string $url
     * @return string
     */
    private function CheckTrash($url) {
        $param = \Url::getURL($this->URL_ACTION);
        if (isset($param) && $param == 'trash') {
            return <<<EOF
<li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente deletar ?','{$url}/delete_all/0')"><i class="fa fa-inbox"></i> Caixa de Entrada</a></li>
<li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente deletar permanente ? não será possivel restaurar depois.','{$url}/delete_all_permanent')"><i class="fa fa-times-circle"></i> Exclusão Permanente</a></li>

EOF;
        } else {
            return <<<EOF
<li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente deletar ?','{$url}/delete_all/1')"><i class="fa fa-trash-o"></i> Lixeira</a></li>
EOF;
        }
    }

    private function check_title_page($total) {
        
        switch (Url::getURL(3)) {
            case 'sends':
                $result = sprintf("Enviados (%d)", $total);
                break;
            case 'important':
                $result = sprintf("Importantes (%d)", $total);
                break;
            case 'spam':
                $result = sprintf("Spam (%d)", $total);
                break;
            case 'trash':
                $result = sprintf("Lixeira (%d)", $total);
                break;
            default:
                $result = sprintf("Caixa de Entrada (%d)", $total);
                break;
        }
        return $result;
    }

    /**
     * Make row HTML listing mode
     * @param array $Object
     * @return String
     */
    protected function MAKE_LISTING_MODE($total, array $Object) {
        $t_unread = \Func::_contarReg('mensagem', array('lida' => 0,
                    'id_to' => \Session::get('user_id'),
                    'trash' => false,
                    'important' => false,
                    'spam' => false
                        )
        );
        $total_unread = ($t_unread > 0) ? '<span class="label label-danger pull-right inbox-notification">' . $t_unread . '</span>' : NULL;

        $t_spam = \Func::_contarReg('mensagem', array('spam' => 1, 'id_to' => \Session::get('user_id'), 'trash' => 0));
        $total_spam = ($t_spam > 0) ? '<span class="label label-info pull-right inbox-notification">' . $t_spam . '</span>' : NULL;

        $url = URL . FILENAME;

        return <<<EOF
        <!-- page start-->
        <div class="row">
            <div class="col-sm-3">
                <section class="panel">
                    <div class="panel-body">
                        <a href="{$url}{$this->loc_action['add']}"  class="btn btn-compose">
                            Enviar Mensagem
                        </a>
                        <ul class="nav nav-pills nav-stacked mail-nav">
                            <li{$this->CheckActive('inbox')}><a href="{$url}/inbox"> <i class="fa fa-inbox"></i> Entrada  {$total_unread}</a></li>
                            <li{$this->CheckActive('sends')}><a href="{$url}/sends"> <i class="fa fa-mail-forward"></i> Enviados</a></li>
                            <li{$this->CheckActive('important')}><a href="{$url}/important"> <i class="fa fa-info-circle"></i> Importantes</a></li>
                            <li{$this->CheckActive('spam')}><a href="{$url}/spam"> <i class="fa fa-times-circle"></i> Spam {$total_spam}</a></li>
                            <li{$this->CheckActive('trash')}><a href="{$url}/trash"> <i class="fa fa-trash-o"></i> Lixeira</a></li>
                        </ul>
                    </div>
                </section>
            </div>
            <div class="col-sm-9">
                <section class="panel">
                    <header class="panel-heading wht-bg">
                       <h4 class="gen-case">{$this->check_title_page($total)}
                        <form action="{$url}/search/" id="form_search" method="get"  class="pull-right mail-src-position">
                            <div class="input-append">
                                <input onsubmit="Search();" type="text" id="id_search" name="search" class="form-control" placeholder="Buscar">
                            </div>
                        </form>
                       </h4>
                    </header>
        <form action="{$url}/mark_all" id="mark_broadcast" method="POST">  
                    <div class="panel-body minimal">
                        <div class="mail-option">
                            <div class="chk-all">
                                <div class="pull-left mail-checkbox center">
                                   <input type="checkbox" id="checkAll">
                                </div>

                                <div class="btn-group">
                                    <a data-toggle="dropdown" href="#" class="btn mini all">
                                        Todas
                                        <i class="fa fa-angle-down "></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{$url}?mark=all"> Todas</a></li>
                                        <li><a href="{$url}?mark=read"> Lidas</a></li>
                                        <li><a href="{$url}?mark=unread"> Não Lidas</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="btn-group">
                                <a data-original-title="Atualizar" data-placement="top" data-toggle="dropdown" href="{$url}" class="btn mini tooltips">
                                    <i class=" fa fa-refresh"></i>
                                </a>
                            </div>
                            <div class="btn-group hidden-phone">
                                <a data-toggle="dropdown" href="#" class="btn mini blue">
                                    Marca como
                                    <i class="fa fa-angle-down "></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente marcar como lidas ?','{$url}/mark_all/1')"><i class="fa fa-pencil"></i> Lida(s)</a></li>
                                    <li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente marcar como não lidas ?','{$url}/mark_all/0')"><i class="fa fa-pencil"></i> Não lida(s)</a></li>
                                    <li class="divider"></li>
                                    <li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente marcar como favorito(s) ?','{$url}/mark_all_star/1')"><i class="fa fa-star"></i> Favorito(s)</a></li>
                                    <li><a href="{$url}#" onclick="javascript:mark_all(' Deseja realmente marcar como não favorito(s) ?','{$url}/mark_all_star/0')"><i class="fa fa-star"></i> Não Favorito(s)</a></li>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <a data-toggle="dropdown" href="#" class="btn mini blue">
                                    Move para
                                    <i class="fa fa-angle-down "></i>
                                </a>
                                <ul class="dropdown-menu">
                                    {$this->CheckTrash($url)}
                                    {$this->CheckImportant($url)}
                                    {$this->CheckSpam($url)}
                                </ul>
                            </div>
                            {$Object['show_pagination']}
                        </div>
                        <div class="table-inbox-wrap ">
                            <table class="table table-inbox table-hover checkboxs">
                        <tbody>{$Object['elements_table']}</tbody>
                        </table>

                        </div>
                    </div>
        </form>
                </section>
            </div>
        </div>
        <!-- page end-->
EOF;
    }

    private function CheckAttack($idMessage) {
        $where = array(
            'id_message' => $idMessage
        );
        $url = URL;

        $total = Func::_contarReg('mensagem_attack', $where);
        if ($total > 0) {
            $q = new Query();
            $q
                    ->select()
                    ->from('mensagem_attack')
                    ->where_equal_to($where)
                    ->order_by('id ASC')
                    ->run();
            $msg = $total == 1 ? '1 Anexo' : $total . ' Anexos';
            $result = <<<EOF
    <div class="attachment-mail">
        <p><span><i class="fa fa-paperclip"></i> {$msg}</span></p>
        <ul>
EOF;
            if ($q) {
                foreach ($q->get_selected() as $data) {
                    $f = Func::str_truncate($data['file'], 15);
                    $b = Func::formatBytes($data['size']);
                    $result.= <<<EOF
<li>
    <a class="atch-thumb" href="{$url}public/dashboard/attachment/{$data['file']}" target="blank">
        <img src="attachment/{$data['file']}">
    </a>

    <a class="name" href="{$url}public/dashboard/attachment/{$data['file']}">
        {$f}
        <span>{$b}</span>
    </a>

    <div class="links">
        <a href="{$url}public/dashboard/attachment/{$data['file']}" target="blank">Visualizar</a>
    </div>
</li>          
EOF;
                }
                $result.= <<<EOF
        </ul>
    </div>
EOF;
                return $result;
            }
        }
    }

    private function CheckMessagesReply($idMessage) {
        $where = array(
            'id_message' => $idMessage
        );
        $url = URL;
        $fullUrl = URL . FILENAME;
        $total = Func::_contarReg('mensagem_attack', $where);
        if ($total > 0) {
            $q = new Query();
            $q
                    ->select()
                    ->from('mensagem_reply')
                    ->where_equal_to($where)
                    ->order_by('id ASC')
                    ->run();
            $result = '';
            if ($q) {
                $furl = URL . FILENAME;
                foreach ($q->get_selected() as $data) {
                    $date = strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($data['data']));

                    $name = \GetInfo::_name($data['id_user']);
                    $email = \GetInfo::Email($data['id_user']);
                    $pic = \GetInfo::_foto($data['id_user']);

                    $result.= <<<EOF
<div id="reply{$data['id']}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form action="{$fullUrl}/reply/{$idMessage}" class="cmxform form-horizontal" id="signupForm" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Responder</h4>
            </div>
            <div class="modal-body">
                 <textarea name="message" class="wysihtml5 form-control" rows="12"><blockquote>{$data['text']}</blockquote> <br></textarea>
            </div>
            <div class="modal-footer">
                 <button class="btn btn-primary" type="submit">Responder</button>
                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true" type="reset">Cancelar</button>
            </div>
        </div>
    </div>
    </form>
</div>
                        
<div class="PrintArea area{$data['id']} all">
<div class="panel-body ">
    <div class="mail-sender">
        <div class="row">
            <div class="col-md-8">
                <img src="{$pic}" alt="">
                <strong>{$name}</strong>
                <span>[{$email}]</span>
            </div>
            <div class="col-md-4">
                <p class="date"> {$date}</p>
            </div>
        </div>
    </div>
    <div class="view-mail">
        {$data['text']}
    </div>
    
    <div class="compose-btn pull-left">
        <a href="{$furl}/preview/{$idMessage}#reply{$data['id']}" data-toggle="modal" class="btn btn-sm btn-primary" ><i class="fa fa-reply"></i> Responder</a>
        <button value="{$data['id']}" class="btn btn-sm tooltips" data-original-title="Imprimir" type="button" data-toggle="tooltip" data-placement="top" title="Imprimir mensagem" id="button-print"><i class="fa fa-print"></i> </button>
    </div>
</div>
</div>
EOF;
                }
                return $result;
            }
        }
    }

    /**
     * Tables preview mode
     * @param array $param Query result
     * @return String
     */
    private function _make_list_mode_tables(array $param) {
        $date = strftime('%d de %B, %Y &aacute;s %H:%M:%S', strtotime($param['data']));
        if ($param['id_from'] == \Session::get('user_id')) {
            $name = \GetInfo::_name($param['id_from']);
            $email = \GetInfo::Email($param['id_from']);
            $pic = \GetInfo::_foto($param['id_from']);
        } else {
            $name = \GetInfo::_name($param['id_to']);
            $email = \GetInfo::Email($param['id_to']);
            $pic = \GetInfo::_foto($param['id_to']);
        }
        $url = URL . FILENAME;
        return <<<EOF
<div id="main{$param['id']}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form action="{$url}/reply/{$param['id']}" class="cmxform form-horizontal" id="signupForm" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Responder</h4>
            </div>
            <div class="modal-body">
                 <textarea name="message" class="wysihtml5 form-control" rows="12"><blockquote>{$param['text']}</blockquote> <br></textarea>
            </div>
            <div class="modal-footer">
                 <button class="btn btn-primary" type="submit">Responder</button>
                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true" type="reset">Cancelar</button>
            </div>
        </div>
    </div>
    </form>
</div>
                        
<div class="PrintArea mainarea{$param['id']} all">
<div class="panel-body ">
    <div class="mail-header row">
        <div class="col-md-8">
            <h4 class="pull-left">{$param['title']}</h4>
        </div>
        <div class="col-md-4">
            <div class="compose-btn pull-right">
                    <a href="{$url}/preview/{$param['id']}#main{$param['id']}" data-toggle="modal" class="btn btn-sm btn-primary" ><i class="fa fa-reply"></i> Responder</a>
                    <button value="{$param['id']}" class="btn btn-sm tooltips" data-original-title="Imprimir" type="button" data-toggle="tooltip" data-placement="top" title="Imprimir mensagem" id="button-print-main"><i class="fa fa-print"></i> </button>
            </div>
        </div>

    </div>
    <div class="mail-sender">
        <div class="row">
            <div class="col-md-8">
                <img src="{$pic}" alt="">
                <strong>{$name}</strong>
                <span>[{$email}]</span>
            </div>
            <div class="col-md-4">
                <p class="date">{$date}</p>
            </div>
        </div>
    </div>
    <div class="view-mail">
        {$param['text']}
    </div>
    {$this->CheckAttack($param['id'])}    
            
    <div class="compose-btn pull-left">
       <a href="{$url}/preview/{$param['id']}#main{$param['id']}" class="btn btn-sm btn-primary" ><i class="fa fa-reply"></i> Responder</a>
       <button value="{$param['id']}" class="btn btn-sm tooltips" data-original-title="Imprimir" type="button" data-toggle="tooltip" data-placement="top" title="Imprimir mensagem" id="button-print-main"><i class="fa fa-print"></i> </button>
    </div>
</div>
</div>
      {$this->CheckMessagesReply($param['id'])}
EOF;
    }

    /**
     * Make row HTML listing mode
     * @param array $Object
     * @return String
     */
    protected function MAKE_PREVIEW_MODE($total, array $Object, array $data) {
        $t_unread = \Func::_contarReg('mensagem', array('lida' => 0,
                    'id_to' => \Session::get('user_id'),
                    'trash' => false,
                    'important' => false,
                    'spam' => false
                        )
        );
        $total_unread = ($t_unread > 0) ? '<span class="label label-danger pull-right inbox-notification">' . $t_unread . '</span>' : NULL;

        $t_spam = \Func::_contarReg('mensagem', array('spam' => 1, 'id_to' => \Session::get('user_id'), 'trash' => 0));
        $total_spam = ($t_spam > 0) ? '<span class="label label-info pull-right inbox-notification">' . $t_spam . '</span>' : NULL;

        $url = URL . FILENAME;

        return <<<EOF
        <!-- page start-->
        <div class="row">

            <div class="col-sm-3">
                <section class="panel">
                    <div class="panel-body">
                        <a href="{$url}{$this->loc_action['add']}"  class="btn btn-compose">
                            Enviar Mensagem
                        </a>
                        <ul class="nav nav-pills nav-stacked mail-nav">
                            <li{$this->CheckActive('inbox')}><a href="{$url}/inbox"> <i class="fa fa-inbox"></i> Entrada  {$total_unread}</a></li>
                            <li{$this->CheckActive('sends')}><a href="{$url}/sends"> <i class="fa fa-mail-forward"></i> Enviados</a></li>
                            <li{$this->CheckActive('important')}><a href="{$url}/important"> <i class="fa fa-info-circle"></i> Importantes</a></li>
                            <li{$this->CheckActive('spam')}><a href="{$url}/spam"> <i class="fa fa-times-circle"></i> Spam {$total_spam}</a></li>
                            <li{$this->CheckActive('trash')}><a href="{$url}/trash"> <i class="fa fa-trash-o"></i> Lixeira</a></li>
                        </ul>
                    </div>
                </section>
            </div>
            <div class="col-sm-9">
                <section class="panel">
                    <header class="panel-heading wht-bg">
                       <h4 class="gen-case">Visualizar Mensagem
                        <form action="{$url}/search/" id="form_search" method="get"  class="pull-right mail-src-position">
                            <div class="input-append">
                                <input onsubmit="Search();" type="text" id="id_search" name="search" class="form-control" placeholder="Buscar">
                            </div>
                        </form>
                       </h4>
                    </header>
                        {$this->_make_list_mode_tables($data)}
                </section>
            </div>
        </div>
        <!-- page end-->
EOF;
    }

    /**
     * Right Elements Insert mode
     * @return String
     */
    private function RIGHT_ELEMENTS_INSER_NEW() {
        $URL = URL . FILENAME . DS . Url::getURL($this->URL_ACTION) . DS;

        return <<<EOF
<div class="panel-body">
<div class="compose-mail">
    <form enctype="multipart/form-data" action="{$URL}new" role="form-horizontal" method="post">
        <div class="form-group">
            <label for="to" class="">Para:</label>
            <input name="to" type="text" tabindex="1" id="autocomplete" class="form-control">
        </div>

        <div class="form-group">
            <label for="subject" class="">Assunto:</label>
            <input type="text" name="subject" tabindex="1" id="subject" class="form-control">
        </div>

        <div class="compose-editor">
            <textarea name="message" class="wysihtml5 form-control" rows="9"></textarea>
            <input type="file" name="img" id="img" class="default" />
        </div>
    
        <div class="compose-btn">
            <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-check"></i> Enviar</button>
            <button class="btn btn-default btn-sm" type="reset"><i class="fa fa-times"></i> Discartar</button>
        </div>

    </form>
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
          
</div>
EOF;
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
    protected function HTML_Insert_New($total, array $Object, array $data) {
        $t_unread = \Func::_contarReg('mensagem', array('lida' => 0,
                    'id_to' => \Session::get('user_id'),
                    'trash' => false,
                    'important' => false,
                    'spam' => false
                        )
        );
        $total_unread = ($t_unread > 0) ? '<span class="label label-danger pull-right inbox-notification">' . $t_unread . '</span>' : NULL;

        $t_spam = \Func::_contarReg('mensagem', array('spam' => 1, 'id_to' => \Session::get('user_id'), 'trash' => 0));
        $total_spam = ($t_spam > 0) ? '<span class="label label-info pull-right inbox-notification">' . $t_spam . '</span>' : NULL;

        $url = URL . FILENAME;

        return <<<EOF
        <!-- page start-->
        <div class="row">
<div id="reply1" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Responder</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                     <label for="firstname" class="control-label col-lg-4">CEP</label>
                    <div class="col-md-11">
                        <textarea class="wysihtml5 form-control" rows="9"><blockquote>something</blockquote></textarea>
                    </div>
                </div> 

            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Fechar</button>
            </div>
        </div>
    </div>
</div>
                        
            <div class="col-sm-3">
                <section class="panel">
                    <div class="panel-body">
                        <a href="{$url}{$this->loc_action['add']}"  class="btn btn-compose">
                            Enviar Mensagem
                        </a>
                        <ul class="nav nav-pills nav-stacked mail-nav">
                            <li{$this->CheckActive('inbox')}><a href="{$url}/inbox"> <i class="fa fa-inbox"></i> Entrada  {$total_unread}</a></li>
                            <li{$this->CheckActive('sends')}><a href="{$url}/sends"> <i class="fa fa-mail-forward"></i> Enviados</a></li>
                            <li{$this->CheckActive('important')}><a href="{$url}/important"> <i class="fa fa-info-circle"></i> Importantes</a></li>
                            <li{$this->CheckActive('spam')}><a href="{$url}/spam"> <i class="fa fa-times-circle"></i> Spam {$total_spam}</a></li>
                            <li{$this->CheckActive('trash')}><a href="{$url}/trash"> <i class="fa fa-trash-o"></i> Lixeira</a></li>
                        </ul>
                    </div>
                </section>
            </div>
            <div class="col-sm-9">
                <section class="panel">
                    <header class="panel-heading wht-bg">
                       <h4 class="gen-case">Enviar Mensagem
                        <form action="{$url}/search/" id="form_search" method="get"  class="pull-right mail-src-position">
                            <div class="input-append">
                                <input onsubmit="Search();" type="text" id="id_search" name="search" class="form-control" placeholder="Buscar">
                            </div>
                        </form>
                       </h4>
                    </header>
                        {$this->RIGHT_ELEMENTS_INSER_NEW($data)}
                </section>
            </div>
        </div>
        <!-- page end-->
EOF;
    }

}

/**
 * Class Required CSS and Javascript
 * @todo put in array the path files
 */
class requireds extends InboxConfig {

    /**
     * Load JS for page listing mode
     * @return string
     */
    private function JS_REQUIRED_LISTING() {
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

<script src="js/iCheck/jquery.icheck.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<!--icheck init 
<script src="js/icheck-init.js"></script>
        -->

EOF;
    }

    /**
     * Load CSS for page listing mode
     * @return string
     */
    private function CSS_REQUIRED_LISTING() {
        return <<<EOF
    <!--icheck-->
    <link href="js/iCheck/skins/minimal/minimal.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/style.css?v=1" rel="stylesheet">
EOF;
    }

    /**
     * Load all CSS required for page Insert Mode
     * @return String
     */
    private function CSS_REQUIRED_INSERT() {
        return <<<EOF
    <link rel="stylesheet" href="css/bootstrap-switch.css" />
    <!--icheck-->
    <link href="js/iCheck/skins/minimal/minimal.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="js/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-fileupload/bootstrap-fileupload.css" />
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

<script type="text/javascript" src="js/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="js/bootstrap-fileupload/bootstrap-fileupload.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

<script type="text/javascript" src="js/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.quicksearch.js"></script>

<script src="js/jquery-tags-input/jquery.tagsinput.js"></script>

<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<script src="js/toggle-init.js"></script>

<script src="js/advanced-form.js"></script>  
<!--this page script-->
<script src="js/validation-init.js"></script>
<script>{$this->LoopAutocompleteUsers()}</script>

EOF;
    }

    /**
     * list users to auto complete
     * @return string Javascript Data
     */
    private function LoopAutocompleteUsers() {
        $q = new Query();
        $q
                ->select()
                ->from('users')
                ->order_by(
                        array(
                            'user_first_name',
                            'user_last_name'
                        )
                )
                ->run();
        $result = '';
        foreach ($q->get_selected() as $value) {
            $result.= <<<EOF
        {value: "{$value['user_email']}", label: "{$value['user_first_name']} {$value['user_last_name']} <{$value['user_email']}>"},
EOF;
        }

        return "var data_autocomplete = [$result];";
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
<script type="text/javascript" src="js/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>


<!--Print documment script -->
<script src="js/printThis.js"></script>

<script>
    //wysihtml5 start
    $('.wysihtml5').wysihtml5();
    //wysihtml5 end

    $(document).ready(function(){
        $("#button-print").click(function(){
            var id = $('#button-print').val();
            var print = "";
            $("input.selPA:checked").each(function(){
                print += (print.length > 0 ? "," : "") + "div.PrintArea." + $(this).val();
            });
       
            var options = { 
                mode : "iframe", 
               // extraCss: "css/print.css",
                //popTitle : "Recibo #27"
                popClose : true,  
                extraHead : '<meta charset="utf-8" />,<meta http-equiv="X-UA-Compatible" content="IE=edge"/>' 
                };
            $('div.PrintArea.area'+id).printArea( options );
        });
    });
/*
$(function () {
    $("#button-print").click(function () {        
        $('#print-receipt').printThis({
                pageTitle: 'Recibo #27', // add title to print page
        });
    });
});*/
</script>
        
        
<script>
    $(document).ready(function(){
        $("#button-print-main").click(function(){
            var id = $('#button-print-main').val();
            var print = "";
            $("input.selPA:checked").each(function(){
                print += (print.length > 0 ? "," : "") + "div.PrintArea." + $(this).val();
            });
       
            var options = { 
                mode : "iframe", 
               // extraCss: "css/print.css",
                //popTitle : "Recibo #27"
                popClose : true,  
                extraHead : '<meta charset="utf-8" />,<meta http-equiv="X-UA-Compatible" content="IE=edge"/>' 
                };
            $('div.PrintArea.mainarea'+id).printArea( options );
        });
    });
/*
$(function () {
    $("#button-print-main").click(function () {        
        $('#print-receipt').printThis({
                pageTitle: 'Recibo #27', // add title to print page
        });
    });
});*/
</script>
EOF;
    }

    /**
     * Load CSS for page Preview mode
     * @return string
     */
    private function CSS_REQUIRED_PREVIEW() {
        return <<<EOF
    <!--icheck-->
    <link href="js/iCheck/skins/minimal/minimal.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="js/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
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
