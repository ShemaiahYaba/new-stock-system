<?php
/**
 * KZinc: Stock from Coil Controller
 * Converts KZinc meter stock (factory use) into a new KZinc pallet/bundle/piece stock entry.
 * Atomic: both the meter deduction and new pallet entry happen in one transaction.
 */

session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/coil.php';
require_once __DIR__ . '/../../../../models/stock_entry.php';
require_once __DIR__ . '/../../../../models/stock_ledger.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_KZINC_MANAGEMENT, ACTION_CREATE);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /new-stock-system/index.php?page=kzinc_stock_from_coil');
    exit();
}

if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('error', 'Invalid request.');
    header('Location: /new-stock-system/index.php?page=kzinc_stock_from_coil');
    exit();
}

$coilId          = (int)($_POST['coil_id']          ?? 0);
$sourceEntryId   = (int)($_POST['source_entry_id']  ?? 0);
$metersToConsume = floatval($_POST['meters_to_consume'] ?? 0);
$unitType        = sanitize($_POST['unit_type']     ?? '');
$quantity        = floatval($_POST['quantity']      ?? 0);

$errors = [];
if ($coilId <= 0)           $errors[] = 'Please select a coil.';
if ($sourceEntryId <= 0)    $errors[] = 'Please select a source stock entry.';
if ($metersToConsume <= 0)  $errors[] = 'Meters to consume must be greater than 0.';
if (!array_key_exists($unitType, KZINC_UNITS)) $errors[] = 'Invalid unit type.';
if ($quantity <= 0)         $errors[] = 'Quantity must be greater than 0.';

if (!empty($errors)) {
    setFlashMessage('error', implode(' ', $errors));
    header('Location: /new-stock-system/index.php?page=kzinc_stock_from_coil');
    exit();
}

$coilModel       = new Coil();
$stockEntryModel = new StockEntry();
$ledgerModel     = new StockLedger();
$currentUser     = getCurrentUser();

$coil = $coilModel->findById($coilId);
if (!$coil || strtolower($coil['category']) !== STOCK_CATEGORY_KZINC) {
    setFlashMessage('error', 'Coil not found or is not a K-Zinc coil.');
    header('Location: /new-stock-system/index.php?page=kzinc_stock_from_coil');
    exit();
}

$sourceEntry = $stockEntryModel->findById($sourceEntryId);
if (!$sourceEntry || (int)$sourceEntry['coil_id'] !== $coilId) {
    setFlashMessage('error', 'Source stock entry not found or does not belong to this coil.');
    header('Location: /new-stock-system/index.php?page=kzinc_stock_from_coil');
    exit();
}

if ((float)$sourceEntry['meters_remaining'] < $metersToConsume) {
    setFlashMessage('error', sprintf(
        'Insufficient meters: need %.2f m, only %.2f m remaining on entry #%d.',
        $metersToConsume,
        (float)$sourceEntry['meters_remaining'],
        $sourceEntryId
    ));
    header('Location: /new-stock-system/index.php?page=kzinc_stock_from_coil');
    exit();
}

// Compute resulting pieces
$palletSize  = (int)($coil['pallet_size'] ?? 0);
switch ($unitType) {
    case STOCK_UNIT_PALLETS:
        if ($palletSize <= 0) {
            setFlashMessage('error', 'This coil has no pallet size configured. Edit the coil first.');
            header('Location: /new-stock-system/index.php?page=kzinc_stock_from_coil');
            exit();
        }
        $piecesTotal = (int)($quantity * $palletSize * KZINC_PIECES_PER_BUNDLE);
        break;
    case STOCK_UNIT_BUNDLES:
        $piecesTotal = (int)($quantity * KZINC_PIECES_PER_BUNDLE);
        break;
    default: // pieces
        $piecesTotal = (int)$quantity;
}

$bundlesTotal = (int)($piecesTotal / KZINC_PIECES_PER_BUNDLE);

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    // 1. Deduct meters from the source entry
    $newMetersRemaining = round((float)$sourceEntry['meters_remaining'] - $metersToConsume, 4);
    $updateOk = $stockEntryModel->update($sourceEntryId, [
        'meters_remaining' => $newMetersRemaining,
    ]);
    if (!$updateOk) {
        throw new Exception("Failed to deduct meters from stock entry #$sourceEntryId.");
    }

    // 2. Log meter outflow to stock_ledger (factory use)
    $db->prepare(
        "INSERT INTO stock_ledger
         (coil_id, stock_entry_id, transaction_type, description,
          inflow_meters, outflow_meters, balance_meters,
          inflow_pieces, outflow_pieces, balance_pieces,
          inflow_bundles, outflow_bundles, balance_bundles,
          reference_type, reference_id, created_by, created_at)
         VALUES
         (:coil_id, :entry_id, 'outflow', :desc,
          0, :outflow_m, :balance_m,
          0, 0, 0, 0, 0, 0,
          'stock_entry', :ref_id, :created_by, NOW())"
    )->execute([
        ':coil_id'   => $coilId,
        ':entry_id'  => $sourceEntryId,
        ':desc'      => "Factory use: {$metersToConsume}m cut from entry #{$sourceEntryId} for K-Zinc pallet production",
        ':outflow_m' => $metersToConsume,
        ':balance_m' => $newMetersRemaining,
        ':ref_id'    => $sourceEntryId,
        ':created_by'=> $currentUser['id'],
    ]);

    // 3. Create new KZinc pallet stock entry
    $newEntryId = $stockEntryModel->create([
        'coil_id'          => $coilId,
        'meters'           => 0,
        'weight_kg'        => null,
        'weight_kg_remaining' => null,
        'unit_type'        => $unitType,
        'quantity'         => $quantity,
        'pieces_total'     => $piecesTotal,
        'pieces_remaining' => $piecesTotal,
        'created_by'       => $currentUser['id'],
    ]);
    if (!$newEntryId) {
        throw new Exception('Failed to create new K-Zinc pallet stock entry.');
    }

    // 4. Log pallet inflow to stock_ledger
    $description = "Produced from coil entry #{$sourceEntryId} ({$metersToConsume}m): {$quantity} {$unitType} ({$piecesTotal} pcs / {$bundlesTotal} bundles)";
    $ledgerModel->recordKzincInflow($coilId, $newEntryId, $piecesTotal, $bundlesTotal, $description, $currentUser['id']);

    // 5. Update coil status
    $stockEntryModel->checkAndUpdateCoilStatus($coilId);

    $db->commit();

    logActivity('KZinc stock from coil', "Coil: {$coil['code']}, consumed {$metersToConsume}m → {$quantity} {$unitType} ({$piecesTotal} pcs)");
    setFlashMessage('success', "Converted {$metersToConsume}m → {$quantity} {$unitType} ({$piecesTotal} pcs). New stock entry #$newEntryId created.");
    header('Location: /new-stock-system/index.php?page=kzinc_stock');
    exit();

} catch (Exception $e) {
    $db->rollBack();
    error_log('KZinc from_coil error: ' . $e->getMessage());
    setFlashMessage('error', 'Conversion failed: ' . $e->getMessage());
    header('Location: /new-stock-system/index.php?page=kzinc_stock_from_coil');
    exit();
}
