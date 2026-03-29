<?php
session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_ledger.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE);

/**
 * Convert a KZinc quantity in any unit to total pieces.
 *
 * @param string $unitType  One of STOCK_UNIT_PALLETS / STOCK_UNIT_BUNDLES / STOCK_UNIT_PIECES
 * @param float  $quantity  Amount in that unit
 * @param int    $palletSize Bundles per pallet (from coil record, required for pallets)
 * @return int
 */
function computeKzincPieces(string $unitType, float $quantity, int $palletSize): int
{
    switch ($unitType) {
        case STOCK_UNIT_PALLETS:
            return (int)($quantity * $palletSize * KZINC_PIECES_PER_BUNDLE);
        case STOCK_UNIT_BUNDLES:
            return (int)($quantity * KZINC_PIECES_PER_BUNDLE);
        case STOCK_UNIT_PIECES:
            return (int)$quantity;
        default:
            return 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=stock_entries_create');
        exit();
    }
    
    $coilId = (int)($_POST['coil_id'] ?? 0);

    $errors = [];

    if ($coilId <= 0) $errors[] = 'Please select a coil.';

    if (!empty($errors)) {
        setFlashMessage('error', implode(' ', $errors));
        header('Location: /new-stock-system/index.php?page=stock_entries_create');
        exit();
    }

    // Verify coil exists and load its category + pallet_size
    $coilModel = new Coil();
    $coil = $coilModel->findById($coilId);

    if (!$coil) {
        setFlashMessage('error', 'Coil not found.');
        header('Location: /new-stock-system/index.php?page=stock_entries_create');
        exit();
    }

    // Only treat as KZinc pallet path if the coil is explicitly a pallet/sheet coil.
    // KZinc meter coils go through the regular meters path.
    $isKzinc = $coil['category'] === STOCK_CATEGORY_KZINC
               && ($coil['kzinc_track_mode'] ?? null) === 'pallets';

    if ($isKzinc) {
        // -------------------------------------------------------
        // KZinc path: record in pallets / bundles / pieces
        // -------------------------------------------------------
        $unitType = sanitize($_POST['unit_type'] ?? '');
        $quantity = floatval($_POST['quantity'] ?? 0);

        if (!array_key_exists($unitType, KZINC_UNITS)) $errors[] = 'Please select a valid unit type.';
        if ($quantity <= 0) $errors[] = 'Quantity must be greater than 0.';

        if ($unitType === STOCK_UNIT_PALLETS) {
            $palletSize = (int)($coil['pallet_size'] ?? 0);
            if ($palletSize <= 0) {
                $errors[] = 'This coil has no pallet size configured. Edit the coil first to set it.';
            }
        }

        if (!empty($errors)) {
            setFlashMessage('error', implode(' ', $errors));
            header('Location: /new-stock-system/index.php?page=stock_entries_create');
            exit();
        }

        $palletSize = (int)($coil['pallet_size'] ?? 0);
        $piecesTotal = computeKzincPieces($unitType, $quantity, $palletSize);

        $entryData = [
            'coil_id'          => $coilId,
            'meters'           => 0,
            'weight_kg'        => null,
            'weight_kg_remaining' => null,
            'unit_type'        => $unitType,
            'quantity'         => $quantity,
            'pieces_total'     => $piecesTotal,
            'pieces_remaining' => $piecesTotal,
            'created_by'       => null, // set below
        ];
        $logDetail = "Coil: {$coil['code']}, {$quantity} {$unitType} ({$piecesTotal} pieces)";
    } else {
        // -------------------------------------------------------
        // Non-KZinc path: record in meters (existing behaviour)
        // -------------------------------------------------------
        $meters   = floatval($_POST['meters'] ?? 0);
        $weightKg = floatval($_POST['weight_kg'] ?? 0);

        if ($meters <= 0) $errors[] = 'Meters must be greater than 0.';

        if (!empty($errors)) {
            setFlashMessage('error', implode(' ', $errors));
            header('Location: /new-stock-system/index.php?page=stock_entries_create');
            exit();
        }

        $entryData = [
            'coil_id'             => $coilId,
            'meters'              => $meters,
            'weight_kg'           => $weightKg > 0 ? $weightKg : null,
            'weight_kg_remaining' => $weightKg > 0 ? $weightKg : null,
            'unit_type'           => STOCK_UNIT_METERS,
            'quantity'            => null,
            'pieces_total'        => null,
            'pieces_remaining'    => null,
            'created_by'          => null, // set below
        ];
        $logDetail = "Coil: {$coil['code']}, Meters: $meters";
    }

    try {
        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        $stockEntryModel = new StockEntry();
        $currentUser = getCurrentUser();
        $entryData['created_by'] = $currentUser['id'];

        $entryId = $stockEntryModel->create($entryData);

        if (!$entryId) {
            throw new Exception('Failed to create stock entry.');
        }

        $ledgerModel = new StockLedger();

        if ($isKzinc) {
            // KZinc: log inflow in both pieces and bundles
            $bundlesTotal = (int)($piecesTotal / KZINC_PIECES_PER_BUNDLE);
            $description  = "Stock entry added — {$coil['code']}: {$quantity} {$unitType} ({$piecesTotal} pcs / {$bundlesTotal} bundles)";
            $ledgerModel->recordKzincInflow($coilId, $entryId, $piecesTotal, $bundlesTotal, $description, $currentUser['id']);
        } elseif ($coil['status'] === STOCK_STATUS_FACTORY_USE) {
            // Non-KZinc factory-use: log inflow in meters
            $description = "Stock entry added - {$meters}m for {$coil['code']}";
            $ledgerModel->recordInflow($coilId, $entryId, $meters, $description, $currentUser['id']);
        }

        // Auto-update coil status based on stock entries
        $statusChanged = $stockEntryModel->checkAndUpdateCoilStatus($coilId);

        $db->commit();

        logActivity('Stock entry created', $logDetail);

        if ($statusChanged && $coil['status'] === STOCK_STATUS_OUT_OF_STOCK) {
            setFlashMessage('success', 'Stock entry created successfully! Coil status changed from OUT OF STOCK to AVAILABLE.');
        } else {
            setFlashMessage('success', 'Stock entry created successfully!');
        }

        $allowedRedirects = ['stock_entries', 'kzinc_stock'];
        $redirectTo = in_array($_POST['redirect_to'] ?? '', $allowedRedirects)
            ? sanitize($_POST['redirect_to'])
            : 'stock_entries';

        header("Location: /new-stock-system/index.php?page=$redirectTo");

    } catch (Exception $e) {
        $db->rollBack();
        error_log("Stock entry creation error: " . $e->getMessage());
        setFlashMessage('error', 'Failed to create stock entry: ' . $e->getMessage());
        header('Location: /new-stock-system/index.php?page=stock_entries_create');
    }
    
    exit();
}

header('Location: /new-stock-system/index.php?page=stock_entries_create');
exit();