<?php

namespace Manager\Receipt;

use Query as Query;
use Developer\Tools\Url as Url;

/**
 * Classe para visualização
 */
class ReceiptPreview extends ReceiptHTML {

    var $DataQuery = array();

    /**
     * verifica qual query vai chamar
     * @access private
     * @param Unknow $param nome da coluna na DB
     * @return Object
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
        $this->DataQuery = $q->get_selected();
    }

    /**
     * controi e printa conteudo.
     * @access private
     * @return object
     */
    private function _build() {
        return print
                $this->_REQUIRED_PREVIEW_MODE($this->DataQuery) .
                $this->MAKE_PREVIEW_MODE($this->DataQuery);
    }

    /**
     * método mágico controi a tabela
     * @access private
     * @return Main
     */
    public function __construct() {
        $this->verify_query();
        return $this->_build();
    }

}
