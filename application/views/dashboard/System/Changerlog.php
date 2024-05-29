<?php
$this->renderFeedbackMessages();

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// load menu
new menu($filename);
// load breadcrumb
$names = array(
    'Sistema' => array(
        'link' => NULL,
        'icon' => NULL
    ),
    'Changerlog' => array(
        'link' => 'Manager/',
        'icon' => NULL
    )
);

function Loop_changelog($id) {
    $q = new Query();
    $q
            ->select()
            ->from('ChangeLog')
            ->where_equal_to(
                    array(
                        'id_version' => $id
                    )
            )
            ->order_by('title asc')
            ->run();
    $result = '';
    foreach ($q->get_selected() as $data) {
        $result.= <<<EOF
<div class="fa-hover col-md-12 col-sm-4"><i class="fa fa-check"></i>{$data['title']}</div>
EOF;
    }
    return $result;
}

new breadcrumb($filename, $names);
?>
<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">

                <?php
                $q = new Query();
                $q
                        ->select()
                        ->from('ChangeLog_Version')
                        ->order_by('version desc')
                        ->limit(1)
                        ->run();
                $data = $q->get_selected();
                echo '<strong>Versão Atual : ' .  $data['version'] .' '.$data['subtitle'].'</strong>  - ' . strftime('%d/%m/%Y', strtotime($data['data']));
                ?>
                <span class="tools pull-right">
                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" class="fa fa-times"></a>
                </span>
            </header>
            <div class="panel-body">
                <div class="adv-table">
                    <!--main content start-->
                    <div class="fontawesome-icon-list">
                        <?php
                        $q = new Query();
                        $q
                                ->select()
                                ->from('ChangeLog_Version')
                                ->order_by('version desc')
                                ->run();
                        foreach ($q->get_selected() as $data) {
                            $loop = Loop_changelog($data['id']);
                            $date = strftime('%d/%m/%Y', strtotime($data['data']));
                            echo <<<EOF
    <div id="new">
        <h2 class="page-header"><strong>Versão {$data['version']} {$data['subtitle']}</strong> - <span>{$date}</span></h2>
        <div class="row fontawesome-icon-list">
           {$loop}
        </div>
    </div>
           <br>
EOF;
                        }
                        ?>
                    </div>
                    <!--main content end-->
                </div>
            </div>
        </section><!--main content end-->



        <?php new sidebar(); ?>

        <!-- Placed js at the end of the document so the pages load faster -->
        <!--Core js-->
        <script src="js/jquery.js"></script>
        <script src="bs3/js/bootstrap.min.js"></script>
        <script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
        <script src="js/jquery.scrollTo.min.js"></script>
        <script src="js/easypiechart/jquery.easypiechart.js"></script>
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