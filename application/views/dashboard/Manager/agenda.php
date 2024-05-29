<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// loaded menu dashboard
new menu($filename);
// loaded breadcrumb
new AgendaModel('loaded');  // used param for load class only here :P
new sidebar(); // load sidebar
