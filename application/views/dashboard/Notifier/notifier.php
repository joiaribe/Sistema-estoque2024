<!-- echo out the system feedback (error and success messages) -->
<?php
$this->renderFeedbackMessages();

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// load menu
new menu($filename);
// load page notifier
new notifierModel('loaded'); // used param for load class only here :P
// load sidebar
new sidebar();