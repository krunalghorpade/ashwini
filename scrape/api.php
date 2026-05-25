<?php
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (isset($data['url'])) {
    $url = escapeshellarg($data['url']);
    
    // Execute python script
    $command = "python3 fetch_comments.py $url 2>&1";
    $output = shell_exec($command);
    
    // Attempt to decode python JSON output
    $result = json_decode($output, true);
    
    if (json_last_error() === JSON_ERROR_NONE && isset($result['success'])) {
        echo json_encode([
            'status' => 'success',
            'comments' => $result['comments']
        ]);
    } else {
        $errMsg = isset($result['error']) ? $result['error'] : 'Unknown error or Instagram blocked the request (Login Required).';
        if (!$result) {
            $errMsg .= " Output: " . $output;
        }
        echo json_encode([
            'status' => 'error',
            'message' => $errMsg
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No URL provided']);
}
?>
