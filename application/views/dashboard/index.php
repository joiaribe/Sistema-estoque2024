<?php

use Dashboard\menu as menu;
use Dashboard\sidebar as sidebar;

new menu($filename);
?>
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--mini statistics start-->
        <div class="row">
            <div class="col-md-3">
                <section class="panel">
                    <div class="panel-body">
                        <div class="top-stats-panel">
                            <div class="gauge-canvas">
                                <h4 class="widget-h">Despesa Mensal</h4>
                                <canvas width=160 height=100 id="gauge"></canvas>
                            </div>
                            <ul class="gauge-meta clearfix">
                                <li id="gauge-textfield" class="pull-left gauge-value"></li>
                                <li class="pull-right gauge-title">Prejuízo</li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="panel">
                    <div class="panel-body">
                        <div class="top-stats-panel">
                            <div class="daily-visit">
                                <h4 class="widget-h">Ganhos Diários</h4>
                                <div id="daily-visit-chart" style="width:100%; height: 100px; display: block">

                                </div>
                                <ul class="chart-meta clearfix">
                                    <li class="pull-left visit-chart-value"></li>
                                    <li class="pull-right visit-chart-title"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-md-3">
                <section class="panel">
                    <div class="panel-body">
                        <div class="top-stats-panel">
                            <h4 class="widget-h">Serviços Mensais</h4>
                            <div class="sm-pie">
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php new homeModel('loaded', 'BuildProductsDaily') ?>
        </div>
        <!--mini statistics end-->
        <div class="row">
            <div class="col-md-8">
                <!--earning graph start-->
                <section class="panel">
                    <header class="panel-heading">
                        Gráfico Ganhos <span class="tools pull-right">
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                            <a href="javascript:;" class="fa fa-times"></a>
                        </span>
                    </header>
                    <div class="panel-body">
                        <div id="graph-area" class="main-chart"></div>
                    </div>
                </section>
                <!--earning graph end-->
            </div>
            <div class="col-md-4">
                <?php new homeModel('loaded', 'BuildMonthlyIncome'); ?>
                <?php new homeModel('loaded', 'BuildHistoryIncome'); ?>
            </div>
        </div>
        <!--mini statistics start-->
        <div class="row">
            <div class="col-md-3">
                <div class="mini-stat clearfix">
                    <span class="mini-stat-icon orange"><i class="fa fa-gavel"></i></span>
                    <div class="mini-stat-info">
                        <span><?php echo Func::_contarReg('servicos', array()); ?></span>
                        Serviços
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat clearfix">
                    <span class="mini-stat-icon tar"><i class="fa fa-tag"></i></span>
                    <div class="mini-stat-info">
                        <span><?php echo Func::_contarReg('produtos', array()); ?></span>
                        Produtos
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat clearfix">
                    <span class="mini-stat-icon pink"><i class="fa fa-money"></i></span>
                    <div class="mini-stat-info">
                        <span>R$ <?php
                            $where = array('MONTH(data)' => date('m'));
                            $services = Func::_sum_values('input_servico', 'value', $where);
                            $products = Func::_sum_values('input_product', 'value', $where);
                            $others = Func::_sum_values('input_others', 'value', $where);
                            $total = $services + $products + $others;
                            echo number_format($total);
                            ?></span>
                        Renda Mensal
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mini-stat clearfix">
                    <span class="mini-stat-icon green"><i class="fa fa-eye"></i></span>
                    <div class="mini-stat-info">
                        <span><?php echo Func::_contarReg('clientes', array()); ?></span>
                        Clientes  
                    </div>
                </div>
            </div>
        </div>
        <!--mini statistics end-->
        <?php new homeModel('loaded', 'BuildNotes'); ?>
        <div class="row">
            <?php new homeModel('loaded', 'BuildChat'); ?>
            <div class="col-md-6">
                <?php new homeModel('loaded', 'BuildTask'); ?>
                <!--todolist end-->
                <?php new sidebar(); ?>
                <link href='fullcalendar-2.1.1/fullcalendar.css' rel='stylesheet' />
                <link href='fullcalendar-2.1.1/fullcalendar.print.css' rel='stylesheet' media='print' />
                <!-- Placed js at the end of the document so the pages load faster -->
                <!-- Notifications -->
                <link rel="stylesheet" media="screen" href="plugins/messenger-master/build/css/messenger.css">
                <link rel="stylesheet" media="screen" href="plugins/messenger-master/build/css/messenger-theme-future.css">

                <!--<script type="text/javascript" src="js/notifications.js"></script>-->
                <!--Core js-->
                <script src="js/jquery.js"></script>
                <script src="js/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
                <script src="bs3/js/bootstrap.min.js"></script>
                <script src="js/jquery.dcjqaccordion.2.7.js"></script>
                <script src="js/jquery.scrollTo.min.js"></script>
                <script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
                <script src="js/jquery.nicescroll.js"></script>

                <!-- Notifications JS -->
                <script type="text/javascript" src="plugins/messenger-master/build/js/messenger.min.js"></script>
                <script type="text/javascript" src="plugins/messenger-master/build/js/messenger-theme-future.js"></script>
                <!--[if lte IE 8]>
                <script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script>
                <![endif]-->
                <script src="js/skycons/skycons.js"></script>
                <script src="js/jquery.scrollTo/jquery.scrollTo.js"></script>
                <script src="cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
                <script src="cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>

                <script src='fullcalendar-2.1.1/lib/moment.min.js'></script>
                <script src='fullcalendar-2.1.1/fullcalendar.min.js'></script>
                <script src='fullcalendar-2.1.1/lang-all.js'></script>

                <script src="js/gauge/gauge.js"></script>
                <script src="js/jvector-map/jquery-jvectormap-1.2.2.min.js"></script>
                <script src="js/jvector-map/jquery-jvectormap-us-lcc-en.js"></script>
                <script type="text/javascript" src="js/gritter/js/jquery.gritter.js"></script>
                <!--clock init-->
                <script src="js/css3clock/js/css3clock.js"></script>
                <!--Easy Pie Chart-->
                <script src="js/easypiechart/jquery.easypiechart.js"></script>
                <!--Sparkline Chart-->
                <script src="js/sparkline/jquery.sparkline.js"></script>
                <!--Morris Chart-->
                <script src="js/morris-chart/morris.js"></script>
                <script src="js/morris-chart/raphael-min.js"></script>
                <!--jQuery Flot Chart-->
                <script src="js/flot-chart/jquery.flot.js"></script>
                <script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
                <script src="js/flot-chart/jquery.flot.resize.js"></script>
                <script src="js/flot-chart/jquery.flot.pie.resize.js"></script>
                <script src="js/flot-chart/jquery.flot.growraf.js"></script>
                <script src="js/flot-chart/jquery.flot.animator.min.js"></script>

