<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;

$names = array(
    'Configuração' => array(
        'link' => NULL,
        'icon' => NULL
    ),
    'Configurações Autenticação' => array(
        'link' => 'Settings/global',
        'icon' => NULL
    )
);
new menu($filename);
new breadcrumb($filename, $names);
new authModel();
$request_ajax = URL . 'application/models/dashboard/Settings/auth/testSMTPAjax.php';
?>

<!-- Modal -->
<div class="modal fade" id="testSMTP" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Testar dados para autenticação</h4>
            </div>
            <form method="post" id="auth_smtp" class="cmxform form-horizontal ">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-md-4">SMTP<span id="field-required">*</span></label>
                        <div class="col-md-6">
                            <input value="<?php echo MAIL_SMTP; ?>" class="form-control" name="smtp" minlength="2" type="text" required />
                        </div>
                        <button data-original-title="SMTP" data-content="Digite o SMTP do provedor exemplo : smtp.gmail.com" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Email :<span id="field-required">*</span></label>
                        <div class="col-md-6">
                            <input value="<?php echo MAIL_USER; ?>" class="form-control" type="email" id="cemail" name="email" />
                        </div>
                        <button data-original-title="Email" data-content="Email que será enviada as cobranças." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Senha<span id="field-required">*</span></label>
                        <div class="col-md-6">
                            <input value="<?php echo MAIL_PASS; ?>" class="form-control" id="password" name="password" type="password" required />
                        </div>
                        <button data-original-title="Senha" data-content="Senha do email para autenticação." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Porta<span id="field-required">*</span></label>
                        <div class="col-md-6">
                            <div class="input-group" style="width:150px;">
                                <input name="port" value="<?php print MAIL_PORT; ?>" type="number" class="spinner-input form-control" maxlength="5" >
                            </div>
                        </div>
                        <button data-original-title="Porta" data-content="Porta do provedor SMTP geralmente é a porta 587." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4">Criptografia<span id="field-required">*</span></label>
                        <div class="col-md-6">
                            <select id="e2" name="cripter" class="populate" style="width: 100%">
                                <option <?php
                                if (MAIL_SMTP_SECURE == 'tls') {
                                    echo 'selected="selected" ';
                                }
                                ?> value="tls">TLS</option>
                                <option <?php
                                if (MAIL_SMTP_SECURE == 'ssl') {
                                    echo 'selected="selected" ';
                                }
                                ?> value="ssl">SSL</option>
                            </select>
                        </div>
                        <button data-original-title="Criptografia" data-content="Escolha o tipo de criptografia usada pelo servidor SMTP." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Testar</button>
                    <button class="btn btn-info" type="reset">Restaurar</button>
                    <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
                </div>
            </form>                   
        </div>
    </div>
