<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// loaded menu dashboard
new menu($filename);

$b = array(
    'Movimentar' => array('link' => false, 'icon' => false),
    'Venda Serviços' => array('link' => 'Mov/services', 'icon' => false),
    'Checkout Serviços' => NULL,
);
new breadcrumb($filename, $b);
// actions
new CheckoutServicesModel('action');
