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
    
    // Execute the entire schema as a single batch (MySQL supports multi-statement)
    // This avoids issues with semicolon-splitting on multi-line statements
    try {
        $pdo->exec($schema);
        echo "✅ Schema executed successfully\n";
    } catch (PDOException $e) {
        // If multi-statement fails (some MySQL configs disable it), fall back to statement-by-statement
        if (str_contains($e->getMessage(), 'multi') || str_contains($e->getMessage(), 'multiple')) {
            echo "Multi-statement execution not supported, falling back to statement-by-statement...\n";
            executeStatementsIndividually($pdo, $schema);
        } else {
            // Table already exists or duplicate entry - that's OK
            if (str_contains($e->getMessage(), 'already exists') || 
                str_contains($e->getMessage(), 'Duplicate entry')) {
                echo "⚠️  Schema partially exists, checking individual statements...\n";
                executeStatementsIndividually($pdo, $schema);
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

/**
 * Fallback: Execute statements individually with better parsing
 */
function executeStatementsIndividually(PDO $pdo, string $schema): void
{
    // Remove comments and split more carefully
    $lines = explode("\n", $schema);
    $cleanLines = [];
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        // Skip empty lines and comment-only lines
        if ($trimmed === '' || str_starts_with($trimmed, '--')) {
            continue;
        }
        // Remove inline comments
        if (($pos = strpos($trimmed, '--')) !== false) {
            $trimmed = trim(substr($trimmed, 0, $pos));
        }
        if ($trimmed !== '') {
            $cleanLines[] = $trimmed;
        }
    }
    
    // Rejoin and split by semicolon at end of line (more reliable)
    $rejoined = implode(' ', $cleanLines);
    $statements = preg_split('/;\s*$/', $rejoined, -1, PREG_SPLIT_NO_EMPTY);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            echo "  ✓ " . substr($statement, 0, 60) . "...\n";
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'already exists') || 
                str_contains($e->getMessage(), 'Duplicate entry')) {
                echo "  ⊘ Skipped (exists): " . substr($statement, 0, 60) . "...\n";
            } else {
                throw $e;
            }
        }
    }
}