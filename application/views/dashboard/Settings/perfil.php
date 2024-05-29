<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;

new menu($filename);
new breadcrumb($filename, array('Perfil' => array('link' => false, 'icon' => false)));
new PerfilSettingsModel();
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">Alterar Configurações Globais
                <span class="tools pull-right">
                    <a class="fa fa-chevron-down" href="javascript:;"></a>
                    <a class="fa fa-times" href="javascript:;"></a>
                </span>
            </header>
            <div class="panel-body">
                <div class="form">
                    <form class="cmxform form-horizontal" id="signupForm" method="post" enctype="multipart/form-data" action="<?php echo URL; ?>dashboard/Settings/perfil/update">
                        <div class="col-md-6">        

                            <div class="form-group">
                                <label class="control-label col-md-4">Nome<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <input value="<?php echo GetInfo::FirstName() ?>" class="form-control" name="firstname" type="text" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Sobrenome</label>
                                <div class="col-md-6">
                                    <input value="<?php echo GetInfo::LastName() ?>" class="form-control" name="lname" type="text" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Usuário<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <input value="<?php echo GetInfo::Login(); ?>" class="form-control " id="username" name="username" type="text" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4">Email<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <input value="<?php echo GetInfo::Email() ?>" class="form-control" id="email" name="email" type="email" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4">Mudar Senha</label>
                                <div class="col-md-6">
                                    <input id="c_pass" name="c_pass" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
                                </div>
                                <button data-original-title="Mudar Senha" data-content="Marque caso queira mudar a sua senha" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-exclamation-circle"></i></button>
                            </div>
                            <id id="change_password">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Senha Atual<span id="field-required">*</span></label>
                                    <div class="col-md-6">
                                        <input class="form-control " id="pass" name="pass" type="password" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-4">Nova Senha<span id="field-required">*</span></label>
                                    <div class="col-md-6">
                                        <input class="form-control " id="password" name="password" type="password" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-4">Confirmar Nova Senha<span id="field-required">*</span></label>
                                    <div class="col-md-6">
                                        <input class="form-control " id="confirm_password" name="confirm_password" type="password" />
                                    </div>
                                </div>
                            </id>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-3">Avatar</label>
                                <div class="col-md-9">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                        <div class="fileupload-new thumbnail" style="width: auto; height: auto;">
                                            <img src="<?php echo GetInfo::_foto(); ?>" style="" alt="" />
                                        </div>
                                        <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height:80px; line-height: 20px;"></div>
                                        <div>
                                            <span class="btn btn-white btn-file">
                                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Enviar Imagem</span>
                                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Mudar</span>
                                                <input type="file" name="img" id="img" class="default"/>
                                            </span>
                                            <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remover</a>
                                        </div>
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
<link rel="stylesheet" type="text/css" href="js/bootstrap-fileupload/bootstrap-fileupload.css" />

<link rel="stylesheet" type="text/css" href="js/bootstrap-datepicker/css/datepicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-timepicker/css/timepicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-colorpicker/css/colorpicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-datetimepicker/css/datetimepicker.css" />

<link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-tags-input/jquery.tagsinput.css" />

<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<link href="css/jquery.tagit.css" rel="stylesheet" type="text/css">
<link href="css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css"><!-- Placed js at the end of the document so the pages load faster -->
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

<script type="text/javascript" src="js/fuelux/js/spinner.min.js"></script>
<script type="text/javascript" src="js/bootstrap-fileupload/bootstrap-fileupload.js"></script>

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
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>  
<!--this page script-->
<script src="js/validation-init.js"></script>

<script>
    $("#change_password").hide();
    $("#c_pass").change(function () {
        if ($(this).attr("checked")) {

            $("#change_password").fadeIn();
            return;
        } else {
            $("#change_password").fadeOut();
        }
    });
</script>