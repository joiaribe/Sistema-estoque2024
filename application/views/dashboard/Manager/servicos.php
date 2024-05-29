<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// loaded menu dashboard
new menu($filename);
// loaded breadcrumb
new ServicoModel('loaded');  // used param for load class only here :P

?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#valor").change(function()){ 
            alert(this.value);
        });
    });
</script>