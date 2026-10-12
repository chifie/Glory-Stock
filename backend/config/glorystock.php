<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GloryStock Settings
    |--------------------------------------------------------------------------
    */

    // Admin registration security key (legacy register.php: PRO-99-SECURE).
    'admin_key' => env('GLORYSTOCK_ADMIN_KEY', 'PRO-99-SECURE'),

    // Stock at or below this quantity is considered low (legacy dashboards).
    'low_stock_threshold' => 5,

    // Store contact details shown on receipts.
    'store_name' => 'GloryStock',
    'store_phone' => '0617008046',
    'store_city' => 'Dar es Salaam',
];
