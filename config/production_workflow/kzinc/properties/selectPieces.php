
<?php // ========================================
// File: config/production_workflow/kzinc/properties/selectPieces.php
// ========================================
return [
    'id' => 'pieces',
    'label' => 'Pieces',
    'input_type' => 'quantity',
    'unit' => 'piece(s)',
    'price' => 4500, // Fixed price
    'price_editable' => false,
    'description' => 'KZINC pieces at ₦4,500 per piece',
    'compute' => [
        'formula' => 'quantity * price',
        'result_field' => 'subtotal',
    ],
];
