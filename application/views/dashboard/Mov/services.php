<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;

// loaded menu dashboard
new menu($filename);
// loaded breadcrumb
new ServiceModel('action');  // used param for load class only here :P
new breadcrumb($filename, array('Movimentar' => array('link' => false, 'icon' => false), 'Venda Serviços' => NULL));
$request_ajax = URL . 'application/models/dashboard/Mov/Services/ServicesAjax.php';
?>

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">Movimentar Vendas de Serviços
                <span class="tools pull-right">
                    <a class="fa fa-chevron-down" href="javascript:;"></a>
                    <a class="fa fa-times" href="javascript:;"></a>
                </span>
            </header>
            <div class="panel-body">
                <div class="form">
                    <?php new ServiceModel('loaded'); ?>
                </div>
            </div>
        </section>
    </div>
</div>    

<!--external css-->
<link rel="stylesheet" type="text/css" href="js/gritter/css/jquery.gritter.css" />
<link href="css/smart_cart.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/bootstrap-switch.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-datepicker/css/datepicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-timepicker/css/timepicker.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
<link rel="stylesheet" type="text/css" href="js/bootstrap-datetimepicker/css/datetimepicker.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-tags-input/jquery.tagsinput.css" />
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<link href="css/jquery.tagit.css" rel="stylesheet" type="text/css">
<link href="css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">



<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
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


<script type="text/javascript" src="js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-daterangepicker/daterangepicker.js"></script>
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
<script type="text/javascript" src="js/jquery.smartCart-2.0-Services.js"></script>
<script type="text/javascript" src="js/gritter/js/jquery.gritter.js"></script>

<!-- Jquery Mask -->
<script type="text/javascript" src="js/mask/jquery.mask.js"></script>
<script type="text/javascript" src="js/mask.js"></script>
<script src="js/nanobar-master/nanobar.min.js"></script>

<!-- Check status -->
<script>
    $(".status_txt").text('Pago');
    $("#icheck").change(function () {
        if ($(this).attr("checked")) {
            $(".status_txt").text('Pago');
            return;
        } else {
            $(".status_txt").text('Pendente');
        }
    });

</script>

<!-- load jquery smart cart -->
<script type='text/javascript'>
    $(document).ready(function () {
        // Call Smart Cart    	
        $('#SmartCart').smartCart();
    });
</script>

<!-- check metthod payment -->
<script>
    $(document).ready(function () {
        $("#only_check").hide();
        $("#only_card").hide();
        $("#only_ag").hide();
        $("select").change(function () {
            $("select option:selected").each(function () {
                if ($(this).attr("value") == "Cheque") {

                    $("#only_card").fadeOut();
                    $("#only_ag").fadeOut();
                    $("#only_check").fadeIn();
                    $('#only_card').find('input:text').val('');
                    $('#only_ag').find('input:text').val('');
                }
                if ($(this).attr("value") == "Cartão de Crédito") {
                    $("#only_ag").fadeOut();
                    $("#only_card").fadeIn();
                    $('#only_ag').find('input:text').val('');
                }
                if ($(this).attr("value") == "Débito Automático") {
                    $("#only_check").fadeOut();
                    $("#only_ag").fadeIn();
                    $("#only_card").fadeIn();
                    $('#only_check').find('input:text').val('');
                }
                if ($(this).attr("value") == "Dinheiro") {
                    $("#only_check").fadeOut();
                    $("#only_ag").fadeOut();
                    $("#only_card").fadeOut();
                    $('#only_check').find('input:text').val('');
                    $('#only_ag').find('input:text').val('');
                    $('#only_card').find('input:text').val('');
                }
            });
        }).change();
    });
</script>

