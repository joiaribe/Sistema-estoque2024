<!-- echo out the system feedback (error and success messages) -->
<?php
$this->renderFeedbackMessages();

use Dashboard\menu as menu;

// load menu
new menu($filename);
// load page notifier
new DoubtsModel('loaded'); // used param for load class only here :P

