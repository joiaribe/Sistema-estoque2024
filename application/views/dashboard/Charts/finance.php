<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// loaded menu dashboard
new menu($filename);
$b = array(
    'Gráficos Relatório' => false,
    'Finanças' => false
);
// loaded breadcrumb
new breadcrumb($filename, $b);
?>


<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Controle Financeiro Diário
                <span class="tools pull-right">
                    <a href="javascript:;" title="Minimizar" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" title="Fechar" class="fa fa-times"></a>
                </span>
            </header>
            <div class="panel-body">
                <div id="combine-chart">
                    <div id="legendcontainer26" class="legend-block">
                    </div>
                    <div id="combine-chartContainer" style="width: 100%;height:300px; text-align: center; margin:0 auto;">
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>



<div class="row">
    <div class="col-sm-6">
        <section class="panel">
            <header class="panel-heading">
                Top Serviços
                <span class="tools pull-right">
                    <a href="javascript:;" title="Minimizar" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" title="Fechar" class="fa fa-times"></a>
                </span>
            </header>
            <div class="panel-body">
                <div id="graph-donut"></div>
            </div>
        </section>
    </div>
    <div class="col-sm-6">
        <section class="panel">
            <header class="panel-heading">
                Top Clientes</header>
            <div class="panel-body">
                <div id="pdonutContainer"></div>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Relatório Diário
                <span class="tools pull-right">
                    <a href="javascript:;" title="Minimizar" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" title="Fechar" class="fa fa-times"></a>
                </span>
            </header>
            <div class="panel-body">
                <div id="visitors-chart">
                    <div id="visitors-container" style="width: 100%;height:300px; text-align: center; margin:0 auto;">
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!--main content end-->
<?php new sidebar(); ?>
<!-- Placed js at the end of the document so the pages load faster -->

<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<!-- Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!-- Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>

<!-- jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>
<script src="js/flot-chart/jquery.flot.selection.js"></script>
<script src="js/flot-chart/jquery.flot.stack.js"></script>
<script src="js/flot-chart/jquery.flot.time.js"></script>
<script src="js/flot-chart/jquery.flot.animator.min.js"></script>

<!--Morris Chart-->
<script src="js/morris-chart/morris.js"></script>
<script src="js/morris-chart/raphael-min.js"></script>


<!-- Common script init for all pages-->
<script src="js/scripts.js"></script>
<?php new FinanceModel('InventoryControl'); ?>
<?php new FinanceModel('GetServices'); ?>
<?php new FinanceModel('GetProducts'); ?>
<?php new FinanceModel('Report'); ?>