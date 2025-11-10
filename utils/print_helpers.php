<?php
/**
 * Print Helpers
 * Utility functions for handling printing functionality
 */

/**
 * Get the base URL for the application
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    return rtrim("$protocol://$host$script", '/');
}

/**
 * Generate print view URL for an invoice
 */
function getPrintUrl($invoiceId) {
    return getBaseUrl() . "/index.php?page=invoice_print&id=" . urlencode($invoiceId) . "&_t=" . time();
}

/**
 * Generate print button HTML with modern styling
 */
function printButton($invoiceId, $options = []) {
    $defaults = [
        'label' => 'Print Invoice',
        'icon' => 'bi-printer',
        'class' => 'btn btn-outline-primary',
        'target' => '_blank',
        'showIcon' => true,
        'data' => []
    ];
    
    $options = array_merge($defaults, $options);
    $dataAttrs = '';
    
    foreach ($options['data'] as $key => $value) {
        $dataAttrs .= sprintf(' data-%s="%s"', htmlspecialchars($key), htmlspecialchars($value));
    }
    
    $icon = $options['showIcon'] ? sprintf('<i class="bi %s me-1"></i>', $options['icon']) : '';
    
    return sprintf(
        '<a href="%s" class="%s" target="%s"%s>%s%s</a>',
        htmlspecialchars(getPrintUrl($invoiceId)),
        htmlspecialchars($options['class']),
        htmlspecialchars($options['target']),
        $dataAttrs,
        $icon,
        htmlspecialchars($options['label'])
    );
}

/**
 * Get print styles
 */
function getPrintStyles() {
    return '<style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-container, .print-container * {
                visibility: visible;
            }
            .print-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            .no-print, .no-print * {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
                margin-top: 2cm;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .print-logo {
            max-height: 70px;
            width: auto;
        }
        .print-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        .print-subtitle {
            color: #7f8c8d;
            margin: 5px 0 0;
            font-size: 14px;
        }
        .print-section {
            margin-bottom: 25px;
        }
        .print-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #3498db;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .print-table th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: 600;
        }
        .print-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 40px auto 0;
            text-align: center;
            padding-top: 5px;
        }
    </style>';
}

/**
 * Get print header HTML
 */
function getPrintHeader($title = 'INVOICE', $subtitle = '') {
    return '
    <div class="print-header">
        <div>
            <h1 class="print-title">' . htmlspecialchars($title) . '</h1>
            ' . (!empty($subtitle) ? '<p class="print-subtitle">' . htmlspecialchars($subtitle) . '</p>' : '') . '
        </div>
        <img src="' . getBaseUrl() . '/assets/logo.png" alt="Company Logo" class="print-logo">
    </div>';
}

/**
 * Get print footer HTML
 */
function getPrintFooter() {
    return '
    <div class="print-footer mt-4 text-center text-muted" style="font-size: 12px;">
        <p>Thank you for your business!</p>
        <p>Generated on ' . date('F j, Y \a\t g:i A') . '</p>
    </div>';
}
?>
