<?php

/**
 * HTMLs configuration pages
 * @author Bruno Ribeiro <bruno.espertinho@gmail.com>
 * @version 1.1
 * @access public
 * @package Config
 * @todo Improve the customization of the configuration file
 * */
class SetupConfigHTML {

    /**
     * file config required for start the website
     * Note: at the time of verification and creating the configuration file, is always 
     * in the root folder, then the file is in a folder specify as follows: "config/config.inc.php"
     * @var string 
     */
    public $file = 'application/config/config.php';

    /**
     * Name your project
     * @var str 
     */
    var $name = 'Sistema Financeiro para salão de beleza';

    /**
     * Name SQL file
     * @var String
     */
    var $sql = 'Salao.sql';

    /**
     * link your project
     * @var str 
     */
    var $link = 'http://example.com';

    /**
     * logo your project
     * @var str 
     */
    var $logo = 'images/w-logo-blue.png';

    /**
     * Rules for replacement
     * @var array
     */
    var $Rules = array("&lt;" => "<", "&gt;" => ">", "&quot;" => '"', "&apos;" => "'", "&amp;" => "&");

    /**
     * Metthod Magic
     * Require language file
     */
    public function __construct() {
        include('Language/pt-br.php');
        if (!defined('LOGO')) {
            die('error');
        }
    }

    protected function makehtaccess($POST) {
        $RewriteBase = parse_url($POST['url'], PHP_URL_PATH);
        return <<<EOF
# Necessary to prevent problems when using a controller named "index" and having a root index.php
# more here: http://stackoverflow.com/q/20918746/1114320
Options -MultiViews

# turn rewriting on
RewriteEngine On

# When using the script within a sub-folder, put this path here, like /mysubfolder/
# If your app is in the root of your web folder, then please delete this line or comment it out
RewriteBase {$RewriteBase}

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-l

RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
EOF;
    }

