<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo WEB_SITE_CEO_NAME; ?> - Administração</title>
        <base href="<?php echo URL ?>public/dashboard/">
        <link rel="shortcut icon" href="images/favicon.png">
        <!-- Bootstrap core CSS -->
        <link href="bs3/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-reset.css" rel="stylesheet">
        <!--external css-->
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="js/gritter/css/jquery.gritter.css" />
        <!-- Custom styles for this template -->
        <link href="css/style.css?1" rel="stylesheet">
        <link href="css/style-responsive.css" rel="stylesheet" />
        <style>
            .lock-screen{
                text-align: center; 
                color: white; 
                width: 100%; 
                margin-top: 180px; 
                clear: both;
                font-size: 16px;
            }
        </style>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="lock-screen" onload="startTime()">
        <div class="lock-wrapper">
            <div id="time"></div>
            <div class="lock-box text-center">
                <div class="lock-name"><?php echo Session::get('user_name'); ?></div>
                <img src="<?php echo GetInfo::_foto(); ?>" alt="<?php echo Session::get('user_name'); ?>" title="<?php echo Session::get('user_name'); ?>"/>
                <div class="lock-pwd">
                    <form role="form" class="form-inline" action="<?php echo URL; ?>dashboard/login" method="post">
                        <div class="form-group">
                            <input type="hidden" value="<?php echo Session::get('user_name'); ?>" name="user_name">
                            <input type="password" value="" name="user_password" placeholder="Senha" id="exampleInputPassword2" class="form-control lock-input">
                            <button class="btn btn-lock" type="submit">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                </div>


            </div>

        </div>
        <script>
            function startTime() {
                var today = new Date();
                var h = today.getHours();
                var m = today.getMinutes();
                var s = today.getSeconds();
                // add a zero in front of numbers<10
                m = checkTime(m);
                s = checkTime(s);
                document.getElementById('time').innerHTML = h + ":" + m + ":" + s;
                t = setTimeout(function () {
                    startTime()
                }, 500);
            }

            function checkTime(i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }
        </script>
        <!-- Placed js at the end of the document so the pages load faster -->
        <!--Core js-->
        <script src="js/jquery.js"></script>
        <script src="js/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
        <script src="bs3/js/bootstrap.min.js"></script>
        <script src="js/jquery.dcjqaccordion.2.7.js"></script>
        <script src="js/jquery.scrollTo.min.js"></script>
        <script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
        <script src="js/jquery.nicescroll.js"></script>
        <script type="text/javascript" src="js/gritter/js/jquery.gritter.js"></script>
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>
    </body>
</html>