<!--<script src="js/dashboard.js"></script>-->
                <script src="js/jquery.customSelect.min.js" ></script>
                <script type="text/javascript" src="js/jquery.validate.min.js"></script>
                <!--common script init for all pages-->
                <script src="js/scripts.js"></script>

                <!--this page script-->
                <script src="js/validation-init.js"></script>
                <!--script for this page-->
                <!-- echo out the system feedback (error and success messages) -->
                <?php
                $this->renderFeedbackMessages();
                new homeModel();
                ?>
                <script type='text/javascript'>//<![CDATA[ 
                    function TaskChecked(id, num) {
                        sum = num;
                        var form = document.getElementById("ftask" + num);
                        form.submit();
                    }

                </script>
                <style>
                    .tooltipevent{
                        position:absolute;
                        z-index:10001;
                        border:1px solid #5897fb;
                        width: 20%;
                        height: auto;
                        background-color: white;
                    }
                </style>
                <script>

                    $(document).ready(function () {
                        $("#task_box_message").hide();
                        $("#task_new_button").hide();
                        $("#task_update_button").hide();

                        $("#task_show_new_button").click(function () {
                            $("#task_box_message").show();
                            $("#task_new_button").show();
                            $("#task_show_new_button").hide();
                        });

                        $("#task_new_button").click(function () {
                            $message = $("#task-message").val();
                            if ($message !== '') {
                                $.post("../../application/ajax/new_task.php", {message: $message}, function (json) {
                                    if (json.error) {
                                        Messenger().post({
                                            message: json.msg,
                                            type: 'error',
                                            showCloseButton: true
                                        });
                                    } else {
                                        $("#task-message").val('');
                                        $("#task_box_message").hide();
                                        $("#task_new_button").hide();
                                        $("#task_show_new_button").show();
                                        $("#task_update_button").hide();
                                        loadTasks();
                                        Messenger().post({
                                            message: json.msg,
                                            showCloseButton: true
                                        });
                                    }
                                });
                            } else {
                                Messenger().post({
                                    message: 'Digite alguma mensagem para tarefa',
                                    type: 'error',
                                    showCloseButton: true
                                });
                            }

                        });
                        function loadTasks() {
                            $.get("../../application/ajax/tasks.php", function (html) {
                                $("#sortable-todo").html(html);
                                // check
                                $(".task_check").click(function () {
                                    var myID = $(this).val();
                                    var check = $(this).is(':checked') ? 1 : 0;
                                    $.post("../../application/ajax/check_task.php", {id: myID, status: check}).done(function (json) {
                                        if (json.error) {
                                            Messenger().post({
                                                message: json.msg,
                                                type: 'error',
                                                showCloseButton: true
                                            });
                                        } else {
                                            loadTasks();
                                            Messenger().post({
                                                message: json.msg,
                                                showCloseButton: true
                                            });
                                        }
                                    });
                                });
                                //edit
                                $(".task_edit").click(function (e) {
                                    var myID = $(this).val();
                                    $.post("../../application/ajax/update_task.php", {action: "form", id: myID}).done(function (txt) {
                                        $("#task-message").val(txt.title);

                                        $("#task_box_message").show();
                                        $("#task_update_button").show();

                                        $("#task_new_button").hide();
                                        $("#task_show_new_button").hide();

                                        $("#task_update_button").click(function () {
                                            $message = $("#task-message").val();
                                            if ($message !== '') {
                                                $.post("../../application/ajax/update_task.php", {action: "update", message: $message, id: myID}, function (json) {
                                                    if (json.error) {
                                                        Messenger().post({
                                                            message: json.msg,
                                                            type: 'error',
                                                            showCloseButton: true
                                                        });
                                                    } else {
                                                        $("#task-message").val('');
                                                        $("#task_box_message").hide();
                                                        $("#task_new_button").hide();
                                                        $("#task_show_new_button").show();
                                                        $("#task_update_button").hide();
                                                        loadTasks();
                                                        Messenger().post({
                                                            message: json.msg,
                                                            showCloseButton: true
                                                        });
                                                    }
                                                });
                                            } else {
                                                Messenger().post({
                                                    message: 'Você não pode alterar a tarefa e deixar o campo vázio',
                                                    type: 'error',
                                                    showCloseButton: true
                                                });
                                            }
                                        });
                                    });
                                });
                                // remove
                                $(".task_remove").click(function (e) {
                                    var myID = $(this).val();
                                    $.getJSON("../../application/ajax/del_task.php?id=" + myID, function (json) {
                                        if (json.error) {
                                            Messenger().post({
                                                message: json.msg,
                                                type: 'error',
                                                showCloseButton: true
                                            });
                                        } else {
                                            $("#task_update_button").hide();
                                            loadTasks();
                                            Messenger().post({
                                                message: json.msg,
                                                showCloseButton: true
                                            });
                                        }
                                    });
                                });
                            });
                        }

                        loadTasks();
                        function renderCalendar() {
                            $('#calendar').fullCalendar({
                                header: {
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: 'month,agendaWeek,agendaDay'
                                },
                                defaultDate: '<?php echo date('Y-m-d'); ?>',
                                lang: 'pt-br',
                                buttonIcons: true, // show the prev/next text
                                weekNumbers: false,
                                editable: false,
                                eventLimit: true, // allow "more" link when too many events

                                eventMouseover: function (calEvent, jsEvent) {
                                    var title = "<tr><th>Título : </th><th> " + calEvent.title + "</th></tr>";
                                    var client = "<tr><th>Cliente : </th><th> " + calEvent.client + "</th></tr>";
                                    var data = "<tr><th>Data : </th><th> " + calEvent.date_formatted + "</th></tr>";
                                    var time = "<tr><th>Horário : </th><th> " + calEvent.time_formatted + "</th></tr>";
                                    var msg = "<tr><th>Descrição : </th><th> " + calEvent.msg + "</th></tr>";
                                    var content = client + title + data + time + msg;
                                    var tooltip = '<table id="calendar" class="tooltipevent"><tbody>' + content + '</tbody></table>';
                                    $("body").append(tooltip);
                                    $(this).mouseover(function (e) {
                                        $(this).css('z-index', 10000);
                                        $('.tooltipevent').fadeIn('500');
                                        $('.tooltipevent').fadeTo('10', 1.9);
                                    }).mousemove(function (e) {
                                        $('.tooltipevent').css('top', e.pageY + 10);
                                        $('.tooltipevent').css('left', e.pageX + 20);
                                    });
                                },
                                eventMouseout: function (calEvent, jsEvent) {
                                    $(this).css('z-index', 8);
                                    $('.tooltipevent').remove();
                                },
                                events: [<?php new calendarModel('events_js'); ?>],
                            });
                        }

                        renderCalendar();
                    });

                </script>