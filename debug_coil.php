<?php
/**
 * DEBUG COILS VIEW - Critical Analysis
 * Temporary file to diagnose the exact issue
 * Save as: debug_coils.php in root directory
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/models/coil.php';
require_once __DIR__ . '/utils/helpers.php';

echo "<h1>🔍 CRITICAL DEBUG: Coils View</h1>";
echo "<hr>";

// Test 1: Check if constants exist
echo "<h2>Test 1: Check Constants</h2>";
echo "<p><strong>STOCK_STATUSES defined:</strong> " . (defined('STOCK_STATUS_AVAILABLE') ? '✅ YES' : '❌ NO') . "</p>";
if (defined('STOCK_STATUS_AVAILABLE')) {
    echo "<pre>";
    print_r(STOCK_STATUSES);
    echo "</pre>";
}

// Test 2: Check getStatusBadgeClass function
echo "<h2>Test 2: Check Helper Function</h2>";
if (function_exists('getStatusBadgeClass')) {
    echo "<p>✅ getStatusBadgeClass() exists</p>";
    echo "<p>Test: getStatusBadgeClass('available') = " . getStatusBadgeClass('available') . "</p>";
    echo "<p>Test: getStatusBadgeClass('factory_use') = " . getStatusBadgeClass('factory_use') . "</p>";
} else {
    echo "<p>❌ getStatusBadgeClass() NOT FOUND</p>";
}

// Test 3: Check formatDate function
echo "<h2>Test 3: Check Date Formatter</h2>";
if (function_exists('formatDate')) {
    echo "<p>✅ formatDate() exists</p>";
    echo "<p>Test: formatDate('2025-11-05 21:11:59') = " . formatDate('2025-11-05 21:11:59') . "</p>";
} else {
    echo "<p>❌ formatDate() NOT FOUND</p>";
}

// Test 4: Fetch actual coil data
echo "<h2>Test 4: Fetch Coil Data</h2>";
$coilModel = new Coil();
$coils = $coilModel->getAll(null, 5, 0);

echo "<p><strong>Coils fetched:</strong> " . count($coils) . "</p>";

if (!empty($coils)) {
    echo "<h3>First Coil Raw Data:</h3>";
    echo "<pre>";
    print_r($coils[0]);
    echo "</pre>";
    
    // Test 5: Check specific fields
    echo "<h2>Test 5: Check Field Values</h2>";
    $firstCoil = $coils[0];
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Value</th><th>Status</th></tr>";
    
    // Check status field
    echo "<tr>";
    echo "<td><strong>status</strong></td>";
    echo "<td>" . ($firstCoil['status'] ?? 'NULL') . "</td>";
    echo "<td>" . (isset($firstCoil['status']) ? '✅ EXISTS' : '❌ MISSING') . "</td>";
    echo "</tr>";
    
    // Check created_at field
    echo "<tr>";
    echo "<td><strong>created_at</strong></td>";
    echo "<td>" . ($firstCoil['created_at'] ?? 'NULL') . "</td>";
    echo "<td>" . (isset($firstCoil['created_at']) ? '✅ EXISTS' : '❌ MISSING') . "</td>";
    echo "</tr>";
    
    // Check id field
    echo "<tr>";
    echo "<td><strong>id</strong></td>";
    echo "<td>" . ($firstCoil['id'] ?? 'NULL') . "</td>";
    echo "<td>" . (isset($firstCoil['id']) ? '✅ EXISTS' : '❌ MISSING') . "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    // Test 6: Render status badge
    echo "<h2>Test 6: Render Status Badge</h2>";
    if (isset($firstCoil['status'])) {
        $statusBadgeClass = getStatusBadgeClass($firstCoil['status']);
        $statusText = STOCK_STATUSES[$firstCoil['status']] ?? ucfirst($firstCoil['status']);
        
        echo "<p>Status value: <code>{$firstCoil['status']}</code></p>";
        echo "<p>Badge class: <code>{$statusBadgeClass}</code></p>";
        echo "<p>Status text: <code>{$statusText}</code></p>";
        echo "<p>Rendered: <span class='badge {$statusBadgeClass}'>{$statusText}</span></p>";
    }
    
    // Test 7: Render formatted date
    echo "<h2>Test 7: Render Formatted Date</h2>";
    if (isset($firstCoil['created_at'])) {
        $formattedDate = formatDate($firstCoil['created_at']);
        echo "<p>Raw date: <code>{$firstCoil['created_at']}</code></p>";
        echo "<p>Formatted: <code>{$formattedDate}</code></p>";
    }
    
    // Test 8: Test table rendering
    echo "<h2>Test 8: Simulate Table Row</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<thead style='background: #f0f0f0;'>";
    echo "<tr>";
    echo "<th>Code</th>";
    echo "<th>Name</th>";
    echo "<th>Status</th>";
    echo "<th>Created At</th>";
    echo "<th>Actions</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach (array_slice($coils, 0, 3) as $coil) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($coil['code']) . "</td>";
        echo "<td>" . htmlspecialchars($coil['name']) . "</td>";
        
        // Status column
        echo "<td>";
        if (isset($coil['status'])) {
            $badgeClass = getStatusBadgeClass($coil['status']);
            $statusText = STOCK_STATUSES[$coil['status']] ?? ucfirst($coil['status']);
            echo "<span class='badge {$badgeClass}'>{$statusText}</span>";
        } else {
            echo "<span style='color:red;'>❌ NO STATUS</span>";
        }
        echo "</td>";
        
        // Created At column
        echo "<td>";
        if (isset($coil['created_at'])) {
            echo formatDate($coil['created_at']);
        } else {
            echo "<span style='color:red;'>❌ NO DATE</span>";
        }
        echo "</td>";
        
        // Actions column
        echo "<td>";
        echo "<a href='#' class='btn btn-sm btn-info'>View</a> ";
        echo "<a href='#' class='btn btn-sm btn-warning'>Edit</a> ";
        echo "<button class='btn btn-sm btn-danger'>Delete</button>";
        echo "</td>";
        
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
}

// Test 9: Check permission functions
echo "<h2>Test 9: Check Permission Functions</h2>";
session_start();
if (function_exists('hasPermission')) {
    echo "<p>✅ hasPermission() exists</p>";
    if (defined('MODULE_STOCK_MANAGEMENT') && defined('ACTION_VIEW')) {
        $hasView = hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW);
        echo "<p>hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW) = " . ($hasView ? '✅ TRUE' : '❌ FALSE') . "</p>";
    }
} else {
    echo "<p>❌ hasPermission() NOT FOUND</p>";
}

// Test 10: Check quick_action_buttons.php file
echo "<h2>Test 10: Check Quick Action Buttons File</h2>";
$quickButtonsPath = __DIR__ . '/layout/quick_action_buttons.php';
if (file_exists($quickButtonsPath)) {
    echo "<p>✅ File exists: {$quickButtonsPath}</p>";
    
    // Try to include it
    echo "<h3>Testing include:</h3>";
    $id = 1;
    $module = 'coils';
    $canView = true;
    $canEdit = true;
    $canDelete = true;
    
    ob_start();
    include $quickButtonsPath;
    $buttonOutput = ob_get_clean();
    
    if (!empty($buttonOutput)) {
        echo "<p>✅ Quick buttons rendered:</p>";
        echo "<div style='background: #f0f0f0; padding: 10px;'>{$buttonOutput}</div>";
    } else {
        echo "<p>❌ Quick buttons produced NO OUTPUT</p>";
        echo "<p>File contents:</p>";
        echo "<pre>" . htmlspecialchars(file_get_contents($quickButtonsPath)) . "</pre>";
    }
} else {
    echo "<p>❌ File NOT FOUND: {$quickButtonsPath}</p>";
}

echo "<hr>";
echo "<h2>📋 Summary</h2>";
echo "<p>If any tests show ❌, that's where the problem is!</p>";
echo "<p><a href='/new-stock-system/index.php?page=coils'>Back to Coils List</a></p>";

?>

<!-- Bootstrap CSS for testing -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">