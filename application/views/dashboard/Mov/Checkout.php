<?php

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// loaded menu dashboard
new menu($filename);

$b = array(
    'Movimentar' => array('link' => false, 'icon' => false),
    'Venda Produtos' => array('link' => 'Mov/products', 'icon' => false),
    'Checkout Produtos' => NULL,
);
new breadcrumb($filename, $b);
// actions
new CheckoutModel('action');