</div>
<!-- modal -->

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">Alterar Configurações Autenticação

                <span class="tools pull-right">
                    <a class="fa fa-chevron-down" href="javascript:;"></a>
                    <a class="fa fa-magic" title="Testar Conexão SMTP" data-toggle="modal" href="#testSMTP"></a>
                    <a class="fa fa-times" href="javascript:;"></a>
                </span>
            </header>
            <div class="panel-body">
                <div class="form">
                    <form class="cmxform form-horizontal" id="signupForm" method="post" action="<?php echo URL; ?>dashboard/Settings/auth/update">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-md-4">CC</label>
                                <div class="col-md-6">

                                    <input value="<?php echo MAIL_CC; ?>" class="form-control" name="cc" type="text"/>
                                </div>
                                <button data-original-title="Carbon Copy" data-content="Se adicionar o nome de um destinatário a esta caixa uma cópia da mensagem para esse destinatário e o nome do destinatário é visível para os outros destinatários da mensagem" data-placement="bottom" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4">BCC</label>
                                <div class="col-md-6">
                                    <input value="<?php echo MAIL_BCC; ?>" class="form-control" name="bcc" type="text"/>
                                </div>
                                <button data-original-title="Blind Carbon Copy" data-content="Se adicionar o nome de um destinatário a esta caixa numa mensagem de correio, é enviada uma cópia da mensagem a esse destinatário e o nome do destinatário não será visível para os outros destinatários da mensagem. " data-placement="bottom" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4">HTML</label>
                                <div class="col-md-6">
                                    <input <?php
                                if (MAIL_HTML == true) {
                                    echo 'checked';
                                }
                                ?> id="c_html" name="c_html" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
                                </div>
                                <button data-original-title="Permitir HTML" data-content="Marque essa opção para permitir textos em HTML em seu email." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>
                            <div id="no_html_mode">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Mensagem Topo</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" name="assing_top" rows="6"><?php echo MAIL_TOP_SIGNATURE; ?></textarea>
                                    </div>
                                    <button data-original-title="Mensagem Topo" data-content="Mensagem que será mostrada no começo de todas as mensagens." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                                </div>


                                <div class="form-group">
                                    <label class="control-label col-md-4">Assinatura</label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" name="assing_button" rows="6"><?php echo MAIL_BUTTON_SIGNATURE; ?></textarea>
                                    </div>
                                    <button data-original-title="Assinatura" data-content="Assinatura que será mostrado no fim de todas as mensagens." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                                </div>
                            </div>
                        </div>



                        <div class="col-md-4">        
                            <div class="form-group">
                                <label class="control-label col-md-4">Autenticar</label>
                                <div class="col-md-6">
                                    <input <?php
                                if (MAIL_AUTH == true) {
                                    echo 'checked';
                                }
                                ?> name="auth" id="c_auth" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
                                </div>
                                <button data-original-title="Autenticar Email" data-content="Marque essa opçao caso queira autenticar os emails das cobranças enviadas." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4">SMTP<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <input value="<?php echo MAIL_SMTP; ?>" class="form-control" id="cname" name="smtp" minlength="2" type="text" required />
                                </div>
                                <button data-original-title="SMTP" data-content="Digite o SMTP do provedor exemplo : smtp.gmail.com" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>

                            </div>
                            <div id="auth_mode">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Email :<span id="field-required">*</span></label>
                                    <div class="col-md-6">
                                        <input value="<?php echo MAIL_USER; ?>" class="form-control" type="email" id="cemail" name="email" />
                                    </div>
                                    <button data-original-title="Email" data-content="Email que será enviada as cobranças." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4">Senha<span id="field-required">*</span></label>
                                    <div class="col-md-6">
                                        <input value="<?php echo MAIL_PASS; ?>" class="form-control" id="password" name="password" type="password" required />
                                    </div>
                                    <button data-original-title="Senha" data-content="Senha do email para autenticação." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4">Criptografia<span id="field-required">*</span></label>
                                    <div class="col-md-6">
                                        <select id="e1" name="cripter" class="populate" style="width: 100%">
                                            <option <?php
                                                if (MAIL_SMTP_SECURE == 'tls') {
                                                    echo 'selected="selected" ';
                                                }
                                                ?> value="tls">TLS</option>
                                            <option <?php
                                                if (MAIL_SMTP_SECURE == 'ssl') {
                                                    echo 'selected="selected" ';
                                                }
                                                ?> value="ssl">SSL</option>
                                        </select>
                                    </div>
                                    <button data-original-title="Criptografia" data-content="Escolha o tipo de criptografia" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Porta<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <div class="input-group" style="width:150px;">
                                        <input name="port" value="<?php print MAIL_PORT; ?>" type="number" class="spinner-input form-control" >
                                    </div>
                                </div>
                                <button data-original-title="Porta" data-content="Porta do provedor SMTP geralmente é a porta 587." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                        </div>
                        <div id="html_mode">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Mensagem Topo</label>
                                    <button data-original-title="Mensagem Topo HTML" data-content="Mensagem que será mostrada no começo de todas as mensagens." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                                    <div class="col-md-9">
                                        <textarea class="wysihtml5 form-control" name="assing_top_html" rows="9"><?php echo MAIL_TOP_SIGNATURE; ?></textarea>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Assinatura</label>
                                    <button data-original-title="Assinatura HTML" data-content="Assinatura que será mostrado no fim de todas as mensagens." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                                    <div class="col-md-9">
                                        <textarea class="wysihtml5 form-control" name="assing_button_html" rows="9"><?php echo MAIL_BUTTON_SIGNATURE; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <div class="col-lg-offset-3 col-lg-6">
                                <button class="btn btn-primary" type="submit">Alterar</button>
                                <button class="btn btn-default" type="reset">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>    



