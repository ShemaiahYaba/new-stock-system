<?php
/**
 * KZINC Compute Rules
 * File: config/production_workflow/kzinc/compute_rules/default.php
 *
 * Handles calculations for KZINC products:
 * - Scraps: ₦2,500 per scrap
 * - Pieces: ₦4,500 per piece
 * - Bundles: ₦64,000 per bundle (15 pieces each)
 */

class KzincComputeRules
{
    // Fixed prices
    const PRICE_SCRAP = 2500;
    const PRICE_PIECE = 4500;
    const PRICE_BUNDLE = 64000;
    const PIECES_PER_BUNDLE = 15;

    /**
     * Compute row total based on property type
     *
     * @param array $row Row data with property_type and quantity
     * @return array Computed values (quantity, unit_price, subtotal, pieces)
     */
    public static function computeRowTotal($row)
    {
        $propertyType = $row['property_type'] ?? '';
        $quantity = floatval($row['quantity'] ?? 0);

        $unitPrice = 0;
        $subtotal = 0;
        $pieces = 0; // Track equivalent pieces for inventory

        switch ($propertyType) {
            case 'scraps':
                $unitPrice = self::PRICE_SCRAP;
                $subtotal = $quantity * $unitPrice;
                $pieces = 0; // Scraps don't count as full pieces
                break;

            case 'pieces':
                $unitPrice = self::PRICE_PIECE;
                $subtotal = $quantity * $unitPrice;
                $pieces = $quantity;
                break;

            case 'bundles':
                $unitPrice = self::PRICE_BUNDLE;
                $subtotal = $quantity * $unitPrice;
                $pieces = $quantity * self::PIECES_PER_BUNDLE;
                break;

            default:
                $unitPrice = 0;
                $subtotal = 0;
                $pieces = 0;
        }

        return [
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'pieces' => $pieces, // Total pieces (for bundles)
            'property_type' => $propertyType,
        ];
    }

    /**
     * Compute grand total from multiple rows
     *
     * @param array $rows Array of row data
     * @return array Grand totals
     */
    public static function computeGrandTotal($rows)
    {
        $totalAmount = 0;
        $totalPieces = 0;
        $totalScraps = 0;
        $totalBundles = 0;

        foreach ($rows as $row) {
            $computed = self::computeRowTotal($row);
            $totalAmount += $computed['subtotal'];

            // Track quantities by type
            switch ($computed['property_type']) {
                case 'scraps':
                    $totalScraps += $computed['quantity'];
                    break;
                case 'pieces':
                    $totalPieces += $computed['quantity'];
                    break;
                case 'bundles':
                    $totalBundles += $computed['quantity'];
                    $totalPieces += $computed['pieces']; // Add bundle pieces
                    break;
            }
        }

        return [
            'total_amount' => $totalAmount,
            'total_pieces' => $totalPieces,
            'total_scraps' => $totalScraps,
            'total_bundles' => $totalBundles,
            'summary_text' => self::generateSummaryText($totalBundles, $totalPieces, $totalScraps),
        ];
    }

    /**
     * Generate human-readable summary
     */
    private static function generateSummaryText($bundles, $pieces, $scraps)
    {
        $parts = [];

        if ($bundles > 0) {
            $parts[] = "$bundles bundle(s)";
        }
        if ($pieces > 0) {
            $parts[] = "$pieces piece(s)";
        }
        if ($scraps > 0) {
            $parts[] = "$scraps scrap(s)";
        }

        return !empty($parts) ? implode(', ', $parts) : 'No items';
    }

    /**
     * Get price for a specific property type
     */
    public static function getPriceForType($propertyType)
    {
        switch ($propertyType) {
            case 'scraps':
                return self::PRICE_SCRAP;
            case 'pieces':
                return self::PRICE_PIECE;
            case 'bundles':
                return self::PRICE_BUNDLE;
            default:
                return 0;
        }
    }
}
