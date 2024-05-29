<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Bruno Ribeiro">
        <base href="<?php echo URL ?>public/dashboard/">
        <link src="<?php echo WEB_SITE_LOGO; ?>" rel="icon" type="image/x-icon" />
        <link rel="shortcut icon" type="image/x-icon" href="images/favicon/<?php echo WEB_SITE_CEO_FAVOICON; ?>">
        <title><?php echo WEB_SITE_CEO_NAME; ?> - Administração</title>
        <!--Core CSS -->
        <link href="bs3/css/bootstrap.min.css" rel="stylesheet">
        <link href="js/jquery-ui/jquery-ui-1.10.1.custom.min.css" rel="stylesheet">
        <link href="css/bootstrap-reset.css" rel="stylesheet">
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="js/jvector-map/jquery-jvectormap-1.2.2.css" rel="stylesheet">
        <link href="css/clndr.css" rel="stylesheet">
        <!--clock css-->
        <link href="js/css3clock/css/style.css" rel="stylesheet">
        <!--Morris Chart CSS -->
        <link rel="stylesheet" href="js/morris-chart/morris.css">
        <!-- Custom styles for this template -->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/style-responsive.css" rel="stylesheet"/>
        <!--external css-->
        <link rel="stylesheet" type="text/css" href="js/gritter/css/jquery.gritter.css" />
        <!--[if lt IE 9]>
        <script src="js/ie8-responsive-file-warning.js"></script><![endif]-->
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <script>
            function GuideSearch() {
                var form = document.getElementById("form_guide_search");
                var field = document.getElementById("g_search");
                form.action = form.action + encodeURIComponent(field.value).replace(/%20/g, "+").replace(/%2/g, "");
                form.submit();
            }
        </script>
    </head>
    <body>
        <section id="container">
            <!--header start-->
            <header class="header fixed-top clearfix">
                <!--logo start-->
                <div class="brand">

                    <a href="<?php echo URL; ?>dashboard/index" class="logo">
                        <img src="images/logo/<?php echo WEB_SITE_LOGO; ?>" alt="<?php echo WEB_SITE_CEO_NAME; ?>" title="<?php echo WEB_SITE_CEO_NAME; ?>">
                    </a>
                    <div class="sidebar-toggle-box">
                        <div class="fa fa-bars"></div>
                    </div>
                </div>
                <!--logo end-->

                <div class="nav notify-row" id="top_menu">
                    <!--  notification start -->
                    <ul class="nav top-menu">
                        <!-- inbox dropdown start-->
                        <?php new Dashboard\notifier('load_inbox'); ?>
                        <!-- inbox dropdown end -->

                        <!-- notification dropdown start-->
                        <?php new Dashboard\notifier('load_notifier'); ?>
                        <!--  notification end -->
                </div>
                <div class="top-nav clearfix">
                    <!--search & user info start-->
                    <ul class="nav pull-right top-menu">
                        <li>
                            <form id="form_guide_search" action="<?php echo URL; ?>dashboard/Guide/doubts/Search" method="get">
                                <input onsubmit="GuideSearch();" id="g_search" name="search" type="text" class="form-control search" placeholder=" Pesquisar Dúvidas">
                            </form>
                        </li>
                        <!-- user login dropdown start-->
                        <li class="dropdown">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <img alt="<?php echo Session::get('user_name'); ?>" title="<?php echo Session::get('user_name'); ?>" width="33" height="33" src="<?php echo GetInfo::_foto(Session::get('user_id')); ?>">
                                <span class="username"><?php echo Session::get('user_name'); ?></span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu extended logout">
                                <li><a href="<?php echo URL; ?>dashboard/Settings/perfil"><i class=" fa fa-suitcase"></i>Perfil</a></li>
                                <!-- <li><a href="<?php echo URL; ?>dashboard/settings"><i class="fa fa-cog"></i> Configurações</a></li> -->
                                <li><a href="<?php echo URL; ?>dashboard/System/Changerlog"><i class="fa fa-book"></i> Changerlog</a></li>
                                <li><a href="<?php echo URL; ?>dashboard/System/lock_screen"><i class="fa fa-lock"></i> Suspender</a></li>
                                <li><a href="<?php echo URL; ?>login/logout"><i class="fa fa-power-off"></i> Sair</a></li>
                            </ul>
                        </li>
                        <!-- user login dropdown end -->
                         <!--
                         <li>
                            <div class="toggle-right-box">
                                <div class="fa fa-bars"></div>
                            </div>
                        </li>
                        -->
                    </ul>
                    <!--search & user info end-->
                </div>
            </header>
            <!--header end-->