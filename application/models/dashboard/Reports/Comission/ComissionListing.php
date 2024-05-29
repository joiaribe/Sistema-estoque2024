<?php

namespace Reports\Comission;

use Query as Query;
use Developer\Tools\Url as Url;
use Dashboard\Call_JS as Call_JS;

/**
 * Class para listagem
 */
class ComissionListing extends ComissionHTML {

    private static function CheckParamAction($param, $param2) {
        $arr = array();
        switch ($param) {
            case 'PreviewPerClient':
                $arr['id_client'] = $param2;
                break;
            case 'PreviewPerService':
                $arr['id_client'] = $param2;
                break;
            case 'PreviewPerUser':
                $arr['id_employee'] = $param2;
                break;
            default:
                break;
        }
        return $arr;
    }

    /**
     * Criter filter
     * @return array
     */
    private static function CriterFilter($param, $param2) {
        $criter = array();
        // Check month
        if (self::GetParam('month')) {
            $criter['MONTH(data)'] = self::GetParam('month');
        } else {
            $criter['MONTH(data)'] = date('m');
        }
        // Check year
        if (self::GetParam('year')) {
            $criter['YEAR(data)'] = self::GetParam('year');
        } else {
            $criter['YEAR(data)'] = date('Y');
        }
        $action = self::CheckParamAction($param, $param2);
        $result = array_merge($criter, $action);



        // in case is id_user, id_client or id_service
        #$criter[$column] = $value;


        return $result;
    }

    /**
     * verifica qual query vai ser chamada dependendo do cargo
     * @access protected
     * @return object
     */
    protected function Query() {
        $param = Url::getURL($this->URL_ACTION + 1);
        $param2 = Url::getURL($this->URL_ACTION + 2);
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(self::CriterFilter($param, $param2))
                ->order_by(
                        array(
                            'status asc',
                            'data asc'
                        )
                )
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
        $param = Url::getURL(3);
        $ac = Url::getURL(5);
        
        if (isset($param) && $param == 'MarkSingle' && ($ac == false || $ac == true)) {
            $this->Mark();
        }
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