<link rel="stylesheet" href="css/bootstrap-switch.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-wysihtml5/bootstrap-wysihtml5.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-tags-input/jquery.tagsinput.css" />

<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<link href="css/jquery.tagit.css" rel="stylesheet" type="text/css">
<link href="css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">

<!--Core js-->
<script src="js/jquery.js"></script>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<script src="js/bootstrap-switch.js"></script>

<script type="text/javascript" src="js/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="js/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>

<script type="text/javascript" src="js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="js/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>

<script type="text/javascript" src="js/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.quicksearch.js"></script>

<script type="text/javascript" src="js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>

<script src="js/jquery-tags-input/jquery.tagsinput.js"></script>

<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<script src="js/toggle-init.js"></script>

<script src="js/advanced-form.js"></script>
<!--this page script-->
<script src="js/validation-init.js"></script>
<script type="text/javascript" src="js/gritter/js/jquery.gritter.js"></script>
<script src="js/nanobar-master/nanobar.min.js"></script>

<!--HTML MODE-->
<script>
<?php echo (!MAIL_HTML) ? '$("#html_mode").fadeOut(); $("#no_html_mode").fadeIn();' : '$("#html_mode").fadeIn(); $("#no_html_mode").fadeOut();'; ?>
    $("#c_html").change(function () {
        if ($(this).attr("checked")) {
            $("#no_html_mode").fadeOut();
            $("#html_mode").fadeIn();
            return;
        } else {
            $("#html_mode").fadeOut();
            $("#no_html_mode").fadeIn();
        }
    });
</script>

<!--Auth MODE-->
<script>
<?php echo (!MAIL_AUTH) ? '$("#auth_mode").fadeOut();' : ''; ?>
    $("#c_auth").change(function () {
        if ($(this).attr("checked")) {
            $("#auth_mode").fadeIn();
            return;
        } else {
            $("#auth_mode").fadeOut();
        }
    });
</script>

<!-- Start jQuery code Ajax discounts -->
<script type="text/javascript">

    $(function () {
        $("#auth_smtp").submit(function () {

            var options = {
                bg: '#03f1e4'
            };

            var nanobar = new Nanobar(options);
            nanobar.go(25);
            $.ajax({
                url: "<?= $request_ajax; ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    if (data.status === 'error') {
                        $.gritter.add({
                            text: '<div class="alert alert-block alert-danger fade in"><button data-dismiss="alert" class="close close-sm gritter-close" type="button"><i class="fa fa-times"></i></button> <strong>Erro</strong> ' + data.msg + '</div>',
                            sticky: true
                        });
                    } else {
                        $.gritter.add({
                            text: '<div class="alert alert-success alert-block fade in"><button data-dismiss="alert" class="close close-sm gritter-close" type="button"><i class="fa fa-times"></i></button> <strong>Sucesso !</strong> ' + data.msg + '</div>',
                            sticky: true
                        });
                    }
                },
                error: function (data) {
                    var message = ' Ocorreu um erro na requisição <br>' + JSON.stringify(data);
                    $.gritter.add({
                        text: '<div class="alert alert-block alert-danger fade in"><button data-dismiss="alert" class="close close-sm gritter-close" type="button"><i class="fa fa-times"></i></button> <strong>Erro</strong>' + message + '</div>',
                        sticky: true
                    });
                },
                beforeSend: function (xhr) {
                    nanobar.go(50);
                },
                complete: function (xhr) {
                    nanobar.go(100);
                }
            });
            return false;
        });
    });

</script>