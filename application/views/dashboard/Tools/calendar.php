<?php

use Dashboard\menu as menu;
use Dashboard\sidebar as sidebar;

// loaded menu dashboard
new menu($filename);
// used param for load class only here :P
new CalendarModel('loaded');
?>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Calendário
                        <span class="tools pull-right">
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                            <a href="javascript:;" class="fa fa-times"></a>
                        </span>
                    </header>
                    <div class="panel-body">
                        <aside class="col-lg-11">
                            <div id="calendar" class="has-toolbar"></div>
                        </aside>
                    </div>
                </section>
            </div>
        </div>
        <!-- page end-->
    </section>
</section>
<!--main content end-->


<?php new sidebar(); ?>

<!-- Placed js at the end of the document so the pages load faster -->
<link href='fullcalendar-2.1.1/fullcalendar.css' rel='stylesheet' />
<link href='fullcalendar-2.1.1/fullcalendar.print.css' rel='stylesheet' media='print' />

<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<!--full calendar-->
<script src='fullcalendar-2.1.1/lib/moment.min.js'></script>
<script src='fullcalendar-2.1.1/fullcalendar.min.js'></script>
<script src='fullcalendar-2.1.1/lang-all.js'></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>


<!--common script init for all pages-->
<script src="js/scripts.js"></script>
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
        function renderCalendar() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultDate: '<?php echo date('Y-m-d'); ?>',
                lang: 'pt-br',
                buttonIcons: false, // show the prev/next text
                weekNumbers: true,
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
</body>
</html>
