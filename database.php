<?php
function getDBConnection() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        $dbFile = __DIR__ . '/submissions_local.sqlite';
    } else {
        $dbFile = __DIR__ . '/submissions.sqlite';
    }

    try {
        $pdo = new PDO("sqlite:" . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create table if not exists
        $createTableQuery = "CREATE TABLE IF NOT EXISTS submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            timestamp DATETIME,
            full_name TEXT,
            dob TEXT,
            location TEXT,
            email TEXT,
            contact_number TEXT,
            insta_id TEXT,
            message TEXT,
            consent TEXT
        )";
        $pdo->exec($createTableQuery);
        
        return $pdo;
    } catch (PDOException $e) {
        // Log the error and fail gracefully
        error_log("Database error: " . $e->getMessage());
        return null;
    }
}
?>
