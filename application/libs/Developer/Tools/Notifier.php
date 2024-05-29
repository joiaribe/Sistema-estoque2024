<?php

namespace Developer\Tools;

use Query as Query;
use Session as Session;

class Notifier {

    /**
     * Insert new Notifier by id
     * @param Str $Title Title notifier
     * @param Str $Text Text notifier
     * @param Int $IdUser Id user, use NULL for get id own
     * @param Int $IdFrom Id from
     * @return Boolean
     */
    public function InsertNotifierById($Title, $Text, $IdUser = NULL, $IdFrom = NULL) {
        $IdUser = ($IdUser == NULL) ? Session::get('user_id') : $IdUser;
        $q = new Query;
        $q
                ->insert_into(
                        'notifier', array(
                    'id_from' => $IdFrom,
                    'id_to' => $IdUser,
                    'title' => $Title,
                    'text' => $Text,
                        )
                )
                ->run();
        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Insert new Notifier by account type
     * @param Str $Title Title notifier
     * @param Str $Text Text notifier
     * @param Int $AccountType Account type
     * @param Int $IdFrom Id from
     * @return Boolean
     */
    public function InsertNotifierByAccountType($Title, $Text, $AccountType, $IdFrom = NULL) {
        $q = new Query;
        $q
                ->select()
                ->from('users')
                ->where_equal_to(
                        array(
                            'user_account_type' => $AccountType
                        )
                )
                ->run();
        $data = $q->get_selected();
        $count = $q->get_selected_count();
        if (!($data && $count > 0)) {
            return false;
        } else {
            foreach ($data as $dados) {
                // Insert By Id
                $this->InsertNotifierById($Title, $Text, $dados['user_id'], $IdFrom);
            }
            return true;
        }
    }

}
