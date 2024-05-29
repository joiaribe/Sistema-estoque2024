<?php

namespace Manager\Fonts;

use \Query as Query;

/**
 * Class para listagem
 */
class FontsListing extends FontsHTML {

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query() {
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->order_by('titulo asc')
                ->run();
        $result = '';
        if ($q) {
            foreach ($q->get_selected() as $data) {
                $result.= $this->contain_table($data);
            }
            return $result;
        }
    }

    /**
     * load function jquery used for multpliples checkboxes
     * @return String
     */
    private function JS_CHECKBOXES() {
        return <<<EOF
<script language="JavaScript">
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
        $Object = array(
            'body_table' => $this->body_table(),
            'tools' => $this->make_tools(),
            'elements_table' => $this->Query()
        );
        return print
                $this->_LOAD_REQUIRED_LISTING() .
                $this->MAKE_LISTING_MODE($Object) .
                $this->JS_CHECKBOXES();
    }

}
