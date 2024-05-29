<?php

include("../application/config/config.php");
include("../vendor/phpmailer/phpmailer/PHPMailerAutoload.php");
include("../vendor/offboard/Class-Query/autoload.php");
include("../vendor/kriansa/openboleto/autoloader.php");
include("../application/libs/Func.php");
include("../application/libs/Developer/phpToPDF.php");
include("Billet/GenerateBillet.php");

class Billet extends GenerateBillet {

    /**
     * Format to timestamp
     * remove time
     * 
     * @access public
     * @var string 
     */
    var $format = 'Y-m-d';

    /**
     * Magic Metthod
     * 
     * checks whether it is time to send the message
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        $q = new Query();
        $q
                ->select()
                ->from($this->table)
                ->where_equal_to(
                        array(
                            'DATE(data_send) = DATE(NOW())'
                        )
                )
                ->run();

        $total = $q->get_selected_count();
        $data = $q->get_selected();

        if (!($data && $total > 0)) {
            
            return false;
        }

        foreach ($data as $row) {

            parent::__construct($row);
            $this->isAuthOrNot($row);
        }
    }

    /**
     * is the authentication mode?
     * 
     * @access private
     * @param array $data Query Result
     * @return void
     */
    private function isAuthOrNot(array $data) {
        if (!MAIL_AUTH) {
            $this->sendWithoutAuth($data);
        } else {
            $this->sendWithAuth($data);
        }
    }

    /**
     * Sends the email with authentication
     * 
     * @access private
     * @param array $data Query Result
     * @return boolean
     */
    private function sendWithAuth(array $data) {

        $address = Func::array_table('clientes', array('id' => $data['id_client']), 'Email');
        $name = Func::array_table('clientes', array('id' => $data['id_client']), 'nome');

        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        //$mail->SMTPDebug = 3;                      // Enable verbose debug output
        $mail->isSMTP();                             // Set mailer to use SMTP
        $mail->Host = MAIL_SMTP;                     // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                      // Enable SMTP authentication
        $mail->Username = MAIL_USER;                 // SMTP username
        $mail->Password = MAIL_PASS;                 // SMTP password
        $mail->SMTPSecure = MAIL_SMTP_SECURE;        // Enable TLS encryption, `ssl` also accepted
        $mail->Port = MAIL_PORT;                     // TCP port to connect to

        $mail->From = MAIL_USER;
        $mail->FromName = WEB_SITE_CEO_NAME;
        $mail->addAddress($address, $name);     // Add a recipient
        // checks CC are activate
        if (MAIL_CC) {
            $mail->addCC(MAIL_CC);
        }
        // checks BCC are activate
        if (MAIL_BCC) {
            $mail->addBCC(MAIL_BCC);
        }
        $mail->addAttachment(__DIR__ . '/pdf/' . $this->dir_billet);    // Add attachments Optional name
        $mail->addEmbeddedImage("lol.pdf","boleto");
        $mail->isHTML(MAIL_HTML);

        $mail->Subject = sprintf("%s - Cobrança #%d", WEB_SITE_CEO_NAME, $data['id_receipt']);

        if (MAIL_HTML) {
            $mail->Body = MAIL_TOP_SIGNATURE;
            $mail->Body.= $this->generateOutstandingInstallments($data);
            $mail->Body.= $this->billet_html;
            $mail->Body.= MAIL_BUTTON_SIGNATURE;
        } else {
            $mail->Body.= strip_tags($this->generateOutstandingInstallments($data));
        }

        if (!$mail->send()) {
            echo 'Messagem não pode ser enviada !' . $mail->ErrorInfo;
            error_log('Mailer Error: ' . $mail->ErrorInfo);

            return false;
        } else {
            
            return true;
        }
    }

    /**
     * Sends the email without authentication
     * 
     * @access private
     * @param array $data Query Result
     * @return boolean
     */
    private function sendWithoutAuth(array $data) {
        $address = Func::array_table('clientes', array('id' => $data['id_client']), 'Email');
        $name = Func::array_table('clientes', array('id' => $data['id_client']), 'nome');

        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 2;
        //Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $mail->Host = MAIL_SMTP;
        //Set the SMTP port number - likely to be 25, 465 or 587
        $mail->Port = MAIL_PORT;
        //Whether to use SMTP authentication
        $mail->SMTPAuth = false;
        //Set who the message is to be sent from
        $mail->setFrom(MAIL_USER, WEB_SITE_CEO_NAME);
        //Set who the message is to be sent to
        $mail->addAddress($address, $name);
        //Set the subject line
        $mail->Subject = sprintf("%s - Cobrança #%d", WEB_SITE_CEO_NAME, $data['id_receipt']);

        if (MAIL_HTML) {
            $mail->Body = MAIL_TOP_SIGNATURE;
            $mail->Body.= $this->generateOutstandingInstallments($data);
            $mail->Body.= MAIL_BUTTON_SIGNATURE;
        } else {
            $mail->Body.= strip_tags($this->generateOutstandingInstallments($data));
        }

        //Attach an image file
        $mail->addAttachment(__DIR__ . '/pdf/' . $this->dir_billet, 'Boleto');
        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            error_log('Mailer Error: ' . $mail->ErrorInfo);
        } else {
            return true;
        }
    }

}

new Billet();
