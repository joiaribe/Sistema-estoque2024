<?php

namespace notifier\Inbox;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Classe para visualização
 */
class InboxInsert extends InboxHTML {

    /**
     * criter for listing mode
     * @var array
     */
    var $where_criter = array();

    /**
     * Total reg with criter
     * @var integer 
     */
    var $total_with_criter;

    /**
     * Query data results
     * @var array 
     */
    var $pic = NULL;

    /**
     * Builds page insert new registry and makes form HTML
     * @access private
     * @return object
     */
    private function _build() {
        $this->CheckCriter();
        $Object = array(
            'elements_table' => $this->Query(),
            'show_pagination' => $this->Query('pagination')
        );
        return print
                $this->HTML_Insert_New($this->total_with_criter, $Object, $this->verify_query()) .
                $this->_LOAD_REQUIRED_INSERT();
    }

    /**
     * Check Criter
     * @return Void
     */
    private function CheckCriter() {
        $param = \Url::getURL($this->URL_ACTION);
        switch ($param) {
            case 'sends':
                $this->where_criter = array(
                    'id_from' => \Session::get('user_id'),
                    'spam' => false,
                    'important' => false,
                    'trash' => false
                );
                break;
            case 'important':
                $this->where_criter = array(
                    'id_to' => \Session::get('user_id'),
                    'important' => true
                );
                break;
            case 'spam':
                $this->where_criter = array(
                    'id_to' => \Session::get('user_id'),
                    'spam' => true
                );
                break;
            case 'trash':
                $this->where_criter = array(
                    'id_to' => \Session::get('user_id'),
                    'trash' => true
                );
                break;
            default:
                $this->where_criter = array(
                    'id_to' => \Session::get('user_id'),
                    'spam' => false,
                    'important' => false,
                    'trash' => false
                );
                break;
        }
    }

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query($p = NULL) {
        $param = Url::getURL($this->URL_ACTION + 1);
        // defines if the parameter does not exist
        $id = isset($param) ? $param : 1;
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to($this->where_criter)
                ->order_by('id ASC')
                ->page($id)
                ->limit($this->LimitPerPage)
                ->run();
        $this->total_with_criter = $q->get_selected_count();
        $result = '';
        //pagination
        if ($p !== NULL && $this->total_with_criter > 0) {
            $q->class_after = 'np-btn';
            $q->class_before = 'np-btn';
            $q->message = false;
            $q->message_after = '<i class="fa fa-angle-right  pagination-right"></i>';
            $q->message_before = '<i class="fa fa-angle-left pagination-left"></i>';
            $loop = $q->make_pages(URL . FILENAME . DS, $id);
            $t = \Func::_contarReg($this->table, $this->where_criter);
            echo $this->pagination($loop, $this->LimitPerPage, $t);
            return NULL;
        } else {
            if ($q) {
                foreach ($q->get_selected() as $data) {
                    $result.= $this->contain_table($data);
                }
                return $result;
            }
        }
    }

    /**
     * Query data
     * @access private
     * @return Array
     */
    private function verify_query() {
        $id = Url::getURL($this->URL_ACTION + 1);
        $q = new Query;
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'id' => $id
                        )
                )
                ->limit(1)
                ->run();
        return $q->get_selected();
    }

    /**
     * Check param for inset a new registry
     * @return Void
     */
    private function Check_Insert() {
        $param = Url::getURL($this->URL_ACTION + 1);
        if ($param == 'new') {
            $this->Insert_banner();
            $this->Insert_On_Database();
        }
    }

    /**
     * Insert new banner
     * @return String
     */
    private function Insert_banner() {
        $file = $_FILES['img']['error'];
        if (!$file > 0) {
            $path = 'public/dashboard/attachment/';
            $handle = new \Upload($_FILES['img']);
            if ($handle->uploaded) {
                $encript = substr(md5(microtime()), 0, 32) . '_n';
                $handle->file_new_name_body = $encript;
                $handle->image_resize = false;
                $handle->image_ratio_y = false;
                $handle->process($path);
                $nome_da_imagem = $handle->file_dst_name;
                if ($handle->processed) {
                    $this->pic = $nome_da_imagem;
                } else {
                    Call_JS::alerta('Error : ' . $handle->error);
                    Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page);
                }
            }
        }
    }

    private function Insert_Attackment($id) {
        $path = 'public/dashboard/attachment/';
        $q = new Query();
        $q
                ->insert_into(
                        'mensagem_attack', array(
                    'id_message' => $id,
                    'size' => filesize($path . $this->pic),
                    'file' => $this->pic
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
    }

    /**
     * Insert new registry
     * @access private
     * @return void
     */
    private function Insert_On_Database() {
        $where = array(
            'user_email' => filter_input(INPUT_POST, 'to')
        );
        $to = \Func::array_table('users', $where, 'user_id');
        $q = new Query();
        $q
                ->insert_into(
                        $this->table, array(
                    'id_from' => \Session::get('user_id'),
                    'id_to' => $to,
                    'title' => filter_input(INPUT_POST, 'subject'),
                    'text' => filter_input(INPUT_POST, 'message'),
                    'lida' => true
                        )
                )
                ->run();
        if (!$q) {
            die('was not possible to insert a new ' . $this->msg['singular']);
        }
        if ($this->pic !== NULL) {
            $this->Insert_Attackment($q->get_insert_id());
        }
        Call_JS::alerta($this->msg['singular'] . " enviada com sucesso! ");
        Call_JS::retornar(URL . 'dashboard/Notifier/' . $this->page . '/sends');
    }

    /**
     * Method Magic this script is used for buy my weed
     * @access public
     * @return main
     */
    public function __construct() {
        $this->Check_Insert();
        return $this->_build();
    }

}
