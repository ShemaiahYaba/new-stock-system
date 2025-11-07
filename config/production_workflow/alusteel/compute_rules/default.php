<?php
class AlusteelComputeRules
{
    public static function computeRowTotal($row)
    {
        $meters = $row['sheet_qty'] * $row['sheet_meter'];
        $subtotal = $meters * ($row['unit_price'] ?? 0);

        return [
            'meters' => $meters,
            'subtotal' => $subtotal,
        ];
    }

    public static function computeGrandTotal($rows)
    {
        $totalMeters = 0;
        $totalAmount = 0;

        foreach ($rows as $row) {
            $computed = self::computeRowTotal($row);
            $totalMeters += $computed['meters'];
            $totalAmount += $computed['subtotal'];
        }

        return [
            'total_meters' => $totalMeters,
            'total_amount' => $totalAmount,
        ];
    }
}
