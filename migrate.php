<?php
require_once 'database.php';

function migrateCSVtoSQLite($csvFile, $dbFile) {
    echo "Migrating $csvFile to $dbFile...<br>";
    
    if (!file_exists($csvFile)) {
        echo "CSV file $csvFile does not exist. Skipping.<br><br>";
        return;
    }

    try {
        $pdo = new PDO("sqlite:" . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Ensure table exists in this specific DB
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

        $handle = fopen($csvFile, "r");
        if ($handle !== FALSE) {
            $headers = fgetcsv($handle, 1000, ",");
            $insertQuery = "INSERT INTO submissions (timestamp, full_name, dob, location, email, contact_number, insta_id, message, consent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($insertQuery);

            $count = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) == count($headers)) {
                    $stmt->execute($data);
                    $count++;
                }
            }
            fclose($handle);
            echo "Successfully migrated $count records from $csvFile to $dbFile.<br><br>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "<br><br>";
    }
}

// Migrate Production DB
migrateCSVtoSQLite('submissions.csv', __DIR__ . '/submissions.sqlite');

// Migrate Local DB
migrateCSVtoSQLite('submissions_local.csv', __DIR__ . '/submissions_local.sqlite');

echo "Migration completed.";
?>
