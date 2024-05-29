<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="robots" content="index,nofollow">
        <base href="<?php echo URL ?>public/dashboard/">
        <link rel="shortcut icon" href="images/favicon.png">
        <title><?php echo WEB_SITE_CEO_NAME; ?> - Entrar</title>
        <!--Core CSS -->
        <link href="bs3/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/bootstrap-reset.css" rel="stylesheet">
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet" />
        <!-- Custom styles for this template -->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/style-responsive.css" rel="stylesheet" />
        <!--external css-->
        <link rel="stylesheet" type="text/css" href="js/gritter/css/jquery.gritter.css" />
        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]>
        <script src="js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="login-body">
        <div class="container">
            <form class="form-signin" action="<?php echo URL; ?>login/login" method="post">
                <h2 class="form-signin-heading">Área Restrita</h2>
                <div class="login-wrap">
                    <div class="user-login-info">
                        <input type="text" <?php if (DEMOSTRATION == true) echo 'value="offboard"'; ?> name="user_name" class="form-control" placeholder="Usuário (ou email)" autofocus>
                        <input type="password" <?php if (DEMOSTRATION == true) echo 'value="demo123"'; ?>  name="user_password" class="form-control" placeholder="Senha">
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" value="remember-me"> Lembrar de mim ?
                    </label>
                    <button class="btn btn-lg btn-login btn-block" type="submit">Acessar</button>
                </div>
            </form>
        </div>
        <!-- Placed js at the end of the document so the pages load faster -->
        <!--Core js-->
        <script src="js/jquery.js"></script>
        <script src="bs3/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/gritter/js/jquery.gritter.js"></script>
    </body>
</html>
<?php $this->renderFeedbackMessages(); ?>