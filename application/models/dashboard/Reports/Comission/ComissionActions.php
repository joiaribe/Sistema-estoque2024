<?php

namespace Reports\Employee;

use Dashboard\Call_JS as Call_JS;
use Developer\Tools\Url as Url;
use Query as Query;
use Reports\Comission\ComissionConfig as ComissionConfig;

/**
 * Actions
 */
class ComissionActions extends ComissionConfig {

    /**
     * Magic Metthod
     * @access public
     * @return void
     */
    public function __construct() {
        $this->verify_del();
        $this->verify_mark();
        $this->MarkSingle();
    }

    private function MarkSingle() {
        $q = new Query();
        $q
                ->update($this->table, array(
                    'status' => Url::getURL(5)
                        )
                )
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(4)
                        )
                )
                ->limit(1)
                ->run();
        $recover = DS . self::GetParam('preview_type') . DS . self::GetParam('preview_id');
        $m = Url::getURL(5) == true ? 'Comissão marcada como pago' : 'Comissão marcada como não pago';
        Call_JS::alerta($m);
        Call_JS::retornar(URL . 'dashboard/Reports/commission/PreviewListing' . $recover);
        exit($m);
    }

    /**
     * GET metthod get value of param
     * @param string $name Name param
     * @return mixed
     */
    private static function GetParam($name) {
        $f = filter_input(INPUT_GET, $name);
        return $f;
    }

    /**
     * Check criter filter
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

        return $criter;
    }

    /**
     * Delete comission per user
     * @access private
     * @return void
     */
    private function DeletePerUser() {
        $criter = self::CriterFilter();
        $criter['id_employee'] = Url::getURL($this->URL_ACTION + 2);
        $q = new Query();
        $q
                ->delete($this->table)
                ->where_equal_to($criter)
                ->run();
        if ($q) {
            Call_JS::alerta($this->msg['singular'] . ' por empregado excluidas com sucesso !');
            Call_JS::retornar(URL . 'dashboard/Reports/' . $this->page);
        }
    }

    /**
     * Update status payment comission per user
     * @access private
     * @return void
     */
    private function MarkComissionPerUser() {
        $criter = self::CriterFilter();
        $status = (Url::getURL($this->URL_ACTION + 2) == 'paid') ? true : false;
        $msg = $status == true ? 'pago' : 'pendente';
        $criter['id_employee'] = Url::getURL($this->URL_ACTION + 3);

        $q = new Query();
        $q
                ->update($this->table, array(
                    'status' => $status
                        )
                )
                ->where_equal_to($criter)
                ->run();
        if ($q) {
            Call_JS::alerta($this->msg['singular'] . ' por empregado marcado como ' . $msg . ' com sucesso !');
            Call_JS::retornar(URL . 'dashboard/Reports/' . $this->page);
        }
    }

    /**
     * Update status payment comission per user
     * @access private
     * @return void
     */
    private function MarkComissionPerClient() {
        $criter = self::CriterFilter();
        $status = (Url::getURL($this->URL_ACTION + 2) == 'paid') ? true : false;
        $msg = $status == true ? 'pago' : 'pendente';
        $criter['id_client'] = Url::getURL($this->URL_ACTION + 3);

        $q = new Query();
        $q
                ->update($this->table, array(
                    'status' => $status
                        )
                )
                ->where_equal_to($criter)
                ->run();
        if ($q) {
            Call_JS::alerta($this->msg['singular'] . ' por cliente marcado como ' . $msg . ' com sucesso !');
            Call_JS::retornar(URL . 'dashboard/Reports/' . $this->page);
        }
    }

    /**
     * Update status payment comission per user
     * @access private
     * @return void
     */
    private function MarkComissionPerService() {
        $criter = self::CriterFilter();
        $status = (Url::getURL($this->URL_ACTION + 2) == 'paid') ? true : false;
        $msg = $status == true ? 'pago' : 'pendente';
        $criter['id_service'] = Url::getURL($this->URL_ACTION + 3);

        $q = new Query();
        $q
                ->update($this->table, array(
                    'status' => $status
                        )
                )
                ->where_equal_to($criter)
                ->run();
        if ($q) {
            Call_JS::alerta($this->msg['singular'] . ' por serviço marcado como ' . $msg . ' com sucesso !');
            Call_JS::retornar(URL . 'dashboard/Reports/' . $this->page);
        }
    }

    /**
     * Delete comission per service
     * @access private
     * @return void
     */
    private function DeletePerService() {
        $criter = self::CriterFilter();
        $criter['id_service'] = Url::getURL($this->URL_ACTION + 2);
        $q = new Query();
        $q
                ->delete($this->table)
                ->where_equal_to($criter)
                ->run();
        if ($q) {
            Call_JS::alerta($this->msg['singular'] . ' por serviço excluidas com sucesso !');
            Call_JS::retornar(URL . 'dashboard/Reports/' . $this->page);
        }
    }

    /**
     * Delete comission per client
     * @access private
     * @return void
     */
    private function DeletePerClient() {
        $criter = self::CriterFilter();
        $criter['id_client'] = Url::getURL($this->URL_ACTION + 2);
        $q = new Query();
        $q
                ->delete($this->table)
                ->where_equal_to($criter)
                ->run();
        if ($q) {
            Call_JS::alerta($this->msg['singular'] . ' por cliente excluidas com sucesso !');
            Call_JS::retornar(URL . 'dashboard/Reports/' . $this->page);
        }
    }

    /**
     * checks will delete and which will delete
     * @access protected
     * @return void
     */
    protected function verify_del() {
        $param = Url::getURL($this->URL_ACTION + 1);
        if (isset($param)) {
            switch ($param) {
                case 'DeletePerUser':
                    $this->DeletePerUser();
                    break;
                case 'DeletePerClient':
                    $this->DeletePerClient();
                    break;
                case 'DeletePerService':
                    $this->DeletePerService();
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * checks will score and what was called
     * @access private
     * @return void
     */
    private function verify_mark() {
        $param = Url::getURL($this->URL_ACTION + 1);
        if (isset($param)) {
            switch ($param) {
                case 'MarkPerUser':
                    $this->MarkComissionPerUser();
                    break;
                case 'MarkPerClient':
                    $this->DeletePerClient();
                    break;
                case 'MarkPerService':
                    $this->DeletePerService();
                    break;
                default:
                    break;
            }
        }
    }

}
