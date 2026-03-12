<?php
/**
 * Stock Entry Update Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }

    $entryId  = (int)($_POST['id'] ?? 0);
    $unitType = sanitize($_POST['unit_type'] ?? 'meters');

    $stockEntryModel = new StockEntry();
    $entry = $stockEntryModel->findById($entryId);

    if (!$entry) {
        setFlashMessage('error', 'Stock entry not found.');
        header('Location: /new-stock-system/index.php?page=stock_entries');
        exit();
    }

    $isKzinc = $unitType !== STOCK_UNIT_METERS;

    if ($isKzinc) {
        // -------------------------------------------------------
        // KZinc path: update quantity and recompute pieces
        // -------------------------------------------------------
        $quantity = floatval($_POST['quantity'] ?? 0);

        if ($quantity <= 0) {
            setFlashMessage('error', 'Quantity must be greater than 0.');
            header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
            exit();
        }

        // Compute new pieces_total from the new quantity
        $palletSize = 0; // Only needed for pallets; load from coil if necessary
        if ($unitType === STOCK_UNIT_PALLETS) {
            require_once __DIR__ . '/../../../models/coil.php';
            $coilModel = new Coil();
            $coil = $coilModel->findById($entry['coil_id']);
            $palletSize = (int)($coil['pallet_size'] ?? 0);
            if ($palletSize <= 0) {
                setFlashMessage('error', 'Coil has no pallet size set. Edit the coil first.');
                header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
                exit();
            }
            $newPiecesTotal = (int)($quantity * $palletSize * KZINC_PIECES_PER_BUNDLE);
        } elseif ($unitType === STOCK_UNIT_BUNDLES) {
            $newPiecesTotal = (int)($quantity * KZINC_PIECES_PER_BUNDLE);
        } else {
            $newPiecesTotal = (int)$quantity;
        }

        // Pieces already consumed = pieces_total - pieces_remaining
        $piecesUsed = (int)($entry['pieces_total'] ?? 0) - (int)($entry['pieces_remaining'] ?? 0);

        if ($newPiecesTotal < $piecesUsed) {
            setFlashMessage('error', "Cannot reduce total pieces below the amount already sold ($piecesUsed pcs).");
            header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
            exit();
        }

        $data = [
            'quantity'         => $quantity,
            'pieces_total'     => $newPiecesTotal,
            'pieces_remaining' => $newPiecesTotal - $piecesUsed,
        ];

        if ($stockEntryModel->update($entryId, $data)) {
            $stockEntryModel->checkAndUpdateCoilStatus($entry['coil_id']);
            logActivity('Stock entry updated', "Entry ID: $entryId, Quantity: $quantity $unitType, Pieces: $newPiecesTotal");
            setFlashMessage('success', 'Stock entry updated successfully!');
            header('Location: /new-stock-system/index.php?page=stock_entries');
        } else {
            setFlashMessage('error', 'Failed to update stock entry.');
            header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
        }
        exit();
    } else {
        // -------------------------------------------------------
        // Non-KZinc path: update meters (existing behaviour)
        // -------------------------------------------------------
        $meters   = floatval($_POST['meters'] ?? 0);
        $weightKg = isset($_POST['weight_kg']) && $_POST['weight_kg'] !== '' ? floatval($_POST['weight_kg']) : null;

        if ($meters <= 0) {
            setFlashMessage('error', 'Meters must be greater than 0.');
            header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
            exit();
        }

        $metersUsed = $entry['meters'] - $entry['meters_remaining'];

        if ($meters < $metersUsed) {
            setFlashMessage('error', 'Total meters cannot be less than meters already used (' . number_format($metersUsed, 2) . 'm).');
            header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
            exit();
        }

        $newRemaining = $meters - $metersUsed;

        $data = [
            'meters'           => $meters,
            'meters_remaining' => $newRemaining,
        ];

        if ($weightKg !== null) {
            if (isset($entry['weight_kg']) && $entry['weight_kg'] > 0) {
                $weightRatio          = $entry['weight_kg_remaining'] / $entry['weight_kg'];
                $newRemainingWeight   = $weightKg * $weightRatio;
            } else {
                $newRemainingWeight = $weightKg;
            }
            $data['weight_kg']           = $weightKg;
            $data['weight_kg_remaining'] = $newRemainingWeight;
        }

        if ($stockEntryModel->update($entryId, $data)) {
            $stockEntryModel->checkAndUpdateCoilStatus($entry['coil_id']);
            logActivity('Stock entry updated', "Entry ID: $entryId, New meters: $meters");
            setFlashMessage('success', 'Stock entry updated successfully!');
            header('Location: /new-stock-system/index.php?page=stock_entries');
        } else {
            setFlashMessage('error', 'Failed to update stock entry.');
            header("Location: /new-stock-system/index.php?page=stock_entries_edit&id=$entryId");
        }
        exit();
    }
}

header('Location: /new-stock-system/index.php?page=stock_entries');
exit();
