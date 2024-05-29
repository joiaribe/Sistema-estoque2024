<?php

namespace notifier\Inbox;

use Query as Query;
use Developer\Tools\Url as Url;

/**
 * Classe para visualização
 */
class InboxPreview extends InboxHTML {

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
     * Mark read message
     * @param integer $id
     */
    private function MarkMessagePreview($id) {
        $q = new Query();
        $q
                ->update($this->table)
                ->set(
                        array(
                            'lida' => true
                        )
                )
                ->where_equal_to(
                        array(
                            'id' => $id
                        )
                )
                ->run();
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
     * controi e printa conteudo.
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
                $this->MAKE_PREVIEW_MODE($this->total_with_criter, $Object, $this->verify_query()) .
                $this->_REQUIRED_PREVIEW_MODE();
    }

    /**
     * método mágico controi a tabela
     * @access private
     * @return Main
     */
    public function __construct() {
        $this->MarkMessagePreview(Url::getURL($this->URL_ACTION + 1));
        return $this->_build();
    }

}
