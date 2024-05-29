<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;

$names = array(
    'Configuração' => array(
        'link' => NULL,
        'icon' => NULL
    ),
    'Configurações Acessos' => array(
        'link' => 'Settings/access',
        'icon' => NULL
    )
);
new menu($filename);
new breadcrumb($filename, $names);
new GlobalAccess('actions');
?>




<!--tab nav start-->
<section class="panel">
    <div class="panel-body">
        <?php new GlobalAccess('loadElement'); ?>  
    </div>
</section>
<!--tab nav start-->



<link rel="stylesheet" href="css/bootstrap-switch.css" />

<!-- Placed js at the end of the document so the pages load faster -->
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


<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<script src="js/toggle-init.js"></script>