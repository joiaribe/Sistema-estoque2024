<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;

// loaded menu dashboard
new menu($filename);
// load breadcrumb
$names = array(
    'Ferramentas' => array(
        'link' => NULL,
        'icon' => NULL
    ),
    'Galeria' => array(
        'link' => 'Manager/',
        'icon' => NULL
    )
);
// loaded breadcrumb
new breadcrumb($filename, $names);
new GaleryModel('checks_actions');
?>

<!-- page start-->
<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Galeria de Imagens
                <span class="tools pull-right">
                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" class="fa fa-times"></a>
                </span>
            </header>
            <div class="panel-body">

                <?php new GaleryModel('albuns'); ?>
                <form id="broadcast_delete" action="<?php echo URL . FILENAME; ?>/delete_all" method="post">
                    <div class="btn-group pull-right">
                        <?php
                        if (Developer\Tools\Url::getURL(3) == 'del_mode') {
                            echo '<div id="select"><button id="select-all" type="button" class="btn btn-white btn-sm"><i class="fa fa-check-square-o"></i> Selecionar Todos</button></div>'
                            . '<div id="unselect"><button id="unselect-all" type="button" class="btn btn-white btn-sm"><i class="fa fa-square-o"></i> Deselecionar Todos</button></div>';
                            echo '<button type="submit" title="Clique aqui para deletar os selecionados" class="btn btn-white btn-sm"><i class="fa fa-trash-o"></i> Deletar Todos</button>';
                        } else {
                            echo '<a href="' . URL . FILENAME . '/del_mode" title="Clique aqui para entra no modo de exclusão" type="button" class="btn btn-white btn-sm"><i class="fa fa-trash-o"></i> Deletar</a>';
                        }
                        ?>
                        <a href="#ManagerCategory"  data-toggle="modal" type="button" class="btn btn-white btn-sm"><i class="fa fa-folder-open"></i> Categorias</a>


                    </div>
                    <a href="#UploadNewFile"  data-toggle="modal" type="button" class="btn pull-right btn-sm"><i class="fa fa-upload"></i> Enviar Imagem</a>

                    <div id="gallery" class="media-gal">

                        <?php new GaleryModel('imagens_itens'); ?>

                    </div>
                </form>
                <?php new GaleryModel('LoopImagensModals'); ?>
                <!-- Modal -->
                <div class="modal fade" id="UploadNewFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Enviar nova foto</h4>
                            </div>
                            <form id="upload" method="post" action="<?php echo URL; ?>application/models/dashboard/Tools/Gallery/GalleryUpload.php" enctype="multipart/form-data">
                                <div class="modal-body row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label> Categoria :</label>
                                            <select id="e1" name="cat" class="populate " style="width: 40%;">
                                                <?php new GaleryModel('LoopCategoryItem'); ?>
                                            </select>
                                            <input type="hidden" value="<?php echo Session::get('user_id') ?>" name="id_user"/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="drop" style="border:2px dotted #0B85A1;">
                                            Solte Aqui
                                            <a>Procurar</a>
                                            <input type="file" name="upl" multiple />
                                            <ul style="padding-top: 40px;">
                                                <!-- The file uploads will be shown here -->
                                            </ul>
                                        </div>

                                    </div>


                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- modal -->

                <?php new GaleryModel('ModalCategory'); ?>

            </div>
        </section>
    </div>
</div>
<!-- page end-->
</section>
</section>

<link href="js/mini-upload-form/assets/css/bucketmin.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<link href="css/glyphicons.css" rel="stylesheet" />
<link href="css/estilo.css" rel="stylesheet" />

<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>

<script src="js/jquery.isotope.js"></script>

<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>

<script src="js/mini-upload-form/assets/js/jquery.knob.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="js/jquery-multi-select/js/jquery.quicksearch.js"></script>

<!-- jQuery File Upload Dependencies -->
<script src="js/mini-upload-form/assets/js/jquery.ui.widget.js"></script>
<script src="js/mini-upload-form/assets/js/jquery.iframe-transport.js"></script>
<script src="js/mini-upload-form/assets/js/jquery.fileupload.js"></script>

<!-- Our main JS file -->
<script src="js/mini-upload-form/assets/js/script.js"></script>

<script src="js/select2/select2.js"></script>
<script src="js/select-init.js"></script>

<!--common script init for all pages-->
<script src="js/scripts.js"></script>

<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<link rel="stylesheet" type="text/css" href="js/jquery-multi-select/css/multi-select.css" />


<script type="text/javascript">
    $(function () {
        var $container = $('#gallery');
        $container.isotope({
            itemSelector: '.item',
            animationOptions: {
                duration: 750,
                easing: 'linear',
                queue: false
            }
        });

        // filter items when filter link is clicked
        $('#filters a').click(function () {
            var selector = $(this).attr('data-filter');
            $container.isotope({filter: selector});
            return false;
        });
    });
</script>
<script type="text/javascript">
    $("#unselect").hide();
    $("#select-all").click(function (event) {
        // Iterate each checkbox
        $(":checkbox").each(function () {
            this.checked = true;
        });
        $("#select").hide();
        $("#unselect").show();
    });

    $("#unselect-all").click(function (event) {
        $(":checkbox").each(function () {
            this.checked = false;
        });
        $("#unselect").hide();
        $("#select").show();
    });
</script>
</body>
</html>