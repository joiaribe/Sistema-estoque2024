<?php

namespace Reports\Comission;

use Query as Query;

/**
 * Class para listagem
 */
class ComissionMain extends ComissionHTML {

    /**
     * Query per user
     * @return string
     */
    private function QueryPerUser() {
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(self::CriterFilter())
                ->where_not_equal_to(
                        array(
                            'id_employee' => NULL
                        )
                )
                ->order_by('data asc')
                ->group_by('id_employee')
                ->run();
        $result = '';
        if ($q) {
            foreach ($q->get_selected() as $data) {
                $result.= $this->PerUser($data);
            }
            return $result;
        }
    }

    /**
     * Criter filter
     * @return array
     */
    private static function CriterFilter() {
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
        // Check employee
        if (self::GetParam('employee') !== 'all' && self::GetParam('employee')) {
            $criter['id_employee'] = self::GetParam('employee');
        }
        // Check Service
        if (self::GetParam('service') !== 'all' && self::GetParam('service')) {
            $criter['id_service'] = self::GetParam('service');
        }

        return $criter;
    }

    /**
     * Query per service
     * @return string
     */
    private function QueryPerService() {
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(self::CriterFilter())
                ->where_not_equal_to(
                        array(
                            'id_employee' => NULL
                        )
                )
                ->order_by('data asc')
                ->group_by('id_service')
                ->run();

        $result = '';
        if ($q) {
            foreach ($q->get_selected() as $data) {
                $result.= $this->PerService($data);
            }
            return $result;
        }
    }

    /**
     * Query per service
     * @return string
     */
    private function QueryPerClient() {
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(self::CriterFilter())
                ->where_not_equal_to(
                        array(
                            'id_employee' => NULL
                        )
                )
                ->order_by('data asc')
                ->group_by('id_client')
                ->run();

        $result = '';
        if ($q) {
            foreach ($q->get_selected() as $data) {
                $result.= $this->PerClient($data);
            }
            return $result;
        }
    }

    /**
     * loop services
     * @return string
     */
    private function loop_services() {
        $q = new Query();
        $q
                ->select()
                ->from('servicos')
                ->order_by('titulo asc')
                ->run();

        $result = '<option value="all">-- Todos --</option>';

        foreach ($q->get_selected() as $v) {
            $result.= '<option value="' . $v['id'] . '">' . $v['titulo'] . '</option>';
        }
        return $result;
    }

    /**
     * loop employee
     * @return string
     */
    private function loop_users() {
        $q = new Query();
        $q
                ->select()
                ->from('funcionarios')
                ->order_by('nome asc')
                ->run();

        $result = '<option value="all">-- Todos --</option>';

        foreach ($q->get_selected() as $v) {
            $result.= '<option value="' . $v['id'] . '">' . $v['nome'] . '</option>';
        }
        return $result;
    }

    /**
     * controi classe método mágico
     * @access public
     * @return main
     */
    public function __construct() {
        #  $param = Url::getURL(3);
        # $ac = Url::getURL(5);
        #if (isset($param) && $param == 'mark' && ($ac == false || $ac == true)) {
        #     $this->Mark();
        # }
        $Object = array(
            'PerUser' => $this->QueryPerUser(),
            'PerService' => $this->QueryPerService(),
            'PerClient' => $this->QueryPerClient()
        );
        return print
                $this->_LOAD_REQUIRED_MAIN() .
                $this->ModalListing($this->loop_services(), $this->loop_users()) .
                $this->MAKE_MAIN($Object);
    }

}
