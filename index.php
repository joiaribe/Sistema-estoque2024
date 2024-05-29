<?php

/**
 * Sistema de Controle de Estoque e Finanças, 
 * Atenção todos os comentários dos códigos são escritos em inglês já que é o idioma padrão de TI.
 * siga as instruções para a instalação e não compare esse sistema profissional com scripts vagabundos de 50 reais.
 *
 * A simple, clean and secure PHP Login Script embedded into a small framework.
 * Also available in other versions: one-file, minimal, advanced. See php-login.net for more info.
 *
 * MVC FRAMEWORK VERSION
 *
 * @author Panique
 * @author Offboard <offboard@hotmail.com>
 * @link http://www.php-login.net/
 * @link https://github.com/panique/php-login/
 * @license http://opensource.org/licenses/MIT MIT License
 */
final class main {

    /**
     * Files essential for the functioning of the system, and the way message error
     * @var Array 
     */
    var $REQUIRED_FILES = array(
        // Load application config (error reporting, database credentials etc.)
        'Settings' => array(
            'DIR' => 'application/config/config.php',
            'MESSAGE' => 'Error: The essential settings file not found !'
        ),
        // The auto-loader to load the php-login related internal stuff automatically
        'Autoload' => array(
            'DIR' => 'application/config/autoload.php',
            'MESSAGE' => 'Error: The autoload file was not found !'
        ),
        // The Composer auto-loader (official way to load Composer contents) to load external stuff automatically
        'Composer' => array(
            'DIR' => 'vendor/autoload.php',
            'MESSAGE' => 'Failed installation error: The Composer autoload was not found or does not exist !'
        )
    );

    /**
     * Magic Method initiates the Framework 
     * @return Void
     */
    public function __construct() {
        $this->Check_Version_PHP_Required();
        if ($this->Load_Required_Files()) {
            $this->Load_Libs();
            if (class_exists('Application')) {
                new Application();
            }
        }
    }

    /**
     * Load Libs Folder
     * @return void
     */
    private function Load_Libs() {
        $autoloadManager = new autoloadManager(null, autoloadManager::SCAN_ONCE);
        $autoloadManager->addFolder("vendor/");
        $autoloadManager->addFolder(LIBS_PATH);
        $autoloadManager->register();
    }

    /**
     * Load required files
     * @return boolean
     */
    private function Load_Required_Files() {
        foreach ($this->REQUIRED_FILES as $value) {
            if (($this->Check_Files_is_Loaded($value['DIR'], $value['MESSAGE']) == false)) {
                $result = false;
            }
            $result = true;
        }
        return $result;
    }

    /**
     * Force to display erros if are debugged mode
     * @return Void
     */
    private function Check_Display_Erros() {
        if (WEB_SITE_DEBUG || SYSTEM_DEBUG) {
            ini_set('display_errors', 1);
            ini_set('display_startup_erros', 1);
            error_reporting(E_ALL);
        }
    }

    /**
     * Checks is called the essential files
     * @param String $dir Path file
     * @param String $message Message to display
     * @return boolean
     * @throws Exception
     */
    private function Check_Files_is_Loaded($dir, $message) {
        if (file_exists($dir)) {
            require(__DIR__ . DIRECTORY_SEPARATOR . $dir);
        } else {
            if (SYSTEM_DEBUG == FALSE) {
                die($message);
            } else {
                throw new Exception(printf($message . ' in folder : %s'), $dir);
            }
            return false;
        }
    }

    /**
     * Checks minimum PHP required version 
     * @param float $min Version min PHP required
     * @return boolean
     */
    private function Check_Version_PHP_Required($min = "5.3.7") {
        $message = 'Sorry, this script does not run on a PHP version smaller than PHP ' . $min . ' !';
        if (version_compare(PHP_VERSION, $min, '<')) {
            die($message);
            return false;
        }
        return true;
    }

}

// check config file for install application
$CONFIG_FILE_REQUIRED = 'application/config/config.php';
if (!file_exists($CONFIG_FILE_REQUIRED)) {
    header('location: application/_installation/setup-config.php');
    exit();
}


// start application
new main();

