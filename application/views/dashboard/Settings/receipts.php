<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;

$names = array(
    'Configuração' => array(
        'link' => NULL,
        'icon' => NULL
    ),
    'Informações Comercial' => array(
        'link' => 'Settings/receipts',
        'icon' => NULL
    )
);
new menu($filename);
new breadcrumb($filename, $names);
new ReceiptsSettingsModel('PrepareForActionFromForm');
?>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">Alterar Informações comercial
                <span class="tools pull-right">
                    <a class="fa fa-chevron-down" href="javascript:;"></a>
                    <a class="fa fa-times" href="javascript:;"></a>
                </span>
            </header>
            <div class="panel-body">
                <div class="form">
                    <form class="cmxform form-horizontal" id="signupForm" method="post" enctype="multipart/form-data" action="<?php echo URL; ?>dashboard/Settings/receipts/update">
                        <div class="col-md-6">        

                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">CNPJ</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'CNPJ'); ?>" class="form-control" data-mask="99.999.999/9999-99" name="cnpj" minlength="2" type="text" />
                                </div>
                                <button data-original-title="CNPJ" data-content="CNPJ do seu estabelecimento, deixe vázio caso não tenha." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>
                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Telefone</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'Fone'); ?>" class="form-control" data-mask="(99) 9999-9999" name="tel" minlength="2" type="text" />
                                </div>
                                <button data-original-title="Telefone Comercial" data-content="Telefone do seu estabelecimento, deixe vázio caso não tenha." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>
                            <div class="form-group">
                                <label for="nome" class="control-label col-md-4">Email</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'Email'); ?>" class="form-control" name="email" minlength="2" type="text" />
                                </div>
                                <button data-original-title="Email Comercial" data-content="Endereço Eletrônico do seu estabelecimento, deixe vázio caso não tenha." data-placement="top" data-trigger="hover" class="btn btn-info popovers"><i class="fa fa-question-circle"></i></button>
                            </div>
                            <div class="form-group">
                                <label  class="control-label col-lg-4">CEP</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'Cep'); ?>" name="CEP" type="text" id="cep" class="form-control">
                                </div>
                            </div> 
                            <div class="form-group">
                                <label  class="control-label col-lg-4">Logradouro</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'End'); ?>" name="rua" type="text" id="rua" class="form-control">
                                </div>
                            </div>    
                            <div class="form-group">
                                <label  class="control-label col-lg-4">Bairro</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'Bairro'); ?>" name="bairro" type="text" id="bairro"  class="form-control">
                                </div>
                            </div> 
                            <div class="form-group">
                                <label  class="control-label col-lg-4">Cidade</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'Cidade'); ?>" name="cidade" type="text" id="cidade" class="form-control">
                                </div>
                            </div>    
                            <div class="form-group">
                                <label  class="control-label col-lg-4">Estado</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'UF'); ?>" name="uf" type="text" id="uf" class="form-control">
                                </div>
                            </div> 
                            <div class="form-group">
                                <label  class="control-label col-lg-4">Numero</label>
                                <div class="col-md-6">
                                    <input value="<?php new ReceiptsSettingsModel('loaded', 'Num'); ?>" type="text" id="numero" name="numero" class="form-control">
                                </div>
                            </div>    

                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label col-md-3">Logo</label>
                                <div class="col-md-9">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                        <div class="fileupload-new thumbnail" style="width: 200px; height: 50px;">
                                            <?php
                                            $log = Func::array_table('ConfigureInfos', array('id' => 1), 'logo');
                                            if ($log)
                                                $logo = URL . 'public/dashboard/images/recibo/' . $log;
                                            else
                                                $logo = 'http://www.placehold.it/200x50/EFEFEF/AAAAAA&amp;text=Sem+imagem';
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
                                    <span> Resolução Recomendada 200x50 pixels.</span>
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
<script type="text/javascript">
    $(document).ready(function () {
        $("#cep").blur(function () {
            consulta = $("#cep").val()
            var url = "http://cep.correiocontrol.com.br/" + consulta + ".json";
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (json) {
                    $("#rua").val(json.logradouro)
                    $("#bairro").val(json.bairro)
                    $("#cidade").val(json.localidade)
                    $("#uf").val(json.uf)
                    $("#numero").focus();
                },
            });//ajax

        });//função blur
    });
</script>