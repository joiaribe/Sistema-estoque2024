<?php if (SYSTEM_DEBUG === true) { ?>
    <!-- echo out the content of the SESSION via KINT, a Composer-loaded much better version of var_dump -->
    <!-- KINT can be used with the simple function d() -->
    <h1> Depuração </h1>
    <?php d($_SESSION);
} ?>
</body>
</html>
