<?php

header('Content-type: application/json');
require '../../../../config/config.php';
require '../../../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

if (is_ajax()) {
    // checks fields are empty
    echo is_allowed();
    die();
}

//Function to check if the request is an AJAX request
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * checks empty fields
 * @return void
 */
function checks_fields() {
    if (empty($_POST['smtp'])) {
        $response_array = array(
            'status' => 'error',
            'msg' => 'Por favor insira um endereço smtp !'
        );
        echo json_encode($response_array);
        die();
    }

    if (empty($_POST['email'])) {
        $response_array = array(
            'status' => 'error',
            'msg' => 'Por favor insira um endereço de email !'
        );
        echo json_encode($response_array);
        die();
    }

    if (empty($_POST['password'])) {
        $response_array = array(
            'status' => 'error',
            'msg' => 'Por favor insira uma senha !'
        );
        echo json_encode($response_array);
        die();
    }


    if (empty($_POST['port'])) {
        $response_array = array(
            'status' => 'error',
            'msg' => 'Por favor insira a porta usado pelo endereço smtp !'
        );
        echo json_encode($response_array);
        die();
    }
}

/**
 * makes all magic needed for that problem
 * @return array
 */
function is_allowed() {
    checks_fields();

    //Create a new SMTP instance
    $smtp = new SMTP();

    //Enable connection-level debug output
    #$smtp->do_debug = SMTP::DEBUG_CONNECTION;
    $smtp->SMTPSecure = $_POST['cripter']; // tls or ssl

    try {
        //Connect to an SMTP server
        if ($smtp->connect($_POST['smtp'], $_POST['port'])) {
            //Say hello
            if ($smtp->hello('localhost')) { //Put your host name in here
                //Before authenticate
                $smtp->startTLS();
                
                //Authenticate
                if ($smtp->authenticate($_POST['email'], $_POST['password'])) {
                    $response_array = array(
                        'status' => 'success',
                        'msg' => 'Conexão estabelecida com sucesso !'
                    );
                } else {
                    $response_array = array(
                        'status' => 'error',
                        'msg' => 'Falha na autenticação: ' . $smtp->getLastReply()
                    );
                    #throw new Exception('Falha na autenticação: ' . $smtp->getLastReply());
                }
            } else {
                $response_array = array(
                    'status' => 'error',
                    'msg' => 'HELO failed: ' . $smtp->getLastReply()
                );
                #throw new Exception('HELO failed: ' . $smtp->getLastReply());
            }
        } else {
            $response_array = array(
                'status' => 'error',
                'msg' => 'Conecte falhou '
            );
            #throw new Exception('Conecte falhou');
        }
    } catch (Exception $e) {
        $response_array = array(
            'status' => 'error',
            'msg' => 'SMTP erro : ' . $e->getMessage(), "\n"
        );
    }


    //Whatever happened, close the connection.
    $smtp->quit(true);


    return json_encode($response_array);
}
