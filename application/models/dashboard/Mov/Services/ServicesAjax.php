<?php

header('Content-type: application/json');
require '../../../../config/config.php';


if (is_ajax()) {
    // we do negative-first checks here
    if (!isset($_POST['user_name']) OR empty($_POST['user_name'])) {
        $response_array = array(
            'status' => 'error',
            'msg' => FEEDBACK_USERNAME_FIELD_EMPTY
        );
        echo json_encode($response_array);
        die();
    }
    if (!isset($_POST['user_password']) OR empty($_POST['user_password'])) {
        $response_array = array(
            'status' => 'error',
            'msg' => FEEDBACK_PASSWORD_FIELD_EMPTY
        );
        echo json_encode($response_array);
        die();
    }

    echo is_allowed();
    die();
}

//Function to check if the request is an AJAX request
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * makes all magic needed for that problem
 * @return array
 */
function is_allowed() {
    $login = $_POST['user_name'];
    $pass = $_POST['user_password'];

    $json = <<<INFO
<div class="form-group" id="div-discount">
    <label class="control-label col-md-4">Desconto %<span id="field-required">*</span></label>
       <div class="col-md-6">
           <div class="input-group">
                <input name="discount" id='discount' value="0" type="text" class="form-control" data-mask="99?.99">  
                <span class="input-group-addon ">%</span>    
            </div>
     </div>
</div>
INFO;
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset('utf8');
    $query = "SELECT * FROM `users` WHERE `user_name`='$login' LIMIT 1";
    $q = mysqli_query($mysqli, $query);
    $total = mysqli_num_rows($q);
    // not found
    if ($total != 1) {
        $response_array = array(
            'status' => 'error',
            'msg' => FEEDBACK_LOGIN_FAILED . $query,
            'json' => NULL
        );
    } else {
        while ($value = mysqli_fetch_array($q)) {
            // check if hash of provided password matches the hash in the database
            if (password_verify($pass, $value['user_password_hash'])) {
                $account_type = $value['user_account_type'];
                // is privileged ?
                if ($account_type == 0 || $account_type == 1) {
                    $response_array = array(
                        'status' => 'success',
                        'msg' => 'Desconto destravado com sucesso ! insira o valor do desconto !',
                        'json' => $json
                    );
                } else {
                    // unprivileged
                    $response_array = array(
                        'status' => 'error',
                        'msg' => 'Usuário não tem privilégios o suficiente para dar descontos !',
                        'json' => NULL
                    );
                }
            } else {
                // password wrong
                $response_array = array(
                    'status' => 'error',
                    'msg' => FEEDBACK_PASSWORD_WRONG,
                    'json' => NULL
                );
            }
        }
    }

    return json_encode($response_array);
}
