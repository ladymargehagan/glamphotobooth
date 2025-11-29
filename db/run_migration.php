<?php
/**
 * Database Migration Runner
 * Run this file once to add order_id column to pb_reviews table
 */

require_once __DIR__ . '/../settings/db_cred.php';

// Connect to database
$conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected to database successfully\n";

// Check if column already exists
$check_query = "SHOW COLUMNS FROM pb_reviews LIKE 'order_id'";
$result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($result) > 0) {
    echo "Column 'order_id' already exists in pb_reviews table. No migration needed.\n";
} else {
    echo "Adding 'order_id' column to pb_reviews table...\n";

    // Add order_id column
    $alter_query = "ALTER TABLE pb_reviews ADD COLUMN order_id INT DEFAULT NULL AFTER booking_id";

    if (mysqli_query($conn, $alter_query)) {
        echo "✓ Successfully added 'order_id' column\n";

        // Add index for performance
        $index_query = "ALTER TABLE pb_reviews ADD INDEX idx_order_provider (order_id, provider_id)";

        if (mysqli_query($conn, $index_query)) {
            echo "✓ Successfully added index on (order_id, provider_id)\n";
        } else {
            echo "✗ Failed to add index: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "✗ Failed to add column: " . mysqli_error($conn) . "\n";
    }
}

mysqli_close($conn);
echo "\nMigration complete!\n";
?>
