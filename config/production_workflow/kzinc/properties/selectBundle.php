<?php
// ========================================
// File: config/production_workflow/kzinc/properties/selectBundles.php
// ========================================
return [
    'id' => 'bundles',
    'label' => 'Bundles',
    'input_type' => 'quantity',
    'unit' => 'bundle(s)',
    'price' => 64000, // Fixed price
    'price_editable' => false,
    'pieces_per_bundle' => 15, // Important: Auto-calculate pieces
    'description' => 'KZINC bundles at ₦64,000 per bundle (15 pieces each)',
    'compute' => [
        'formula' => 'quantity * price',
        'result_field' => 'subtotal',
        'pieces_formula' => 'quantity * pieces_per_bundle',
    ],
    'auto_calculate' => [
        'pieces' => 'quantity * 15', // When bundles change, update pieces display
    ],
];
