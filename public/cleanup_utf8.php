<?php

/**
 * Database UTF-8 Cleanup Script
 * 
 * Fixes corrupted UTF-8 data in the PostgreSQL database by:
 * 1. Connecting with SQL_ASCII encoding to bypass encoding validation
 * 2. Reading all text data
 * 3. Cleaning invalid UTF-8 sequences
 * 4. Writing the cleaned data back
 * 
 * Usage: php public/cleanup_utf8.php
 */

// Bootstrap CI4
require_once __DIR__ . '/../vendor/autoload.php';

// Get DB credentials from .env or Config
$envFile = __DIR__ . '/../.env';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) continue;
    if (!str_contains($line, '=')) continue;
    [$key, $value] = explode('=', $line, 2);
    $env[trim($key)] = trim($value);
}

$host = $env['database.default.hostname'] ?? '127.0.0.1';
$port = $env['database.default.port'] ?? '5432';
$db   = $env['database.default.database'] ?? '';
$user = $env['database.default.username'] ?? '';
$pass = $env['database.default.password'] ?? '';

echo "=== UTF-8 Database Cleanup Script ===\n\n";

// Connect with SQL_ASCII to bypass encoding validation
$connStr = "host={$host} port={$port} dbname={$db} user={$user} password={$pass} options='-c client_encoding=SQL_ASCII'";
$conn = pg_connect($connStr);

if (!$conn) {
    die("ERROR: Could not connect to database.\n");
}

echo "Connected to database successfully (SQL_ASCII mode).\n\n";

/**
 * Clean a string to valid UTF-8
 */
function cleanUtf8String(?string $str): ?string
{
    if ($str === null) return null;
    
    // Replace common problematic bytes
    $str = str_replace("\xC2\xA0", ' ', $str); // UTF-8 NBSP
    $str = str_replace("\xA0", ' ', $str);      // Raw 0xA0
    
    // Use iconv with IGNORE to strip invalid sequences
    $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
    if ($cleaned === false) {
        // Fallback: strip all non-ASCII bytes
        $cleaned = preg_replace('/[\x80-\xFF]/', '', $str);
    }
    
    return $cleaned;
}

// Tables and their text columns to clean
$tablesToClean = [
    'mutasi' => ['status', 'keterangan'],
    'perangkat' => ['nama', 'noreg', 'status', 'keterangan'],
    'users' => ['nama'],
    'spec_perangkat' => ['kode_spec', 'nama_perangkat'],
];

$totalFixed = 0;

foreach ($tablesToClean as $table => $columns) {
    echo "Checking table: {$table}\n";
    
    // Check if table exists
    $checkTable = pg_query($conn, "SELECT to_regclass('{$table}')");
    $row = pg_fetch_row($checkTable);
    if ($row[0] === null) {
        echo "  Table {$table} does not exist, skipping.\n";
        continue;
    }
    
    // Check which columns actually exist
    $existingCols = [];
    foreach ($columns as $col) {
        $checkCol = pg_query($conn, "SELECT column_name FROM information_schema.columns WHERE table_name='{$table}' AND column_name='{$col}'");
        if (pg_num_rows($checkCol) > 0) {
            $existingCols[] = $col;
        }
    }
    
    if (empty($existingCols)) {
        echo "  No matching text columns found, skipping.\n";
        continue;
    }
    
    $selectCols = 'id, ' . implode(', ', $existingCols);
    $result = pg_query($conn, "SELECT {$selectCols} FROM {$table}");
    
    if (!$result) {
        echo "  ERROR reading table: " . pg_last_error($conn) . "\n";
        continue;
    }
    
    $rowCount = 0;
    $fixedCount = 0;
    
    while ($row = pg_fetch_assoc($result)) {
        $rowCount++;
        $updates = [];
        
        foreach ($existingCols as $col) {
            $original = $row[$col];
            if ($original === null) continue;
            
            $cleaned = cleanUtf8String($original);
            
            if ($cleaned !== $original) {
                $updates[$col] = $cleaned;
            }
        }
        
        if (!empty($updates)) {
            $setParts = [];
            $params = [];
            $paramIdx = 1;
            
            foreach ($updates as $col => $val) {
                $setParts[] = "{$col} = \${$paramIdx}";
                $params[] = $val;
                $paramIdx++;
            }
            
            $params[] = $row['id'];
            $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE id = \${$paramIdx}";
            
            // Use pg_query_params for safe parameterized update
            // But first set encoding to UTF8 for the update
            pg_query($conn, "SET client_encoding = 'UTF8'");
            $updateResult = pg_query_params($conn, $sql, $params);
            pg_query($conn, "SET client_encoding = 'SQL_ASCII'");
            
            if ($updateResult) {
                $fixedCount++;
                $colNames = implode(', ', array_keys($updates));
                echo "  Fixed row id={$row['id']} columns: {$colNames}\n";
            } else {
                echo "  ERROR updating row id={$row['id']}: " . pg_last_error($conn) . "\n";
            }
        }
    }
    
    echo "  Scanned {$rowCount} rows, fixed {$fixedCount} rows.\n\n";
    $totalFixed += $fixedCount;
}

pg_close($conn);

echo "=== Cleanup complete! Fixed {$totalFixed} total rows. ===\n";
