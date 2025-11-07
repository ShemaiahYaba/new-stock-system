<?php
return [
    'id' => 'mainsheet',
    'label' => 'Mainsheet',
    'input_type' => 'sheets', // sheets|meters|gauge|both
    'price_required' => true,
    'multiple_allowed' => true,
    'compute' => [
        'multiplier_field' => 'sheet_qty',
        'multiplier_value_field' => 'sheet_meter',
        'result_field' => 'meters',
    ],
    'renderer' => 'alusteel/selectMainsheet',
];