<!-- Modal Discount -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Adicionar Desconto ao Serviço</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <form action="return.php" method="post" id="form_discount" class="form-horizontal ">
                        <div class="form-group">
                            <label class="control-label col-md-3">Usuário</label>
                            <div class="col-md-5">
                                <input type="text" name="user_name" class="form-control" placeholder="Usuário (ou email)" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Senha</label>
                            <div class="col-md-6">
                                <input type="password" name="user_password" class="form-control" placeholder="Senha">
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">Prosseguir</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
            </div>

            </form>                   
        </div>
    </div>
</div>
<!-- END modal -->

<!-- Modal Generate Billets -->
<div class="modal fade" id="myModalBillet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Opções Gerar Boletos</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <?php new ServiceModel('load_element', 'loadModalBillet') ?>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="button" id="generate_billet" >Prosseguir</button>
                <button data-dismiss="modal" class="btn btn-default" type="button">Fechar</button>
            </div>                 
        </div>
    </div>
</div>
<!-- END modal -->





<!-- Start jQuery code Ajax discounts -->
<script type="text/javascript">
    $(function () {
        $("#discount").change(function () {
            if ($(this).attr("checked")) {
                $("#myModal").modal('show');
                return false;
            } else {
                $("#myModal").modal('toggle');
                $('#response').html('');
                return false;
            }
        });

        $("#form_discount").submit(function () {

            var options = {
                bg: '#03f1e4'
            };
            var nanobar = new Nanobar(options);
            nanobar.go(50);

            $.ajax({
                url: "<?= $request_ajax; ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    nanobar.go(100);
                    $('#myModal').modal('toggle');
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
                        // show the response
                        $('#response').html(data.json);
                        $('#discount').on.focusin();
                    }
                },
                error: function (data) {
                    nanobar.go(100);
                    var message = ' Ocorreu um erro na requisição ' + JSON.stringify(data);
                    $.gritter.add({
                        text: '<div class="alert alert-block alert-danger fade in"><button data-dismiss="alert" class="close close-sm gritter-close" type="button"><i class="fa fa-times"></i></button> <strong>Erro</strong>' + message + '</div>',
                        sticky: true
                    });
                }
            });
            return false;
        });
    });
</script>


<!-- Start jQuery code Ajax Billet -->
<script type="text/javascript">
    $(document).ready(function () {
        $("#generate_billet").click(function () {
            var date = $('#date_e').val();
            var font = $("#e12").children(":selected").val();

            $("#date_close").val(date);
            $("#final_font_payment").val(font);

            $("#myModalBillet").modal("toggle");

            return false;
        });
    });
</script>

<!-- Start jQuery code Ajax incoming and installment payment's -->
<script type="text/javascript">
    $(document).ready(function () {
        $("select").change(function () {
            $("select option:selected").each(function () {
                if ($(this).attr("value") == "a_vista") {
                    $("#div_discount_percent").fadeOut();
                    $("#div_money_entry").fadeOut();
                    $("#div_plots").fadeOut();
                }
                if ($(this).attr("value") == "parcelado") {
                    $("#div_discount_percent").fadeOut();
                    $("#div_money_entry").fadeOut();
                    $("#div_plots").fadeIn();
                }
                if ($(this).attr("value") == "entrada_e_parcelas") {
                    $("#div_discount_percent").fadeOut();
                    $("#div_money_entry").fadeIn();
                    $("#div_plots").fadeIn();
                }
                if ($(this).attr("value") == "porcentagem_e_Parcelas") {
                    $("#div_discount_percent").fadeIn();
                    $("#div_money_entry").fadeOut();
                    $("#div_plots").fadeIn();
                }
            });
        }).change();

        $("#status_billet").change(function () {
            if ($(this).attr("checked")) {
                $("#myModalBillet").modal('show');
                return false;
            } else {
                $("#myModalBillet").modal('toggle');
                //$('#response').html('');
                return false;
            }
        });
        $("#interval").fadeOut();
        $("#c_interval").change(function () {
            if ($(this).attr("checked")) {
                $("#interval").fadeIn();
                return false;
            } else {
                $("#interval").fadeOut();
                return false;
            }
        });
    });
</script>


