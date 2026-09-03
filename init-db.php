#!/usr/bin/env php
<?php
/**
 * Database initialization script for Render deployment
 * Run this once after deploying to set up the database schema
 * 
 * Usage: php init-db.php
 * Or on Render: Add as a "Pre-Deploy Command" in render.yaml
 */

require_once __DIR__ . '/project/includes/config.php';

echo "Initializing database...\n";

try {
    $pdo = getDbConnection();
    echo "Connected to database: " . DB_NAME . " on " . DB_HOST . ":" . DB_PORT . "\n";
    
    // Read the schema file
    $schemaPath = __DIR__ . '/project/database/schema.sql';
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema file not found at: $schemaPath");
    }
    
    $schema = file_get_contents($schemaPath);
    
    // Split by semicolon and execute each statement
    // This is a simple approach - for complex schemas, consider using a proper migration tool
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || str_starts_with($statement, '--')) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (PDOException $e) {
            // Some statements might fail if tables already exist (IF NOT EXISTS handles most)
            if (str_contains($e->getMessage(), 'already exists') || 
                str_contains($e->getMessage(), 'Duplicate entry')) {
                echo "Skipped (already exists): " . substr($statement, 0, 50) . "...\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n✅ Database initialization completed successfully!\n";
    echo "Demo client credentials:\n";
    echo "  Client ID: democlient\n";
    echo "  Password: Demo@12345\n";
    echo "\n⚠️  IMPORTANT: Change or remove the demo account before going to production!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}