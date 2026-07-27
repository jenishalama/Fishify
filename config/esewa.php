<?php
/**
 * eSewa ePay v2 Configuration & Helper Functions
 * Fishify e-Commerce Integration
 */

// eSewa Sandbox Credentials & API Endpoints
define('ESEWA_PRODUCT_CODE', 'EPAYTEST');
define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');
define('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form');
define('ESEWA_STATUS_URL', 'https://rc.esewa.com.np/api/epay/transaction/status/');

/**
 * Generate Base64 HMAC-SHA256 signature for eSewa v2
 * 
 * @param float|string $total_amount
 * @param string $transaction_uuid
 * @param string $product_code
 * @return string Base64 encoded HMAC-SHA256 signature
 */
function generateEsewaSignature($total_amount, $transaction_uuid, $product_code = ESEWA_PRODUCT_CODE) {
    // Standard eSewa v2 signature format: total_amount=100,transaction_uuid=11-201-13,product_code=EPAYTEST
    // Ensure amount formatting matches exact value sent to eSewa (e.g. integer or string formatting without unnecessary padding)
    $formatted_amount = is_numeric($total_amount) ? (string) round((float)$total_amount, 2) : (string)$total_amount;
    // Strip trailing .00 if standard number string is preferred, or format standard
    // eSewa expects total_amount string to match the form field total_amount
    $message = "total_amount=" . $formatted_amount . ",transaction_uuid=" . $transaction_uuid . ",product_code=" . $product_code;
    
    $secret = ESEWA_SECRET_KEY;
    $s = hash_hmac('sha256', $message, $secret, true);
    return base64_encode($s);
}

/**
 * Verify payment status with official eSewa Status Check API
 * 
 * @param string $product_code
 * @param float|string $total_amount
 * @param string $transaction_uuid
 * @return array Response payload array with status and details
 */
function checkEsewaStatus($product_code, $total_amount, $transaction_uuid) {
    $formatted_amount = is_numeric($total_amount) ? (string) round((float)$total_amount, 2) : (string)$total_amount;
    $query = http_build_query([
        'product_code' => $product_code,
        'total_amount' => $formatted_amount,
        'transaction_uuid' => $transaction_uuid
    ]);
    
    $url = ESEWA_STATUS_URL . '?' . $query;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev environment compatibility
    
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($response === false || !empty($curl_error)) {
        return [
            'success' => false,
            'status' => 'ERROR',
            'message' => 'Network error connecting to eSewa Status API: ' . $curl_error
        ];
    }
    
    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        return [
            'success' => false,
            'status' => 'INVALID_RESPONSE',
            'message' => 'Invalid JSON response received from eSewa Status API.'
        ];
    }
    
    return [
        'success' => true,
        'status' => $decoded['status'] ?? 'UNKNOWN',
        'ref_id' => $decoded['ref_id'] ?? ($decoded['transaction_code'] ?? null),
        'raw' => $decoded
    ];
}

/**
 * Get base URL for dynamic redirect construction
 * 
 * @return string
 */
function getAppBaseUrl() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Trim trailing slashes from path directory
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = dirname(dirname($script)); // relative to pages/ or root
    if ($dir === '/' || $dir === '\\') {
        $dir = '';
    }
    return $scheme . '://' . $host . $dir;
}
