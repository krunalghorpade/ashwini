<?php
require_once __DIR__ . '/../database.php';
$pdo = getDBConnection();

if (!$pdo) {
    die("Database connection failed");
}

$stmt = $pdo->query("SELECT * FROM submissions ORDER BY timestamp DESC");
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=ashwini_submissions_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

if (count($submissions) > 0) {
    // Write headers
    fputcsv($output, array_keys($submissions[0]), ",", "\"", "\\");
    
    // Write rows
    foreach ($submissions as $row) {
        fputcsv($output, $row, ",", "\"", "\\");
    }
}

fclose($output);
exit();
?>
