<?php
// ========================================
// File: config/production_workflow/kzinc/properties/selectScraps.php
// ========================================
return [
    'id' => 'scraps',
    'label' => 'Scraps',
    'input_type' => 'quantity', // Simple quantity input
    'unit' => 'scrap(s)',
    'price' => 2500, // Fixed price
    'price_editable' => false, // Price cannot be changed
    'description' => 'KZINC scraps at ₦2,500 per scrap',
    'compute' => [
        'formula' => 'quantity * price',
        'result_field' => 'subtotal',
    ],
];
