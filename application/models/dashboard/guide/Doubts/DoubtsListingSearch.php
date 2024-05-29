<?php

namespace guide\Doubts;

use \Query as Query;

/**
 * Class para listagem
 */
class DoubtsListingSearch extends DoubtsHTML {

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query() {
        $keyword = filter_input(INPUT_GET, 'search');
        $q = new Query;
        $q
                ->select()
                ->from($this->table)
                ->where_like_or(
                        array(
                            'titulo' => $keyword,
                            'texto' => $keyword
                        )
                )
                ->order_by('id desc')
                ->run();

        $i = 0;
        $left = '<div class="col-md-4 column sortable">';
        $center = '<div class="col-md-4 column sortable">';
        $right = '<div class="col-md-4 column sortable">';

        foreach ($q->get_selected() as $data) {
            if ($data['position'] == 'left')
                $left.= $this->contain_table($data, $i++);

            if ($data['position'] == 'center')
                $center.= $this->contain_table($data, $i++);

            if ($data['position'] == 'right')
                $right.= $this->contain_table($data, $i++);
        }
        return $left . '</div>' . $center . '</div>' . $right . '</div>';
    }

    /**
     * controi classe método mágico
     * @access public
     * @return main
     */
    public function __construct() {
        return print
                $this->_LOAD_REQUIRED_LISTING() .
                $this->MAKE_LISTING_MODE($this->Query());
    }

}
