<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;

$names = array(
    'Configuração' => array(
        'link' => NULL,
        'icon' => NULL
    ),
    'Configurações Globais' => array(
        'link' => 'Settings/global',
        'icon' => NULL
    )
);
new menu($filename);
new breadcrumb($filename, $names);
new GlobalSettingsModel();
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
                    <form class="cmxform form-horizontal" id="signupForm" method="post" enctype="multipart/form-data" action="<?php echo URL; ?>dashboard/Settings/global/update">
                        <div class="col-md-6">        

                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Depuração</label>
                                <div class="col-md-6">
                                    <input <?php
                                    if (SYSTEM_DEBUG == true) {
                                        echo 'checked';
                                    }
                                    ?> name="status" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
                                </div>
                                <button data-original-title="Depuração de Código" data-content="Ferramenta usada por desenvolvedores, força o sistema a mostrar erros e cria uma jenala de depuração de páginas." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Nome<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <input value="<?php echo WEB_SITE_CEO_NAME; ?>" class="form-control" id="cname" name="name" minlength="2" type="text" required />
                                </div>
                                <button data-original-title="Nome" data-content="Nome do seu estabelecimento" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>

                            </div>

                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">URL :<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <input value="<?php echo URL; ?>" class="form-control" type="text" name="url" />
                                </div>
                                <button data-original-title="URL do Sistema" data-content="URL completa do sistema, caso esteja hospedado localmente use http://localhost/pasta/" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>
                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Interface<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <input value="<?php echo INTERFACE_NETWORK; ?>" class="form-control" name="interface" type="text" required />
                                </div>
                                <button data-original-title="Interface de Rede" data-content="Mais comuns são eth0, eth1 eth2 etc..." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Tempo Cookie<span id="field-required">*</span></label>
                                <div class="col-md-6">

                                    <input value="<?php echo COOKIE_RUNTIME; ?>" class="form-control" name="time_cookie" type="text" required />
                                </div>
                                <button data-original-title="Tempo da sessão" data-content="Tempo em segundos, usado no campo lembrar senha. Padrão 1209600 segundos = 2 semanas" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Cookie Domínio<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <input value="<?php echo COOKIE_DOMAIN; ?>" class="form-control" name="cookie_domain" type="text" required />
                                </div>
                                <button data-original-title="Cookie Domínio" data-content="O domínio onde o cookie é válidado, para desenvolvimento local use '.127.0.0.1' e '.localhost' não esqueça de colocar . na frente exemplo '.seudominio.com'" data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Cargo<span id="field-required">*</span></label>
                                <div class="col-md-6">
                                    <select id="e1" name="beautician" class="populate" style="width: 100%">
                                        <?php
                                        foreach (GetInfo::$names_acc_type as $k => $v) {
                                            $isSelected = (ACCOUNT_TYPE_FOR_SALLER == $k) ? ' selected' : NULL;
                                            echo '<option' . $isSelected . ' value="' . $k . '">' . $v . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button data-original-title="Cargo " data-content="Essa função permite qual o cargo de  que não poderá escolher o usuário que receberá a comissão, o cargo digitado será sempre o mesmo usuário que vai receber a comissão por um serviço feito, caso tenha acesso ao módulo movimentar." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>

                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Fechar Caixa</label>
                                <div class="col-md-6">
                                    <input <?php
                                    if (STATUS_DAY_CLOSE == true) {
                                        echo 'checked';
                                    }
                                    ?> id="c_day_close" name="c_day_close" type="checkbox" data-on-label="<i class='fa fa-check'></i>" data-off-label="<i class='fa fa-times'></i>">
                                </div>
                                <button data-original-title="Fechar Caixa" data-content="Ferramenta usada para fechar as comissões por mês." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>
                            <div id="hidde_close_day">
                                <div class="form-group">
                                    <label for="nome" class="control-label col-md-4">Dia Comissões<span id="field-required">*</span></label>
                                    <div class="col-md-6">
                                        <div id="dayclose">
                                            <div class="input-group" style="width:150px;">
                                                <input  name="day_close" value="<?php echo DAY_CLOSE_COMISSION; ?>" type="text" class="spinner-input form-control" maxlength="3" readonly>
                                                <div class="spinner-buttons input-group-btn">
                                                    <button type="button" class="btn btn-default spinner-up">
                                                        <i class="fa fa-angle-up"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-default spinner-down">
                                                        <i class="fa fa-angle-down"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button data-original-title="Dia comissões" data-content="Dia do mês que fechará todas as comissões." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-3">Logo</label>
                                <div class="col-md-9">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                        <div class="fileupload-new thumbnail" style="width: 168px; height: 32px; background-image:url('images/no_bg.jpeg');">
                                            <?php
                                            if (WEB_SITE_LOGO)
                                                $logo = URL . 'public/dashboard/images/logo/' . WEB_SITE_LOGO;
                                            else
                                                $logo = 'http://www.placehold.it/200x80/EFEFEF/AAAAAA&amp;text=Sem+imagem';
                                            ?>
                                            <img src="<?php echo $logo; ?>" style="" alt="" />
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
                                    <span class="label label-danger">Atenção!</span>
                                    <span> Resolução Recomendada 168x32 pixels.</span>
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
<!--this page script-->
<script src="js/validation-init.js"></script>
<script>
<?php echo (!STATUS_DAY_CLOSE) ? '$("#hidde_close_day").hide();' : ''; ?>
    $("#c_day_close").change(function () {
        if ($(this).attr("checked")) {
            $("#hidde_close_day").fadeIn();
            return;
        } else {
            $("#hidde_close_day").fadeOut();
        }
    });
</script>