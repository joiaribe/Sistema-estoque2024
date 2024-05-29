<?php

namespace notifier\Inbox;

use \Query as Query;
use Developer\Tools\Url as Url;

/**
 * Class para listagem
 */
class InboxSearch extends InboxHTML {

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
                    'important' => true,
                );
                break;
            case 'spam':
                $this->where_criter = array(
                    'id_to' => \Session::get('user_id'),
                    'spam' => true,
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
        $keyword = filter_input(INPUT_GET, 'search');
        // defines if the parameter does not exist
        $id = isset($param) ? $param : 1;
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to($this->where_criter)
                ->where_like_or(
                        array(
                            'title' => $keyword,
                            'text' => $keyword
                        )
                )
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
     * load function jquery used for multpliples checkboxes
     * @return String
     */
    private function JS_CHECKBOXES() {
        return <<<EOF
<script>
$("#select-all").click(function(event) {
    if (this.checked) {
        // Iterate each checkbox
        $(":checkbox").each(function() {
            this.checked = true;
        });
    } else {
        $(":checkbox").each(function() {
            this.checked = false;
        });
    }
});
</script> 
EOF;
    }

    /**
     * controi classe método mágico
     * @access public
     * @return main
     */
    public function __construct() {
        $this->CheckCriter();
        $Object = array(
            'body_table' => $this->body_table(),
            'tools' => $this->make_tools(),
            'elements_table' => $this->Query(),
            'show_pagination' => $this->Query('pagination')
        );
        return print
                $this->_LOAD_REQUIRED_LISTING() .
                $this->MAKE_LISTING_MODE($this->total_with_criter, $Object) .
                $this->JS_CHECKBOXES();
    }

}
