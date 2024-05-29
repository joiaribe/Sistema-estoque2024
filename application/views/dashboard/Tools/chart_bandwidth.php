<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// loaded menu dashboard
new menu($filename);
// load breadcrumb
$names = array(
    'Ferramentas' => array(
        'link' => NULL,
        'icon' => NULL
    ),
    'Largura de Banda' => array(
        'link' => 'Manager/',
        'icon' => NULL
    )
);
// loaded breadcrumb
new breadcrumb($filename, $names);
?>
<style>
    #rec_result{font-weight:bold;color:#129631;float: left;}
    #rec_max{ font-weight:bold; color:#129631; float: left; padding-left: 10px;}
    #snd_max{font-weight:bold;color:#0E15CF;float: left;padding-left: 10px;}
    #snd_result{font-weight:bold;color:#0E15CF;float: left;}
    #rec_graph{width:auto;height:auto;font-size: 6pt;}
    #snd_graph{width:auto;height:auto;font-size: 6pt;}
</style>
<!-- page start-->
<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Monitoramento Largura da Banda (Tempo Real)
                <span class="tools pull-right">
                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" class="fa fa-cog"></a>
                    <a href="javascript:;" class="fa fa-times"></a>
                </span>
            </header>
            <div class="panel-body">
                <div id="rec_result"></div><div id="rec_max"></div>
                <canvas id="rec_graph" width="1000" height="300"></canvas>
            </div>

    </div>
    <div class="panel-body">
        <div id="snd_result"></div><div id="snd_max"></div>
        <canvas id="snd_graph" width="1000" height="300"></canvas>
    </div>
</div>

</section>
</section>
<?php new sidebar(); ?>
<!-- Placed js at the end of the document so the pages load faster -->

<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
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

<script type="text/javascript" src="<?php echo URL; ?>public/dashboard/js/graph.js"></script>
<script>
    var range_rec = 512;  // KBps
    var range_snd = 512;  // KBps
    var rec_max = null;
    var snd_max = null;

    if (typeof (EventSource) !== "undefined")
    {
        var stat_source = new EventSource("<?php echo URL; ?>public/dashboard/js/stat.php");
        var rate_rec = 0;
        var rate_snd = 0;
        stat_source.onmessage = function(e)
        {
            var seconds = 1;
            var data = JSON.parse(e.data);
            var new_rec = data.rec;
            var new_snd = data.snd;

            if (typeof (old_rec) != "undefined" && typeof (old_snd) != "undefined")
            {
                var bytes_rec = new_rec - old_rec;
                var bytes_snd = new_snd - old_snd;

                rate_rec = bytes_rec / seconds / 1024;
                rate_snd = bytes_snd / seconds / 1024;

                // Check over/under flow
                if (rate_rec > range_rec || rate_rec < 0)
                    rate_rec = old_rate_rec;
                else
                    old_rate_rec = rate_rec;

                if (rate_snd > range_snd || rate_snd < 0)
                    rate_snd = old_rate_snd;
                else
                    old_rate_snd = rate_snd;

                /*
                 * Capture max receive data
                 */
                if (rate_rec > rec_max) {
                    rec_max = rate_rec;
                    document.getElementById("rec_max").innerHTML = "Máximo: " + Math.round(rec_max * 100) / 100 + " KBps";
                }
                /**
                 * Capture max send data
                 */
                if (rate_snd > snd_max) {
                    snd_max = rate_snd;
                    document.getElementById("snd_max").innerHTML = "Máximo: " + Math.round(snd_max * 100) / 100 + " KBps";
                }

                document.getElementById("rec_result").innerHTML = "Recebido: " + Math.round(rate_rec * 100) / 100 + " KBps";
                document.getElementById("snd_result").innerHTML = "Enviado: " + Math.round(rate_snd * 100) / 100 + " KBps";
            }
            old_rec = new_rec;
            old_snd = new_snd;
        };

    } else {
        document.getElementById("rec_result").innerHTML =
                "Desculpe, seu navegador não suporta eventos enviados pelo servidor ...";
    }

    window.onload = function() {

        rec_graph = new Graph(
                {
                    'id': "rec_graph",
                    'interval': 1000,
                    'strokeStyle': "#819C58",
                    'fillStyle': "rgba(64,128,0,0.25)",
                    'grid': [32, 32],
                    'range': [0, range_rec],
                    'call': function() {
                        return (Math.round(rate_rec));
                    }
                });

        snd_graph = new Graph(
                {
                    'id': "snd_graph",
                    'interval': 1000,
                    'strokeStyle': "#58819C",
                    'fillStyle': "rgba(0,88,145,0.25)",
                    'grid': [32, 32],
                    'range': [0, range_snd],
                    'call': function() {
                        return (Math.round(rate_snd));
                    }
                });

    }
</script>