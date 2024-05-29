<?php

class Visitor extends RemoteAddress {

    /**
     * Expire time limite in days
     * @var Integer
     */
    var $limit = 1;

    /**
     * Database
     * @var String 
     */
    var $table = 'visitas';

    /**
     * Hash sha1 of session id
     * @var mixed 
     */
    var $Session_hash = NULL;

    /**
     * Contruct Method
     * @return Void
     */
    public function __construct() {
        $this->Generate_Hash();
        $this->InsertOnDatabase();
    }

    /**
     * Check seassion is initiated
     * @return boolean
     */
    private function is_session_started() {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Insert new visit
     * @return Void
     */
    private function InsertOnDatabase() {
        if ($this->CheckInDataBase() !== false) {
            $q = new Query;
            $q
                    ->insert_into(
                            $this->table, array(
                        'HashSession' => $this->Session_hash,
                        'UseAgent' => $_SERVER['HTTP_USER_AGENT'],
                        'Address' => $this->getIpAddress(),
                        'proxy' => $this->useProxy
                            )
                    )
                    ->run();
        }
    }

    /**
     * Get current date and add x days
     * @return Timestamp
     */
    private function _get_date_for_criter() {
        $date = date('Y-m-d H:i:s');
        $xmasDay = new DateTime("$date + $this->limit day");
        return $xmasDay->format('Y-m-d H:i:s');
    }

    /**
     * Check uniq visit
     * @return Boolean
     */
    private function CheckInDataBase() {
        $q = new Query;
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'HashSession' => $this->Session_hash,
                            'Address' => $this->getIpAddress()
                        )
                )
                ->where_less_than_or_equal_to(
                        array(
                            'date_history' => $this->_get_date_for_criter()
                        )
                )
                ->run();
        return !($q->get_selected_count() > 0) ? true : false;
    }

    /**
     * Generate Hash from id session
     * @return Void
     */
    protected function Generate_Hash() {
        if (!isset($this->Session_hash)) {
            if ($this->is_session_started() !== true) {
                session_start();
            }
            $this->Session_hash = sha1(session_id());
        }
    }

}