    protected function MakeSQLFile($POST) {
        $name = $POST['name'];
        return <<<EOF
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda`
--

CREATE TABLE IF NOT EXISTS `agenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `titulo` varchar(250) DEFAULT NULL,
  `description` mediumtext,
  `horario` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `horario_end` datetime DEFAULT NULL COMMENT 'Termínio',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ChangeLog`
--

CREATE TABLE IF NOT EXISTS `ChangeLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_version` int(11) NOT NULL,
  `id_bug` int(11) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `descri` varchar(250) DEFAULT NULL,
  `data_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'no show',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=74 ;

--
-- Fazendo dump de dados para tabela `ChangeLog`
--

INSERT INTO `ChangeLog` (`id`, `id_version`, `id_bug`, `title`, `descri`, `data_created`) VALUES
(1, 1, NULL, 'added: Comissão de Usuário', NULL, '2014-10-02 22:21:35'),
(2, 2, NULL, 'added: Filtros para pesquisa', NULL, '2014-10-02 22:22:35'),
(3, 2, NULL, 'added: Lembretes', NULL, '2014-10-02 22:22:35'),
(4, 3, NULL, 'fix: API correios', NULL, '2014-10-02 22:24:11'),
(5, 3, NULL, 'update: Filtros avançados', NULL, '2014-10-02 22:24:11'),
(6, 3, NULL, 'update: 3 novos Gráficos', NULL, '2014-10-02 22:24:11'),
(7, 3, NULL, 'added: Chat Global', NULL, '2014-10-02 22:24:11'),
(8, 3, NULL, 'added: Controle de Serviço', NULL, '2014-10-02 22:24:11'),
(9, 3, NULL, 'added: Controle de Estoque', NULL, '2014-10-02 22:24:41'),
(10, 3, NULL, 'added: Comissão de Usuário', NULL, '2014-10-02 22:24:41'),
(11, 4, NULL, 'fix: Query Security', NULL, '2014-10-02 22:25:12'),
(12, 4, NULL, 'update: Query Mysqli', NULL, '2014-10-02 22:25:12'),
(13, 5, NULL, 'added: Aniversário', NULL, '2014-10-02 22:25:50'),
(14, 5, NULL, 'added: Checkout', NULL, '2014-10-02 22:25:50'),
(15, 5, NULL, 'added: Carrinho de Compras', NULL, '2014-10-02 22:25:50'),
(16, 6, NULL, 'fix: Checkout', NULL, '2014-10-02 22:27:39'),
(17, 6, NULL, 'fix: Vendas', NULL, '2014-10-02 22:27:39'),
(18, 6, NULL, 'update: Aniversário', NULL, '2014-10-02 22:27:39'),
(19, 6, NULL, 'update: Alt Clientes', NULL, '2014-10-02 22:27:39'),
(20, 6, NULL, 'update: Add Clientes', NULL, '2014-10-02 22:27:39'),
(21, 6, NULL, 'update: Query Clientes', NULL, '2014-10-02 22:27:39'),
(22, 6, NULL, 'update: Query Contas', NULL, '2014-10-02 22:27:39'),
(23, 6, NULL, 'added: Query Recibos Itens', NULL, '2014-10-02 22:27:39'),
(24, 6, NULL, 'added: Query Recibos', NULL, '2014-10-02 22:27:39'),
(25, 6, NULL, 'added: Tool Recibos', NULL, '2014-10-02 22:27:39'),
(41, 7, NULL, 'fix: Login', NULL, '2014-10-02 22:30:00'),
(42, 7, NULL, 'fix: Menu', NULL, '2014-10-02 22:30:00'),
(43, 7, NULL, 'fix: Principal', NULL, '2014-10-02 22:30:00'),
(44, 7, NULL, 'fix: Relatório Financeiro', NULL, '2014-10-02 22:30:00'),
(46, 8, NULL, 'fix: Detalhes Funcionário', NULL, '2014-10-02 22:31:14'),
(47, 8, NULL, 'fix: Recibos', NULL, '2014-10-02 22:31:14'),
(48, 8, NULL, 'fix: Finanças Produtos', NULL, '2014-10-02 22:31:29'),
(49, 8, NULL, 'fix: Relatório Financeiro', NULL, '2014-10-02 22:31:29'),
(50, 9, NULL, 'update: Layout Responsivo', NULL, '2014-10-02 22:53:57'),
(51, 9, NULL, 'added: Agendamento de Clientes', NULL, '2014-10-02 22:53:57'),
(52, 9, NULL, 'added: Calendário', NULL, '2014-10-02 22:53:57'),
(54, 9, NULL, 'added: Largura de Banda', NULL, '2014-10-02 22:53:57'),
(55, 9, NULL, 'fix: Comissões', NULL, '2014-10-02 22:53:57'),
(56, 9, NULL, 'added: Permissões de cargos', NULL, '2014-10-02 22:53:57'),
(57, 9, NULL, 'added: Suspender', NULL, '2014-10-02 22:53:57'),
(58, 9, NULL, 'fix: Lembretes', NULL, '2014-10-02 22:53:57'),
(59, 9, NULL, 'update: database structure', NULL, '2014-10-02 22:53:57'),
(60, 9, NULL, 'added: Instalação Fácil', NULL, '2014-10-02 22:53:57'),
(61, 9, NULL, 'added: Gravatar system', NULL, '2014-10-02 22:53:57'),
(62, 9, NULL, 'added: Configurações Globais', NULL, '2014-10-02 22:53:57'),
(64, 9, NULL, 'fix: Notas', NULL, '2014-10-04 03:00:00'),
(65, 9, NULL, 'added: Notificações', NULL, '2014-10-04 03:00:00'),
(67, 10, NULL, 'Versão Inicial', NULL, '2014-10-02 22:27:39'),
(68, 9, NULL, 'added: Configurações Menu', NULL, '2014-10-07 19:27:19'),
(69, 9, NULL, 'added: Configurações Acesso', NULL, '2014-10-07 19:27:35'),
(70, 9, NULL, 'update: Global Chat', NULL, '2014-10-07 19:30:05'),
(71, 9, NULL, 'update: Usuários', NULL, '2014-10-07 19:30:46'),
(72, 9, NULL, 'update: Changerlog', NULL, '2014-10-07 19:31:52'),
(73, 9, NULL, 'update: Gráficos', NULL, '2014-10-07 19:32:53'),
(74, 11, NULL, 'fix: Recibos', NULL, '2014-11-10 02:46:07'),
(75, 11, NULL, 'update: Acessos', NULL, '2014-11-10 02:46:34'),
(76, 11, NULL, 'fix: Menu', NULL, '2014-11-10 02:47:00'),
(77, 11, NULL, 'fix: Principal', NULL, '2014-11-10 05:04:22'),
(78, 11, NULL, 'fix: Auto Instalador', NULL, '2014-11-10 05:29:53'),
(79, 12, NULL, 'added: Custos Fixos', NULL, '2014-11-23 21:06:21'),
(80, 12, NULL, 'added: Módulo Relatórios', NULL, '2014-11-23 21:06:50'),
(81, 12, NULL, 'fix: Adicionar Cliente', NULL, '2014-11-23 21:07:18'),
(82, 12, NULL, 'update: Movimentar Serviços', NULL, '2014-11-23 21:07:53'),
(83, 12, NULL, 'update: Movimentar Produtos', NULL, '2014-11-23 21:08:03'),
(84, 12, NULL, 'update: Configuração Globais', NULL, '2014-11-23 21:08:43'),
(85, 12, NULL, 'update: Auto Instalador', NULL, '2014-11-23 21:09:37'),
(86, 12, NULL, 'update: Recibos', NULL, '2014-11-23 21:10:16'),
(88, 12, NULL, 'update: composer.json', NULL, '2014-11-24 01:52:25'),
(89, 12, NULL, 'fix: Ferramentas Access', NULL, '2014-11-24 01:53:40'),
(90, 12, NULL, 'fix: Notificações', NULL, '2014-11-24 01:55:21'),
(91, 12, NULL, 'fix: Mensagens', NULL, '2014-11-24 01:55:29'),
(92, 12, NULL, 'update: Gráficos Finanças', NULL, '2014-11-24 02:33:49'),
(93, 12, NULL, 'added: Fluxo de Caixa', NULL, '2014-11-26 22:01:52'),
(94, 12, NULL, 'added: Fontes', NULL, '2014-11-26 22:04:06'),
(95, 12, NULL, 'fix: Recibos', NULL, '2014-12-03 21:44:56'),
(96, 12, NULL, 'fix: Checkout', NULL, '2014-12-10 17:24:38'),
(97, 12, NULL, 'added: Galeria', NULL, '2014-12-13 20:26:36'),
(98, 12, NULL, 'fix: Global', NULL, '2014-12-14 02:35:38'),
(99, 12, NULL, 'fix: Estoque', NULL, '2014-12-18 20:48:15'),
(100, 12, NULL, 'fix: Funcionários', NULL, '2014-12-18 20:50:48'),
(101, 12, NULL, 'fix: Serviços', NULL, '2014-12-18 20:53:08'),
(102, 12, NULL, 'fix: Usuários', NULL, '2014-12-18 20:56:24'),
(103, 12, NULL, 'fix: Notificações Barra', NULL, '2014-12-18 21:09:48'),
(104, 12, NULL, 'added: Configuração Autenticação', NULL, '2014-12-18 21:10:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ChangeLog_Version`
--

CREATE TABLE IF NOT EXISTS `ChangeLog_Version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(10) NOT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Fazendo dump de dados para tabela `ChangeLog_Version`
--

INSERT INTO `ChangeLog_Version` (`id`, `version`, `subtitle`, `data`) VALUES
(1, '1.1', 'Beta', '2013-04-26 22:06:21'),
(2, '2.1', NULL, '2013-05-26 22:06:41'),
(3, '3.0', 'Release', '2013-05-28 22:07:06'),
(4, '3.1', NULL, '2014-03-21 22:07:16'),
(5, '3.2', 'Beta', '2014-03-28 22:07:20'),
(6, '3.3', 'Beta', '2014-04-16 22:07:24'),
(7, '3.4', NULL, '2014-08-01 22:07:24'),
(8, '3.5', NULL, '2014-09-18 22:07:24'),
(9, '4.0', 'Release', '2014-10-02 22:07:36'),
(10, '1.0', NULL, '2013-01-03 22:06:21'),
(11, '4.1', NULL, '2014-11-10 02:45:24'),
(12, '4.2', 'Release', '2014-11-23 21:05:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `agenda` varchar(15) DEFAULT NULL,
  `agenda_cor` varchar(10) NOT NULL DEFAULT '#000000',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `End` varchar(100) DEFAULT NULL,
  `Num` varchar(5) DEFAULT NULL,
  `Bairro` varchar(50) DEFAULT NULL,
  `Cidade` varchar(50) DEFAULT NULL,
  `UF` varchar(2) DEFAULT NULL,
  `Cep` varchar(9) DEFAULT NULL,
  `Fone` varchar(255) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Rg` varchar(22) DEFAULT NULL,
  `Cpf` varchar(14) DEFAULT NULL,
  `Aniversario` timestamp NULL DEFAULT NULL,
  `Sexo` enum('F','M','T') NOT NULL,
  `Indicacao` varchar(100) DEFAULT NULL,
  `Obs` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Nm` (`nome`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Configure`
--

CREATE TABLE IF NOT EXISTS `Configure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `DEBUG` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(250) NOT NULL,
  `LOGO` varchar(250) NOT NULL,
  `FAVOICON` varchar(250) NOT NULL,
  `INTERFACE` varchar(250) NOT NULL DEFAULT 'eth0',
  `NAME` varchar(100) NOT NULL,
  `COOKIE_RUNTIME` int(11) NOT NULL,
  `COOKIE_DOMAIN` varchar(50) NOT NULL DEFAULT 'localhost',
  `ACCOUNT_TYPE_FOR_SALLER` int(11) NOT NULL,
  `DAY_CLOSE_COMISSION` int(11) DEFAULT NULL,
  `STATUS_DAY_CLOSE` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Fazendo dump de dados para tabela `Configure`
--

INSERT INTO `Configure` (`id`, `DEBUG`, `url`, `LOGO`, `FAVOICON`, `INTERFACE`, `NAME`, `COOKIE_RUNTIME`,  `COOKIE_DOMAIN`, `ACCOUNT_TYPE_FOR_SALLER`, `DAY_CLOSE_COMISSION`, `STATUS_DAY_CLOSE`) VALUES
(1, 0, '{$POST['url']}', 'logo.png', 'favicon.ico', 'eth1', '{$POST['name']}', 1209600, '{$POST['coockie_domain']}', 1, 10, 1);


-- --------------------------------------------------------

--
-- Estrutura para tabela `ConfigureFonts`
--

CREATE TABLE IF NOT EXISTS `ConfigureFonts` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `banco` enum('Banco Do Brasil','Bradesco','Caixa','Itau','Santander') NOT NULL,
  `agencia` int(4) NOT NULL,
  `conta` int(8) NOT NULL,
  `Convenio` int(11) DEFAULT NULL,
  `carteira` varchar(20) DEFAULT NULL,
  `codigoCliente` int(5) DEFAULT NULL,
  `numeroDocumento` int(7) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Fazendo dump de dados para tabela `ConfigureFonts`
--

INSERT INTO `ConfigureFonts` (`Id`, `titulo`, `banco`, `agencia`, `conta`, `Convenio`, `carteira`, `codigoCliente`, `numeroDocumento`, `data`) VALUES
(1, 'Conta Web Master', 'Caixa', 2392, 3334, NULL, 'SR', NULL, NULL, '2014-11-25 23:55:00');



-- --------------------------------------------------------

--
-- Estrutura para tabela `ConfigureInfos`
--

CREATE TABLE IF NOT EXISTS `ConfigureInfos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(250) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `Fone` varchar(50) DEFAULT NULL,
  `End` varchar(100) DEFAULT NULL,
  `Num` varchar(5) DEFAULT NULL,
  `Bairro` varchar(50) DEFAULT NULL,
  `Cidade` varchar(50) DEFAULT NULL,
  `UF` varchar(2) DEFAULT NULL,
  `Cep` varchar(9) DEFAULT NULL,
  `CNPJ` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Fazendo dump de dados para tabela `ConfigureInfos`
--

INSERT INTO `ConfigureInfos` (`id`, `logo`, `email`, `Fone`, `End`, `Num`, `Bairro`, `Cidade`, `UF`, `Cep`, `CNPJ`) VALUES
(1, 'bucket-logo.png', 'bruno.espertinho@gmail.com', '(82) 3651-2816', 'Rua Doutor Abelardo de Barros', '200', 'Tijuca', 'Rio de Janeiro', 'RJ', '20521030', '48.274.445/7437-55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ConfigureMail`
--

CREATE TABLE IF NOT EXISTS `ConfigureMail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `AUTH` tinyint(1) NOT NULL DEFAULT '0',
  `SMTP` varchar(20) DEFAULT NULL,
  `SMTP_SECURE` enum('tls','ssl') DEFAULT NULL,
  `USER` varchar(50) DEFAULT NULL,
  `PASS` varchar(20) DEFAULT NULL,
  `PORT` int(4) NOT NULL DEFAULT '587',
  `CC` varchar(50) DEFAULT NULL,
  `BCC` varchar(50) DEFAULT NULL,
  `HTML` tinyint(1) NOT NULL DEFAULT '1',
  `BUTTON_SIGNATURE` mediumtext,
  `TOP_SIGNATURE` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Fazendo dump de dados para tabela `ConfigureMail`
--

INSERT INTO `ConfigureMail` (`id`, `AUTH`, `SMTP`, `SMTP_SECURE`, `USER`, `PASS`, `PORT`, `CC`, `BCC`, `HTML`, `BUTTON_SIGNATURE`, `TOP_SIGNATURE`) VALUES
(1, 0, NULL, 'tls', NULL, NULL, 587, '', '', 1, 'Senado Federal - Praça dos Três Poderes - Brasília DF - CEP 70165-900 - Fone: (61)6666-6666', '<img src="https://ci5.googleusercontent.com/proxy/eHfqKidrRMZJ7DHhentABTu1RKQSLxMrt7HAzR_UArgre9dbqSYUK08W6ZLe0VI4bF8H0OdFe8ihLsZMgtaxSRDbcCFhaw6s4rbfyY_AhYl_bIvkMxfJB-y_XKY=s0-d-e1-ft#http://www12.senado.gov.br/ecidadania/++resource++img/email-header.png" alt="">');
-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Estrutura para tabela `cron_input`
--

CREATE TABLE IF NOT EXISTS `cron_input` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(250) DEFAULT NULL,
  `card_number` varchar(250) DEFAULT NULL,
  `cheque_number` varchar(250) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `descri` mediumtext,
  `qnt` int(5) DEFAULT NULL,
  `value` double NOT NULL,
  `status` tinyint(1) NOT NULL,
  `cron_time` enum('monthly','weekly','daily') NOT NULL,
  `monthly_day` int(1) DEFAULT NULL,
  `weekly_day` int(1) DEFAULT NULL,
  `daily_hour` int(2) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cron_output`
--

CREATE TABLE IF NOT EXISTS `cron_output` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(250) DEFAULT NULL,
  `card_number` varchar(250) DEFAULT NULL,
  `cheque_number` varchar(250) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `descri` mediumtext,
  `value` double NOT NULL,
  `status` tinyint(1) NOT NULL,
  `cron_time` enum('monthly','weekly','daily') NOT NULL,
  `monthly_day` int(1) DEFAULT NULL,
  `weekly_day` int(1) DEFAULT NULL,
  `daily_hour` time DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Estrutura para tabela `fornecedores`
--

CREATE TABLE IF NOT EXISTS `fornecedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa` varchar(250) NOT NULL,
  `cpf` varchar(250) DEFAULT NULL,
  `cpnj` varchar(250) DEFAULT NULL,
  `email` varchar(250) NOT NULL,
  `fone` varchar(250) NOT NULL,
  `UF` varchar(2) DEFAULT NULL,
  `Cep` varchar(9) DEFAULT NULL,
  `End` varchar(100) DEFAULT NULL,
  `Num` varchar(5) DEFAULT NULL,
  `Bairro` varchar(50) DEFAULT NULL,
  `Cidade` varchar(50) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE IF NOT EXISTS `funcionarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `Salario` double DEFAULT NULL,
  `Sexo` enum('F','M','T') NOT NULL,
  `DtNasc` varchar(20) DEFAULT NULL,
  `CPF` varchar(100) DEFAULT NULL,
  `RG` varchar(100) DEFAULT NULL,
  `End` varchar(100) DEFAULT NULL,
  `Num` varchar(5) DEFAULT NULL,
  `Bairro` varchar(50) DEFAULT NULL,
  `Cidade` varchar(50) DEFAULT NULL,
  `UF` varchar(2) DEFAULT NULL,
  `Cep` varchar(100) DEFAULT NULL,
  `Tel` varchar(100) DEFAULT NULL,
  `Celular` varchar(100) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Nm` (`nome`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `gallery_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gallery_pic`
--

CREATE TABLE IF NOT EXISTS `gallery_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cat` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `pic` varchar(250) NOT NULL,
  `title` varchar(200) DEFAULT 'Sem título',
  `descri` mediumtext,
  `type` varchar(5) NOT NULL,
  `Resolution` varchar(10) NOT NULL,
  `size` int(20) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `global_chat`
--

CREATE TABLE IF NOT EXISTS `global_chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `msg` mediumtext NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `guia_solucao`
--

CREATE TABLE IF NOT EXISTS `guia_solucao` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `id_poster` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `style` enum('primary','success','info','inverse','default','danger') NOT NULL DEFAULT 'primary',
  `position` enum('left','right','center') NOT NULL,
  `titulo` varchar(240) NOT NULL,
  `texto` longtext NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='portal de dúvidas' AUTO_INCREMENT=7 ;

--
-- Fazendo dump de dados para tabela `guia_solucao`
--

INSERT INTO `guia_solucao` (`id`, `id_poster`, `status`, `style`, `position`, `titulo`, `texto`, `data`) VALUES
(2, NULL, 1, 'success', 'left', 'Não Consigo Adicionar Usuário ', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n<p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n<p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>', '2014-09-07 00:56:00'),
(3, NULL, 1, 'primary', 'left', 'Tem algum erro no meu sistema ', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>', '2014-09-07 00:56:00'),
(4, NULL, 1, 'success', 'right', 'Não Consigo Adicionar uma Foto ', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n<p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>', '2014-09-07 00:56:00'),
(5, NULL, 1, 'default', 'center', 'Quero relatar minhas sugestões ', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n<p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n<p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>', '2014-09-07 00:56:00'),
(6, NULL, 1, 'danger', 'right', 'Quero Formatar minha maquina ', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n<p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n<p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n<p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>\n                                <p>\n                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.\n                                </p>', '2014-09-07 00:56:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `input_others`
--

CREATE TABLE IF NOT EXISTS `input_others` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_cron` int(11) DEFAULT NULL,
  `cron` tinyint(1) NOT NULL DEFAULT '0',
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(250) DEFAULT NULL,
  `card_number` varchar(250) DEFAULT NULL,
  `cheque_number` varchar(250) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `descri` mediumtext,
  `value` double NOT NULL,
  `status` tinyint(1) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `input_product`
--

CREATE TABLE IF NOT EXISTS `input_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) DEFAULT NULL,
  `id_product` int(10) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_font` int(11) DEFAULT NULL,
  `id_receipt` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `descri` mediumtext,
  `qnt` int(3) NOT NULL DEFAULT '1',
  `discount` float DEFAULT NULL,
  `generate_billet` tinyint(1) NOT NULL DEFAULT '0',
  `installments` int(2) NOT NULL DEFAULT '1' COMMENT 'Payment installments',
  `payment_due` int(2) DEFAULT NULL COMMENT 'day of the month due for payment',
  `value` float DEFAULT NULL COMMENT 'atenção o valor deve está multiplicado',
  `status` tinyint(1) NOT NULL,
  `Payment_method` enum('a_vista','parcelado','entrada_e_parcelas','porcentagem_e_Parcelas') NOT NULL DEFAULT 'a_vista',
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(50) DEFAULT NULL,
  `card_number` varchar(100) DEFAULT NULL,
  `cheque_number` varchar(250) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='all products ever made' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Estrutura para tabela `input_product_plots`
--

CREATE TABLE IF NOT EXISTS `input_product_plots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_receipt` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `plot` int(2) NOT NULL,
  `plot_value` float NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Estrutura para tabela `input_servico`
--

CREATE TABLE IF NOT EXISTS `input_servico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) DEFAULT NULL,
  `id_service` int(10) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_employee` int(10) DEFAULT NULL,
  `id_font` int(11) DEFAULT NULL,
  `id_receipt` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `descri` mediumtext,
  `qnt` int(3) NOT NULL DEFAULT '1',
  `discount` float DEFAULT NULL,
  `generate_billet` tinyint(1) NOT NULL DEFAULT '0',
  `installments` int(2) DEFAULT '1' COMMENT 'Payment installments',
  `payment_due` int(2) DEFAULT NULL COMMENT 'day of the month due for payment',
  `value` float DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `Payment_method` enum('a_vista','parcelado','entrada_e_parcelas','porcentagem_e_Parcelas') NOT NULL DEFAULT 'a_vista',
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(50) DEFAULT NULL,
  `card_number` varchar(100) DEFAULT NULL,
  `cheque_number` varchar(250) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='all services ever made' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Estrutura para tabela `input_servico_plots`
--

CREATE TABLE IF NOT EXISTS `input_servico_plots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_receipt` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `plot` int(2) NOT NULL,
  `plot_value` float NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `marcador`
--

CREATE TABLE IF NOT EXISTS `marcador` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagem`
--

CREATE TABLE IF NOT EXISTS `mensagem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_from` int(11) DEFAULT NULL COMMENT 'ID do usuário remetente',
  `id_to` int(11) NOT NULL COMMENT 'ID do usuário destinário',
  `star` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = Não é favorito | 1 = Favorito',
  `important` tinyint(1) NOT NULL DEFAULT '0',
  `trash` tinyint(1) DEFAULT '0' COMMENT '0 = não está na lixeira | 1 = lixeira',
  `spam` tinyint(1) NOT NULL DEFAULT '0',
  `label` int(11) DEFAULT NULL,
  `title` varchar(250) NOT NULL,
  `text` mediumtext NOT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = não lida, 1 = lida',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='mensagens' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagem_attack`
--

CREATE TABLE IF NOT EXISTS `mensagem_attack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_message` int(11) NOT NULL,
  `size` varchar(11) NOT NULL,
  `file` varchar(250) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagem_reply`
--

CREATE TABLE IF NOT EXISTS `mensagem_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_message` int(11) NOT NULL,
  `text` longtext NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL,
  `sub` tinyint(1) DEFAULT '0' COMMENT 'id subcategoria ',
  `name` varchar(50) NOT NULL,
  `link` varchar(250) NOT NULL DEFAULT '#' COMMENT 'padrão #',
  `icone` varchar(250) NOT NULL COMMENT 'icone fa class',
  `position` int(10) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Fazendo dump de dados para tabela `menu`
--

INSERT INTO `menu` (`id`, `status`, `sub`, `name`, `link`, `icone`, `position`, `data`) VALUES
(1, 1, 0, 'Principal', 'dashboard/index', 'fa-home', 1, '2014-09-29 00:36:52'),
(2, 1, 1, 'Gráficos', '#', 'fa-bar-chart-o', 2, '2014-03-05 16:05:52'),
(3, 1, 1, 'Movimentar', '#', 'fa-external-link', 6, '2014-03-05 16:05:52'),
(5, 1, 1, 'Notificações', '#', 'fa-envelope', 8, '2014-03-05 16:05:52'),
(6, 1, 1, 'Ferramentas', '#', 'fa-suitcase', 9, '2014-03-05 16:05:52'),
(7, 1, 0, 'Guia do Usuário', 'dashboard/Guide/doubts', 'fa-question-circle', 10, '2014-03-05 16:05:52'),
(8, 0, 1, 'Sistema', '#', 'fa-user', 12, '2014-03-05 16:05:52'),
(11, 1, 1, 'Gerenciar', '#', 'fa-book', 5, '2014-03-05 16:05:52'),
(12, 1, 1, 'Configuração', '#', 'fa-gear', 13, '2013-11-05 16:05:52'),
(13, 1, 1, 'Custos Fixos', '#', 'fa-refresh', 8, '2014-10-03 02:11:58'),
(14, 1, 1, 'Relatórios', '#', 'fa-tasks', 8, '2014-10-03 02:11:58');


-- --------------------------------------------------------

--
-- Estrutura para tabela `menu_access`
--

CREATE TABLE IF NOT EXISTS `menu_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_type` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;

--
-- Fazendo dump de dados para tabela `menu_access`
--

INSERT INTO `menu_access` (`id`, `account_type`, `id_menu`, `data`) VALUES
(51, 1, 1, CURRENT_TIME),
(52, 1, 11, CURRENT_TIME),
(53, 1, 5, CURRENT_TIME),
(54, 1, 6, CURRENT_TIME),
(55, 1, 7, CURRENT_TIME),
(56, 1, 8, CURRENT_TIME),
(57, 2, 1, CURRENT_TIME),
(58, 2, 2, CURRENT_TIME),
(59, 2, 5, CURRENT_TIME),
(60, 2, 6, CURRENT_TIME),
(61, 2, 7, CURRENT_TIME),
(62, 2, 8, CURRENT_TIME),
(63, 3, 1, CURRENT_TIME),
(64, 3, 2, CURRENT_TIME),
(65, 3, 3, CURRENT_TIME),
(66, 3, 5, CURRENT_TIME),
(67, 3, 7, CURRENT_TIME),
(68, 3, 8, CURRENT_TIME),
(69, 4, 1, CURRENT_TIME),
(70, 4, 2, CURRENT_TIME),
(71, 4, 11, CURRENT_TIME),
(72, 4, 3, CURRENT_TIME),
(73, 4, 5, CURRENT_TIME),
(74, 4, 6, CURRENT_TIME),
(75, 4, 7, CURRENT_TIME),
(76, 4, 8, CURRENT_TIME);


-- --------------------------------------------------------

--
-- Estrutura para tabela `menu_sub`
--

CREATE TABLE IF NOT EXISTS `menu_sub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL COMMENT 'visible ?',
  `id_menu` int(11) DEFAULT NULL COMMENT 'id do menu',
  `name` varchar(50) NOT NULL,
  `link` varchar(250) NOT NULL DEFAULT '#' COMMENT 'padrão #',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data created',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Fazendo dump de dados para tabela `menu_sub`
--

INSERT INTO `menu_sub` (`id`, `status`, `id_menu`, `name`, `link`, `data`) VALUES
(2, 1, 13, 'Despesas', 'dashboard/OverheadCosts/expense', '2014-03-05 16:05:18'),
(3, 1, 2, 'Comissões', 'dashboard/Charts/comissions', '2014-03-05 16:05:18'),
(4, 1, 2, 'Finanças', 'dashboard/Charts/finance', '2014-03-05 16:05:18'),
(6, 1, 3, 'Vendas Serviços', 'dashboard/Mov/services', '2014-03-05 16:05:18'),
(7, 1, 3, 'Vendas Produtos', 'dashboard/Mov/products', '2014-03-05 16:05:18'),
(14, 1, 11, 'Usuários', 'dashboard/Manager/users', '2014-03-05 16:05:18'),
(15, 1, 5, 'Mensagem', 'dashboard/Notifier/inbox', '2014-03-05 16:05:18'),
(17, 1, 5, 'Notificações', 'dashboard/Notifier/notifier', '2014-03-05 16:05:18'),
(18, 1, 8, 'Sair', 'login/logout', '2014-03-05 16:05:18'),
(22, 1, 7, 'Portal de Dúvidas', 'dashboard/Guide/doubts', '2014-03-05 16:05:18'),
(25, 1, 8, 'Suspender', 'dashboard/System/lock_screen', '2014-03-05 16:05:18'),
(27, 1, 6, 'Agenda', 'dashboard/Tools/calendar', '2014-03-05 16:05:18'),
(32, 1, 6, 'Tempo Real Largura de Banda', 'dashboard/Tools/chart_bandwidth', '2014-03-05 16:05:18'),
(35, 1, 12, 'Globais', 'dashboard/Settings/global', '2014-03-05 16:05:18'),
(36, 1, 12, 'Menu', 'dashboard/Settings/Menu', '2014-03-05 16:05:18'),
(47, 1, 11, 'Clientes', 'dashboard/Manager/clients', '2014-09-20 03:00:00'),
(48, 1, 11, 'Fornecedores ', 'dashboard/Manager/fornecedores', '2014-09-20 03:00:00'),
(49, 1, 11, 'Estoque', 'dashboard/Manager/estoque', '2014-09-20 03:00:00'),
(50, 1, 11, 'Serviços', 'dashboard/Manager/servicos', '2014-09-20 03:00:00'),
(51, 1, 14, 'Comissões', 'dashboard/Reports/commission', '2014-09-20 03:00:00'),
(52, 1, 8, 'Changelog', 'dashboard/System/Changerlog', '2014-03-05 16:05:18'),
(53, 1, 11, 'Agenda', 'dashboard/Manager/agenda', '2014-09-22 03:00:00'),
(55, 1, 3, 'Despesas', 'dashboard/Mov/expense', '2014-09-20 03:00:00'),
(56, 1, 3, 'Receitas', 'dashboard/Mov/income', '2014-09-20 03:00:00'),
(58, 1, 12, 'Acessos', 'dashboard/Settings/access', '2014-03-05 16:05:18'),
(59, 1, 13, 'Receitas', 'dashboard/OverheadCosts/income', '2014-03-05 16:05:18'),
(60, 1, 12, 'Informações Comercial', 'dashboard/Settings/receipts', '2014-10-13 23:32:20'),
(61, 1, 14, 'Recibos', 'dashboard/Reports/receipt', '2014-10-16 16:57:15'),
(62, 1, 11, 'Funcionários', 'dashboard/Manager/employee', '2014-10-21 18:56:27'),
(64, 1, 14, 'Fluxo de Caixa', 'dashboard/Reports/cash_flow', '2014-11-20 00:02:56'),
(65, 0, 14, 'Serviços', 'dashboard/Settings/access', '2014-11-20 00:02:56'),
(66, 1, 11, 'Fontes', 'dashboard/Manager/fonts', '2014-11-26 15:51:10'),
(67, 1, 12, 'Autenticação', 'dashboard/Settings/auth', '2014-12-12 22:01:15'),
(68, 1, 6, 'Galeria', 'dashboard/Tools/gallery', '2014-12-13 20:24:47');


-- --------------------------------------------------------

--
-- Estrutura para tabela `menu_sub_access`
--

CREATE TABLE IF NOT EXISTS `menu_sub_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_type` int(11) NOT NULL,
  `id_sub_menu` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

--
-- Fazendo dump de dados para tabela `menu_sub_access`
--

INSERT INTO `menu_sub_access` (`id`, `account_type`, `id_sub_menu`, `data`) VALUES
(1, 4, 3, CURRENT_TIME),
(2, 4, 4, CURRENT_TIME),
(3, 3, 4, CURRENT_TIME),
(4, 4, 6, CURRENT_TIME),
(5, 3, 6, CURRENT_TIME),
(6, 3, 7, CURRENT_TIME),
(7, 4, 7, CURRENT_TIME),
(9, 2, 15, CURRENT_TIME),
(10, 3, 15, CURRENT_TIME),
(11, 4, 15, CURRENT_TIME),
(13, 2, 17, CURRENT_TIME),
(14, 3, 17, CURRENT_TIME),
(15, 4, 17, CURRENT_TIME),
(17, 2, 18, CURRENT_TIME),
(18, 3, 18, CURRENT_TIME),
(19, 4, 18, CURRENT_TIME),
(21, 2, 22, CURRENT_TIME),
(22, 3, 22, CURRENT_TIME),
(23, 4, 22, CURRENT_TIME),
(25, 2, 25, CURRENT_TIME),
(26, 3, 25, CURRENT_TIME),
(27, 4, 25, CURRENT_TIME),
(29, 2, 27, CURRENT_TIME),
(30, 3, 27, CURRENT_TIME),
(31, 4, 27, CURRENT_TIME),
(32, 4, 28, CURRENT_TIME),
(33, 4, 47, CURRENT_TIME),
(34, 4, 48, CURRENT_TIME),
(35, 4, 49, CURRENT_TIME),
(36, 4, 50, CURRENT_TIME),
(37, 4, 51, CURRENT_TIME),
(39, 2, 52, CURRENT_TIME),
(40, 3, 52, CURRENT_TIME),
(41, 4, 52, CURRENT_TIME),
(42, 4, 53, CURRENT_TIME),
(43, 2, 53, CURRENT_TIME),
(45, 4, 55, CURRENT_TIME),
(46, 4, 56, CURRENT_TIME),
(47, 4, 59, CURRENT_TIME),
(48, 3, 61, CURRENT_TIME),
(49, 4, 61, CURRENT_TIME),
(57, 1, 53, CURRENT_TIME);


-- --------------------------------------------------------

--
-- Estrutura para tabela `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `text` mediumtext NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notifier`
--

CREATE TABLE IF NOT EXISTS `notifier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_from` int(11) DEFAULT NULL COMMENT 'ID do usuário remetente',
  `id_to` int(11) NOT NULL COMMENT 'ID do usuário destinário',
  `title` varchar(250) NOT NULL,
  `text` mediumtext NOT NULL,
  `lida` int(1) DEFAULT '0' COMMENT '0 = não lida, 1 = lida',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='notificações' AUTO_INCREMENT=1 ;

INSERT INTO `notifier` (`id_from`, `id_to`, `title`, `text`, `lida`, `data`) VALUES
(NULL, 6, 'Seja Bem Vindo !', 'Instalação efetuada com sucesso ! seja bem vindo ao {$name}.', 0, CURRENT_TIME);

-- --------------------------------------------------------

--
-- Estrutura para tabela `output_others`
--

CREATE TABLE IF NOT EXISTS `output_others` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_cron` int(11) DEFAULT NULL,
  `cron` tinyint(1) NOT NULL DEFAULT '0',
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(250) DEFAULT NULL,
  `card_number` varchar(250) DEFAULT NULL,
  `cheque_number` varchar(250) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `descri` mediumtext,
  `value` double NOT NULL,
  `status` tinyint(1) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `output_product`
--

CREATE TABLE IF NOT EXISTS `output_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `id_product` int(10) NOT NULL,
  `id_receipt` int(11) NOT NULL,
  `discount` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL,
  `descri` mediumtext,
  `qnt` int(3) DEFAULT '1',
  `value` float NOT NULL,
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(50) DEFAULT NULL,
  `card_number` varchar(100) DEFAULT NULL,
  `cheque_number` varchar(250) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='all products ever made' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `output_servico`
--

CREATE TABLE IF NOT EXISTS `output_servico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(10) DEFAULT NULL,
  `id_service` int(10) DEFAULT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_employee` int(10) DEFAULT NULL,
  `id_receipt` int(11) NOT NULL,
  `discount` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(250) NOT NULL,
  `descri` mediumtext,
  `qnt` int(3) DEFAULT '1',
  `value` float NOT NULL,
  `status` tinyint(1) NOT NULL,
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(50) DEFAULT NULL,
  `card_number` varchar(100) DEFAULT NULL,
  `cheque_number` varchar(100) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='all services ever made' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_fornecedor` int(11) NOT NULL,
  `autoexpense` tinyint(1) NOT NULL DEFAULT '0',
  `nome` varchar(250) NOT NULL,
  `descri` mediumtext,
  `valor_original` varchar(50) NOT NULL,
  `valor` varchar(250) NOT NULL,
  `marcador` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `foto` varchar(250) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ProgrammerBillet`
--

CREATE TABLE IF NOT EXISTS `ProgrammerBillet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_poster` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `id_client` int(11) DEFAULT NULL,
  `id_funcionario` int(11) DEFAULT NULL,
  `id_receipt` int(11) DEFAULT NULL,
  `id_font` int(11) NOT NULL,
  `plots` int(11) NOT NULL,
  `value` float NOT NULL,
  `data_send` date NOT NULL,
  `data_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recibos`
--

CREATE TABLE IF NOT EXISTS `recibos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_employee` int(11) DEFAULT NULL COMMENT 'if type is service, used for commission',
  `id_font` int(11) DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `generate_billet` tinyint(1) NOT NULL DEFAULT '0',
  `installments` int(2) NOT NULL DEFAULT '1',
  `payment_due` int(2) DEFAULT NULL,
  `_interval` int(3) DEFAULT NULL,
  `_interval_period` enum('day','week','month','year') DEFAULT NULL,
  `Payment_method` enum('a_vista','parcelado','entrada_e_parcelas','porcentagem_e_Parcelas') NOT NULL DEFAULT 'a_vista',
  `entry_value` float DEFAULT NULL,
  `percent_entry` float DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `style` enum('Products','Services') NOT NULL,
  `metthod` enum('Dinheiro','Cartão de Crédito','Cheque','Débito Automático') NOT NULL,
  `card_name` enum('American Express','Diners Club','MasterCard','Visa','Maestro','Amex','Outros') DEFAULT NULL,
  `card_agence` varchar(250) DEFAULT NULL,
  `card_number` varchar(250) DEFAULT NULL,
  `cheque_number` varchar(250) DEFAULT NULL,
  `data` datetime NOT NULL,
  `data_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recibos_itens`
--

CREATE TABLE IF NOT EXISTS `recibos_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_recibo` int(11) NOT NULL,
  `id_product` int(11) DEFAULT NULL,
  `id_service` int(11) DEFAULT NULL,
  `nome` varchar(200) NOT NULL,
  `descri` mediumtext,
  `valor_original` float DEFAULT NULL,
  `valor_despesa` float DEFAULT NULL,
  `valor_lucro` float NOT NULL,
  `qnt` int(11) NOT NULL DEFAULT '1',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE IF NOT EXISTS `servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(250) NOT NULL COMMENT 'Título do serviço',
  `descri` mediumtext COMMENT 'uma pequena descrição do serviço',
  `valor` double NOT NULL COMMENT 'valor bruto do serviço',
  `comissao` double DEFAULT NULL COMMENT 'comissão por funcionario',
  `data` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `title` mediumtext NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_first_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_last_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  `user_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s activation status',
  `user_account_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'user''s account type (basic, premium, etc)',
  `user_has_avatar` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 if user has a local avatar, 0 if not',
  `user_rememberme_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s remember-me cookie token',
  `user_creation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp of the creation of user''s account',
  `user_last_login_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of user''s last login',
  `user_failed_logins` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s failed login attempts',
  `user_last_failed_login` int(10) DEFAULT NULL COMMENT 'unix timestamp of last failed login attempt',
  `user_activation_hash` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s email verification hash string',
  `user_registration_ip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_password_reset_hash` char(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s password reset code',
  `user_password_reset_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the password reset request',
  `user_provider_type` text COLLATE utf8_unicode_ci,
  `user_facebook_uid` bigint(20) unsigned DEFAULT NULL COMMENT 'optional - facebook UID',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`),
  KEY `user_facebook_uid` (`user_facebook_uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data' AUTO_INCREMENT=11 ;

--
-- Fazendo dump de dados para tabela `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_first_name`, `user_last_name`, `user_password_hash`, `user_email`, `user_active`, `user_account_type`, `user_has_avatar`, `user_rememberme_token`, `user_creation_timestamp`, `user_last_login_timestamp`, `user_failed_logins`, `user_last_failed_login`, `user_activation_hash`, `user_registration_ip`, `user_password_reset_hash`, `user_password_reset_timestamp`, `user_provider_type`, `user_facebook_uid`) VALUES
(6, 'offboard', 'Offboard', NULL, '$2y$10&#36;BW0INwCO5k37HEplCrKg2uJADvcYFt.mYInxrYvXrZrfORmurbNou', 'offboard@gmail.com', 1, 0, 1, '8dab2db098d7dc6a16b0e3e5b5371413ab0d4086ea9d08f16f9bf8f215969022', '2014-07-24 23:37:00', 1414594612, 0, NULL, '9974b4351eeb2eeeb66c96c6eb21ce4c46cb0029', NULL, NULL, NULL, 'DEFAULT', NULL);
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
EOF;
    }

    /**
     * Here is the main configuration file of your application.
     * Note: replace special characters that makes conflict with the EOFPAGE 
     * if the character in question does not exist, add it to 
     * the array $ASCI_table_replace the file setup-config.php
     * @access protected
     * @param array $POST
     * @return HTML
     */
    protected function FileGenerate($POST) {
        $year = date('Y');

        return <<<EOFPAGE
&#60;?php
        
/**
 * Configuration File {$this->name}
 *
 * For more info about constants please @see http://php.net/manual/en/function.define.php
 * If you want to know why we use "define" instead of "const" @see http://stackoverflow.com/q/2447791/1114320
 * @copyright (c) {$year}, Bruno Ribeiro
 */
 
/**
 * Configuration for: Database
 * This is the place where you define your database credentials, type etc.
 *
 * database type
 * define('DB_TYPE', 'mysql');
 * database host, usually it's "127.0.0.1" or "localhost", some servers also need port info, like "127.0.0.1:8080"
 * define('DB_HOST', '127.0.0.1');
 * name of the database. please note: database and database table are not the same thing!
 * define('DB_NAME', 'login');
 * user for your database. the user needs to have rights for SELECT, UPDATE, DELETE and INSERT
 * By the way, it's bad style to use "root", but for development it will work
 * define('DB_USER', 'root');
 * The password of the above user
 * define('DB_PASS', 'xxx');
 * define('DB_PREFIX', 'prefix_'); or NULL
 */
define('DB_TYPE', 'mysql');
define('DB_HOST', '{$POST['dbhost']}');
define('DB_NAME', '{$POST['dbname']}');
define('DB_USER', '{$POST['uname']}');
define('DB_PASS', '{$POST['pwd']}');
define('DB_PREFIX', NULL);

// get data for dynamic settings
function array_table(&#36;table, &#36;where, &#36;fetch) {
    &#36;mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    &#36;mysqli->set_charset('utf8');
    if (&#36;where !== false) {
        &#36;query = "SELECT &#36;fetch FROM `&#36;table` WHERE &#36;where[0] = '&#36;where[1]' LIMIT 1";
    } else {
        &#36;query = "SELECT &#36;fetch FROM `&#36;table` LIMIT 1";
    }
    &#36;q = mysqli_query(&#36;mysqli, &#36;query);
    while (&#36;data = mysqli_fetch_array(&#36;q)) {
        return &#36;data[&#36;fetch];
    }
}

&#36;_SYSTEM_DEBUG = array_table('Configure', false, 'DEBUG') == 0 ? false : true;

# -----------------------------------------
# Set Locale for date function
# ----------------------------------------- 
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

# -----------------------------------------
# Bug fix erros de caracteres na header
# ----------------------------------------- 
ini_set('default_charset', 'UTF-8');

# -----------------------------------------
# Timezone
# -----------------------------------------
date_default_timezone_set('{$_POST['timezone']}');

# -----------------------------------------
# Configuration for: Error reporting
# Useful to show every little problem during development, but only show hard errors in production
# -----------------------------------------
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Configuration for: Base URL
 * This is the base url of our app. if you go live with your app, put your full domain name here.
 * if you are using a (different) port, then put this in here, like http://mydomain:8888/subfolder/
 * Note: The trailing slash is important!
 */
define('URL',  array_table('Configure', false, 'url'));


# -----------------------------------------
# account type  that is not accessible to other users when you add a sales service.
# is used to checked the combobox in Mov/service
# -----------------------------------------
define('ACCOUNT_TYPE_FOR_SALLER', array_table('Configure', false, 'ACCOUNT_TYPE_FOR_SALLER'));
# -----------------------------------------
# status comission close day
# value boolean
# -----------------------------------------
define('STATUS_DAY_CLOSE', array_table('Configure', false, 'STATUS_DAY_CLOSE'));

# -----------------------------------------
# day that will close the monthly commission.
# used to organize committees
# -----------------------------------------
define('DAY_CLOSE_COMISSION', array_table('Configure', FALSE, 'DAY_CLOSE_COMISSION'));

/**
 * demonstration mode, if true no one can change and add users also can not change 
 * user profile, settings menu, global, access and receipts.
 */
define('DEMOSTRATION', FALSE);

/**
 * Versão mínima do PHP para rodar a aplicação
 * veja a lista dos recursos adicionados, editado e alterados do PHP http://pt.wikipedia.org/wiki/PHP
 * 
 */
define('MIN_PHP_VERSION', '5.4.3');


/**
 * Configuration for: Folders
 * Here you define where your folders are. Unless you have renamed them, there's no need to change this.
 */
define('LIBS_PATH', 'application/libs/');
define('LIBS_DEV_PATH', 'application/libs/Developer/');
define('LIBS_DEV_PATH_DASHBOARD', 'application/libs/Dashboard/');
define('CONTROLLER_PATH', 'application/controllers/');
define('MODELS_PATH', 'application/models/');
define('VIEWS_PATH', 'application/views/');
define('DS', DIRECTORY_SEPARATOR);
// don't forget to make this folder writable via chmod 775 or 777 (?)
// the slash at the end is VERY important!
define('AVATAR_PATH', 'public/avatars/');

/**
 * Configuration for: Additional login providers: Facebook
 * Self-explaining. The FACEBOOK_LOGIN_PATH is the controller-action where the user is redirected to after getting
 * authenticated via Facebook. Leave it like that unless you know exactly what you do.
 */
define('FACEBOOK_LOGIN', false);
define('FACEBOOK_LOGIN_APP_ID', '');
define('FACEBOOK_LOGIN_APP_SECRET', '');
define('FACEBOOK_LOGIN_PATH', 'login/loginWithFacebook');
define('FACEBOOK_REGISTER_PATH', 'login/registerWithFacebook');

/**
 * Configuration for: Avatars/Gravatar support
 * Set to true if you want to use "Gravatar(s)", a service that automatically gets avatar pictures via using email
 * addresses of users by requesting images from the gravatar.com API. Set to false to use own locally saved avatars.
 * AVATAR_SIZE set the pixel size of avatars/gravatars (will be 44x44 by default). Avatars are always squares.
 * AVATAR_DEFAULT_IMAGE is the default image in public/avatars/
 */
define('USE_GRAVATAR', true);
define('AVATAR_SIZE', 44);
define('AVATAR_JPEG_QUALITY', 85);
define('AVATAR_DEFAULT_IMAGE', 'default.jpg');

/**
 * Configuration for: Cookies
 * Please note: The COOKIE_DOMAIN needs the domain where your app is,
 * in a format like this: .mydomain.com
 * Note the . in front of the domain. No www, no http, no slash here!
 * For local development .127.0.0.1 is fine, but when deploying you should
 * change this to your real domain, like '.mydomain.com' ! The leading dot makes the cookie available for
 * sub-domains too.
 * @see http://stackoverflow.com/q/9618217/1114320
 * @see php.net/manual/en/function.setcookie.php
 */
// 1209600 seconds = 2 weeks
define('COOKIE_RUNTIME', array_table('Configure', false, 'COOKIE_RUNTIME'));
// the domain where the cookie is valid for, for local development ".127.0.0.1" and ".localhost" will work
// IMPORTANT: always put a dot in front of the domain, like ".mydomain.com" !
define('COOKIE_DOMAIN', array_table('Configure', false, 'COOKIE_DOMAIN'));


/**
 * Interface used for tool live brandswich
 */
define('INTERFACE_NETWORK', array_table('Configure', false, 'INTERFACE'));


/**
 * Configuration for: Hashing strength
 * This is the place where you define the strength of your password hashing/salting
 *
 * To make password encryption very safe and future-proof, the PHP 5.5 hashing/salting functions
 * come with a clever so called COST FACTOR. This number defines the base-2 logarithm of the rounds of hashing,
 * something like 2^12 if your cost factor is 12. By the way, 2^12 would be 4096 rounds of hashing, doubling the
 * round with each increase of the cost factor and therefore doubling the CPU power it needs.
 * Currently, in 2013, the developers of this functions have chosen a cost factor of 10, which fits most standard
 * server setups. When time goes by and server power becomes much more powerful, it might be useful to increase
 * the cost factor, to make the password hashing one step more secure. Have a look here
 * (@see https://github.com/panique/php-login/wiki/Which-hashing-&-salting-algorithm-should-be-used-%3F)
 * in the BLOWFISH benchmark table to get an idea how this factor behaves. For most people this is irrelevant,
 * but after some years this might be very very useful to keep the encryption of your database up to date.
 *
 * Remember: Every time a user registers or tries to log in (!) this calculation will be done.
 * Don't change this if you don't know what you do.
 *
 * To get more information about the best cost factor please have a look here
 * @see http://stackoverflow.com/q/4443476/1114320
 */
// the hash cost factor, PHP's internal default is 10. You can leave this line
// commented out until you need another factor then 10.
define("HASH_COST_FACTOR", "10");

/**
 * Configuration for: Email server credentials
 *
 * Here you can define how you want to send emails.
 * If you have successfully set up a mail server on your linux server and you know
 * what you do, then you can skip this section. Otherwise please set EMAIL_USE_SMTP to true
 * and fill in your SMTP provider account data.
 *
 * An example setup for using gmail.com [Google Mail] as email sending service,
 * works perfectly in August 2013. Change the "xxx" to your needs.
 * Please note that there are several issues with gmail, like gmail will block your server
 * for "spam" reasons or you'll have a daily sending limit. See the readme.md for more info.
 *
 * define("PHPMAILER_DEBUG_MODE", 0);
 * define("EMAIL_USE_SMTP", true);
 * define("EMAIL_SMTP_HOST", 'ssl://smtp.gmail.com');
 * define("EMAIL_SMTP_AUTH", true);
 * define("EMAIL_SMTP_USERNAME", 'xxxxxxxxxx@gmail.com');
 * define("EMAIL_SMTP_PASSWORD", 'xxxxxxxxxxxxxxxxxxxx');
 * define("EMAIL_SMTP_PORT", 465);
 * define("EMAIL_SMTP_ENCRYPTION", 'ssl');
 *
 * It's really recommended to use SMTP!
 */
// Options: 0 = off, 1 = commands, 2 = commands and data, perfect to see SMTP errors, see the PHPMailer manual for more
define("PHPMAILER_DEBUG_MODE", 0);
// use SMTP or basic mail() ? SMTP is strongly recommended
define("EMAIL_USE_SMTP", true);
// name of your host
define("EMAIL_SMTP_HOST", '');
// leave this true until your SMTP can be used without login
define("EMAIL_SMTP_AUTH", true);
// SMTP provider username
define("EMAIL_SMTP_USERNAME", '');
// SMTP provider password
define("EMAIL_SMTP_PASSWORD", '');
// SMTP provider port
define("EMAIL_SMTP_PORT", 465);
// SMTP encryption, usually SMTP providers use "tls" or "ssl", for details see the PHPMailer manual
define("EMAIL_SMTP_ENCRYPTION", 'ssl');

/**
 * Configuration for: Email content data
 *
 * php-login uses the PHPMailer library, please have a look here if you want to add more
 * config stuff: @see https://github.com/PHPMailer/PHPMailer
 *
 * As email sending within your project needs some setting, you can do this here:
 *
 * Absolute URL to password reset action, necessary for email password reset links
 * define("EMAIL_PASSWORD_RESET_URL", "http://127.0.0.1/php-login/4-full-mvc-framework/login/passwordReset");
 * define("EMAIL_PASSWORD_RESET_FROM_EMAIL", "noreply@example.com");
 * define("EMAIL_PASSWORD_RESET_FROM_NAME", "My Project");
 * define("EMAIL_PASSWORD_RESET_SUBJECT", "Password reset for PROJECT XY");
 * define("EMAIL_PASSWORD_RESET_CONTENT", "Please click on this link to reset your password:");
 *
 * absolute URL to verification action, necessary for email verification links
 * define("EMAIL_VERIFICATION_URL", "http://127.0.0.1/php-login/4-full-mvc-framework/login/verify/");
 * define("EMAIL_VERIFICATION_FROM_EMAIL", "noreply@example.com");
 * define("EMAIL_VERIFICATION_FROM_NAME", "My Project");
 * define("EMAIL_VERIFICATION_SUBJECT", "Account Activation for PROJECT XY");
 * define("EMAIL_VERIFICATION_CONTENT", "Please click on this link to activate your account:");
 */
define("EMAIL_PASSWORD_RESET_URL", URL . "login/verifypasswordreset");
define("EMAIL_PASSWORD_RESET_FROM_EMAIL", "no-reply@example.com");
define("EMAIL_PASSWORD_RESET_FROM_NAME", "My Project");
define("EMAIL_PASSWORD_RESET_SUBJECT", "Password reset for PROJECT XY");
define("EMAIL_PASSWORD_RESET_CONTENT", "Please click on this link to reset your password: ");

define("EMAIL_VERIFICATION_URL", URL . "login/verify");
define("EMAIL_VERIFICATION_FROM_EMAIL", "no-reply@example.com");
define("EMAIL_VERIFICATION_FROM_NAME", "My Project");
define("EMAIL_VERIFICATION_SUBJECT", "Account activation for PROJECT XY");
define("EMAIL_VERIFICATION_CONTENT", "Please click on this link to activate your account: ");

/**
 * Configuration for: Error messages and notices
 *
 * In this project, the error messages, notices etc are all-together called "feedback".
 */
define("FEEDBACK_UNKNOWN_ERROR", "Ocorreu um erro desconhecido!");
define("FEEDBACK_PASSWORD_WRONG_3_TIMES", "Você digitou uma senha errada 3 ou mais vezes. Por favor, aguarde 30 segundos para tentar novamente.");
define("FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET", "A sua conta ainda não está ativada. Por favor, clique no link de confirmação no e-mail.");
define("FEEDBACK_PASSWORD_WRONG", "Senha estava errada.");
define("FEEDBACK_USER_DOES_NOT_EXIST", "Este usuário não existe.");
// The "login failed"-message is a security improved feedback that doesn't show a potential attacker if the user exists or not
define("FEEDBACK_LOGIN_FAILED", "Falha no login.");
define("FEEDBACK_USERNAME_FIELD_EMPTY", "Campo Nome de Usuário vazio.");
define("FEEDBACK_PASSWORD_FIELD_EMPTY", "Campo Senha estava vazio.");
define("FEEDBACK_EMAIL_FIELD_EMPTY", "E-mail e senhas campos estavam vazios.");
define("FEEDBACK_EMAIL_AND_PASSWORD_FIELDS_EMPTY", "Email campo estava vazio.");
define("FEEDBACK_USERNAME_SAME_AS_OLD_ONE", "Desculpe, esse nome é o mesmo que o seu atual. Por favor, escolha outro.");
define("FEEDBACK_USERNAME_ALREADY_TAKEN", "Desculpe, esse nome de usuário já está tomada. Por favor, escolha outro.");
define("FEEDBACK_USER_EMAIL_ALREADY_TAKEN", "Desculpe, esse e-mail já está em uso. Por favor, escolha outro.");
define("FEEDBACK_USERNAME_CHANGE_SUCCESSFUL", "Seu nome de usuário foi alterado com sucesso.");
define("FEEDBACK_USERNAME_AND_PASSWORD_FIELD_EMPTY", "Nome de usuário e senha campos estão vazios.");
define("FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN", "Usuário não se encaixa no esquema username: apenas AZ e números são permitidos, 2-64 caracteres.");
define("FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN", "Desculpe, seu e-mail escolhido não se encaixam no padrão de nomenclatura e-mail.");
define("FEEDBACK_EMAIL_SAME_AS_OLD_ONE", "Desculpe, esse endereço de e-mail é o mesmo que o seu atual. Por favor, escolha outro.");
define("FEEDBACK_EMAIL_CHANGE_SUCCESSFUL", "O seu endereço de e-mail foi alterado com sucesso.");
define("FEEDBACK_CAPTCHA_WRONG", "Os caracteres digitados de segurança captcha estavam errados.");
define("FEEDBACK_PASSWORD_REPEAT_WRONG", "Senha e senha repetição não são os mesmos.");
define("FEEDBACK_PASSWORD_TOO_SHORT", "Senha tem um comprimento mínimo de 6 caracteres.");
define("FEEDBACK_USERNAME_TOO_SHORT_OR_TOO_LONG", "Nome de usuário não pode ser menor que 2 ou mais de 64 caracteres.");
define("FEEDBACK_EMAIL_TOO_LONG", "E-mail não poderá ser maior que 64 caracteres.");
define("FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED", "Sua conta foi criada com sucesso e nós enviou um e-mail. Por favor, clique no link de verificação dentro desse e-mail.");
define("FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED", "Desculpe, mas não poderia enviar-lhe um e-mail de verificação. Sua conta não foi criada.");
define("FEEDBACK_ACCOUNT_CREATION_FAILED", "Desculpe, seu registro falhou. Por favor, volte e tente novamente.");
define("FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR", "Verificação de e-mail não pôde ser enviado devido a: ");
define("FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL", "Um e-mail de verificação foi enviada com sucesso.");
define("FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL", "A ativação foi bem sucedida! Agora você pode logar");
define("FEEDBACK_ACCOUNT_ACTIVATION_FAILED", "Desculpe, nenhuma combinação de código de id / verificação aqui ...");
define("FEEDBACK_AVATAR_UPLOAD_SUCCESSFUL", "Avatar foi enviado bem sucedido.");
define("FEEDBACK_AVATAR_UPLOAD_WRONG_TYPE", "Apenas arquivos JPEG e PNG são suportados.");
define("FEEDBACK_AVATAR_UPLOAD_TOO_SMALL", "Largura / altura do arquivo de origem Avatar é muito pequeno. Precisa ser 100x100 pixels mínimo.");
define("FEEDBACK_AVATAR_UPLOAD_TOO_BIG", "Arquivo de origem Avatar é muito grande. 5 Megabyte é o máximo.");
define("FEEDBACK_AVATAR_FOLDER_DOES_NOT_EXIST_OR_NOT_WRITABLE", "pasta Avatar não existe ou não é gravável. Por favor, altere esta via chmod 775 ou 777.");
define("FEEDBACK_AVATAR_IMAGE_UPLOAD_FAILED", "Algo deu errado com o upload da imagem.");
define("FEEDBACK_PASSWORD_RESET_TOKEN_FAIL", "Não foi possível gravar token para banco de dados.");
define("FEEDBACK_PASSWORD_RESET_TOKEN_MISSING", "Nenhuma senha sinal de reset.");
define("FEEDBACK_PASSWORD_RESET_MAIL_SENDING_ERROR", "Redefinição de senha e-mail não pôde ser enviado devido a: ");
define("FEEDBACK_PASSWORD_RESET_MAIL_SENDING_SUCCESSFUL", "A redefinição de senha de e-mail foi enviado com sucesso.");
define("FEEDBACK_PASSWORD_RESET_LINK_EXPIRED", "Seu link de redefinição expirou. Por favor, use o link de redefinição de dentro de uma hora.");
define("FEEDBACK_PASSWORD_RESET_COMBINATION_DOES_NOT_EXIST", "Combinação código Nome de Usuário / Verificação não existe.");
define("FEEDBACK_PASSWORD_RESET_LINK_VALID", "Redefinição de senha link de validação é válido. Por favor, altere a senha agora.");
define("FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL", "Senha alterada com sucesso.");
define("FEEDBACK_PASSWORD_CHANGE_FAILED", "Desculpe, sua mudança de senha falhou.");
define("FEEDBACK_ACCOUNT_UPGRADE_SUCCESSFUL", "Conta atualização foi bem sucedida.");
define("FEEDBACK_ACCOUNT_UPGRADE_FAILED", "Conta atualização falhou.");
define("FEEDBACK_ACCOUNT_DOWNGRADE_SUCCESSFUL", "Conta rebaixamento foi bem sucedida.");
define("FEEDBACK_ACCOUNT_DOWNGRADE_FAILED", "Rebaixamento Conta falhou.");
define("FEEDBACK_NOTE_CREATION_FAILED", "Nota criação falhou.");
define("FEEDBACK_NOTE_EDITING_FAILED", "Edição Nota falhou.");
define("FEEDBACK_NOTE_DELETION_FAILED", "Nota exclusão falhou.");
define("FEEDBACK_COOKIE_INVALID", "Seu lembrar-me-cookie é inválido.");
define("FEEDBACK_COOKIE_LOGIN_SUCCESSFUL", "Você foi registrado com sucesso em via a lembrar-me-cookie.");
define("FEEDBACK_FACEBOOK_LOGIN_NOT_REGISTERED", "Desculpe, você não tem uma conta aqui. Por favor, registre-se primeiro.");
define("FEEDBACK_FACEBOOK_EMAIL_NEEDED", "Desculpe, mas você precisa nos permitem ver o seu endereço de e-mail para se cadastrar.");
define("FEEDBACK_FACEBOOK_UID_ALREADY_EXISTS", "Desculpe, mas você já se registrou aqui (seu Facebook ID existe em nosso banco de dados).");
define("FEEDBACK_FACEBOOK_EMAIL_ALREADY_EXISTS", "Desculpe, mas você já se registrou aqui (seu e-mail Facebook existe em nosso banco de dados).");
define("FEEDBACK_FACEBOOK_USERNAME_ALREADY_EXISTS", "Desculpe, mas você já se registrou aqui (seu nome de usuário do Facebook existe em nosso banco de dados).");
define("FEEDBACK_FACEBOOK_REGISTER_SUCCESSFUL", "Você foi registrado com êxito com o Facebook.");
define("FEEDBACK_FACEBOOK_OFFLINE", "Nós não poderia alcançar os servidores do Facebook. Talvez Facebook está offline (que realmente acontece às vezes).");


// Português Brasileiro Paginação
define('PAGINATION_TEXT_DB_NAME', 'Não foi poss&iacute;vel conectar ao banco de dados: ');
define('PAGINATION_TEXT_ERRO_QUERY', 'Erro: Tipo de consulta: ');
define('PAGINATION_TEXT_ERRO_TYPE_QUERY', 'Erro na consulta: ');
define('PAGINATION_TEXT_BEFORE', 'Anterior');
define('PAGINATION_TEXT_AFTER', 'Pr&#243;ximo');

// Settings Web Site


/**
 * URL logo web site
 */
define('WEB_SITE_LOGO', array_table('Configure', false, 'LOGO'));

/**
 * Favicon of site
 * use false for disable
 */
define('WEB_SITE_CEO_FAVOICON', array_table('Configure', false, 'FAVOICON'));


/**
 * keywords site
 * use , for separate the words
 */
define('WEB_SITE_CEO_NAME', array_table('Configure', false, 'NAME'));


/**
 * System Debug
 * if it is as false when there is an error interrupts the task if true shows the error as Exception
 */
define('SYSTEM_DEBUG', &#36;_SYSTEM_DEBUG);



/**
 * Sustem preview mode
 * if this application are using a host, the database values autofill for graphs
 * @Boolean
 */
define('SYSTEM_PREVIEW_MODE', false);

/**
 * Mail configuration
 */
define('MAIL_AUTH', array_table('ConfigureMail', false, 'AUTH'));
define('MAIL_SMTP', array_table('ConfigureMail', false, 'SMTP'));
define('MAIL_SMTP_SECURE', array_table('ConfigureMail', false, 'SMTP_SECURE'));
define('MAIL_USER', array_table('ConfigureMail', false, 'USER'));
define('MAIL_PASS', array_table('ConfigureMail', false, 'PASS'));
define('MAIL_PORT', array_table('ConfigureMail', false, 'PORT'));
define('MAIL_CC', array_table('ConfigureMail', false, 'CC'));
define('MAIL_BCC', array_table('ConfigureMail', false, 'BCC'));
define('MAIL_HTML', array_table('ConfigureMail', false, 'HTML'));
define('MAIL_BUTTON_SIGNATURE', array_table('ConfigureMail', false, 'BUTTON_SIGNATURE'));
define('MAIL_TOP_SIGNATURE', array_table('ConfigureMail', false, 'TOP_SIGNATURE'));

EOFPAGE;
    }

    /**
     * Convert Special Char
     * @param string $string
     * @return string
     */
    private function RevertHTML($string) {
        foreach ($this->Rules as $k => $v) {
            $string = str_replace($k, $v, $string);
        }
        return $string;
    }

    /**
     * Create an HTML for step 1
     * @access protected
     * @return HTML
     */
    protected function HTMLStep1() {
        $html = new DOMDocument();
        $html->loadHTMLFile("Pages/step1.xml");
        $html->preserveWhiteSpace = false;
        $html->formatOutput = true;

        $html->getElementById('logo')->nodeValue = sprintf(LOGO, $this->logo, $this->link, $this->name);
        $html->getElementsByTagName('title')->item(0)->nodeValue = sprintf(STEP1_TITLETAG, $this->name);
        $html->getElementById('title')->nodeValue = sprintf(STEP1_TITLE, $this->name);
        $html->getElementById('LIST1')->nodeValue = STEP1_LIST1;
        $html->getElementById('LIST2')->nodeValue = STEP1_LIST2;
        $html->getElementById('LIST3')->nodeValue = STEP1_LIST3;
        $html->getElementById('LIST4')->nodeValue = STEP1_LIST4;
        $html->getElementById('txt1')->nodeValue = sprintf(STEP1_TEXT1, $this->file, $this->file, $this->file);
        $html->getElementById('txt2')->nodeValue = STEP1_TEXT2;
        $html->getElementById('step_1_buttom')->nodeValue = STEP1_BUTTOM;

        return $this->RevertHTML($html->saveHTML());
    }

    /**
     * Make timezone
     * @return String
     */
    private function MakeTZ() {
        $zones = timezone_identifiers_list();
        $locales = array(
            'Africa',
            'America',
            'Antarctica',
            'Arctic',
            'Asia',
            'Atlantic',
            'Australia',
            'Europe',
            'Indian',
            'Pacific',
        );

        foreach ($zones as $zone) {
            $zone = explode('/', $zone); // 0 => Continent, 1 => City
            // Only use "friendly" continent names
            if ($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' || $zone[0] == 'Indian' || $zone[0] == 'Pacific') {
                if (isset($zone[1]) != '') {
                    $locations[$zone[0]][$zone[0] . '/' . $zone[1]] = str_replace('_', ' ', $zone[1]); // Creates array(DateTimeZone => 'Friendly name')
                }
            }
        }

        $result = '<select id="e1" class="populate" style="width: 100%" name="timezone">';
        foreach ($locales as $v) {
            $result.= '<optgroup label="' . $v . '">';
            foreach ($locations[$v] as $value) {
                $result.= '<option value="' . $v . '/' . str_replace(" ", "_", $value) . '">' . $v . '/' . $value . '</option>';
            }
            $result.= '</optgroup>';
        }
        $result.= '</select>';
        return $result;
    }

    /**
     * Create an HTML for step 2
     * @access protected
     * @return HTML
     */
    protected function HTMLStep2() {
        $html = new DOMDocument();
        $html->loadHTMLFile("Pages/step2.xml");
        $html->preserveWhiteSpace = false;
        $html->formatOutput = true;

        $html->getElementById('logo')->nodeValue = sprintf(LOGO, $this->logo, $this->link, $this->name);
        $html->getElementsByTagName('title')->item(0)->nodeValue = sprintf(STEP1_TITLETAG, $this->name);

        $html->getElementById('STEP2_TEXT1')->nodeValue = STEP2_TEXT1;

        $html->getElementById('LIST1')->nodeValue = STEP2_LIST1;
        $html->getElementById('DLIST1')->nodeValue = sprintf(STEP2_DLIST1, $this->name);

        $html->getElementById('LIST2')->nodeValue = STEP2_LIST2;
        $html->getElementById('DLIST2')->nodeValue = STEP2_DLIST2;

        $html->getElementById('LIST3')->nodeValue = STEP2_LIST3;
        $html->getElementById('DLIST3')->nodeValue = STEP2_DLIST3;

        $html->getElementById('LIST3')->nodeValue = STEP2_LIST3;
        $html->getElementById('DLIST3')->nodeValue = STEP2_DLIST3;

        $html->getElementById('LIST4')->nodeValue = STEP2_LIST4;
        $html->getElementById('DLIST4')->nodeValue = STEP2_DLIST4;


        $html->getElementById('field5')->nodeValue = $this->MakeTZ();

        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = str_replace("application/_installation/setup-config.php?step=1", "", $actual_link);


        $u = $html->getElementById('url');

        $u->setAttribute('value', $url);




        $input = $html->getElementById('send');
        $input->removeAttribute('value');
        $input->setAttribute('value', STEP2_BUTTOM);



        return $this->RevertHTML($html->saveHTML());
    }

    /**
     * Create an HTML when it is not possible to connect to the database
     * @access protected
     * @return type
     */
    protected function HTMLErroConnection() {
        $html = new DOMDocument();
        $html->loadHTMLFile("Pages/ErroConnection.xml");
        $html->preserveWhiteSpace = false;
        $html->formatOutput = true;

        $html->getElementsByTagName('title')->item(0)->nodeValue = sprintf(STEP1_TITLETAG, $this->name);

        $html->getElementById('TEXT1')->nodeValue = ERRO_CONNETION_TEXT1;
        $html->getElementById('TEXT2')->nodeValue = ERRO_CONNETION_TEXT2;
        $html->getElementById('TEXT3')->nodeValue = ERRO_CONNETION_TEXT3;
        $html->getElementById('TEXT4')->nodeValue = ERRO_CONNETION_TEXT4;
        $html->getElementById('TEXT5')->nodeValue = ERRO_CONNETION_TEXT5;
        $html->getElementById('TEXT6')->nodeValue = ERRO_CONNETION_TEXT6;
        $html->getElementById('buttom')->nodeValue = ERRO_CONNETION_BUTTOM;

        return $this->RevertHTML($html->saveHTML());
    }

    /**
     * create an HTML when the file was created successfully
     * @access protected
     * @return HTML
     */
    protected function HTMLSucessCreatedFileConfig() {
        $html = new DOMDocument();
        $html->loadHTMLFile("Pages/SucessCreated.xml");
        $html->preserveWhiteSpace = false;
        $html->formatOutput = true;

        $html->getElementById('logo')->nodeValue = sprintf(LOGO, $this->logo, $this->link, $this->name);
        $html->getElementsByTagName('title')->item(0)->nodeValue = sprintf(STEP1_TITLETAG, $this->name);

        $html->getElementById('text')->nodeValue = sprintf(SUCCESS_TEXT, $this->name);

        $html->getElementById('buttom')->nodeValue = SUCCESS_BUTTOM;

        return $this->RevertHTML($html->saveHTML());
    }

    /**
     * create an HTML when some error occurs
     * @access protected
     * @return HTML
     */
    protected function HTMLErroUnknow() {
        $html = new DOMDocument();
        $html->loadHTMLFile("Pages/ErroUnknow.xml");
        $html->preserveWhiteSpace = false;
        $html->formatOutput = true;

        $html->getElementById('logo')->nodeValue = sprintf(LOGO, $this->logo, $this->link, $this->name);
        $html->getElementsByTagName('title')->item(0)->nodeValue = sprintf(STEP1_TITLETAG, $this->name);

        $html->getElementById('ERROUNKNOW_TEXT1')->nodeValue = sprintf(ERROUNKNOW_TEXT1, $this->file);
        $html->getElementById('ERROUNKNOW_TEXT2')->nodeValue = sprintf(ERROUNKNOW_TEXT2, $this->file);
        $html->getElementById('ERROUNKNOW_TEXT3')->nodeValue = ERROUNKNOW_TEXT3;

        $html->getElementById('wp-config')->nodeValue = $this->FileGenerate($_POST);

        $html->getElementById('buttom')->nodeValue = SUCCESS_BUTTOM;

        return $this->RevertHTML($html->saveHTML());
    }

}
