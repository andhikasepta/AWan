<?php
/**
 * Quick script to create the nodes and installation_requests tables via PDO.
 * Run once and delete: php run_migrations.php
 */

$host = '123.231.223.6';
$port = '5100';
$db   = 'assetmanage';
$user = 'akp';
$pass = 'S3pt4@';

try {
    $pdo = new PDO("pgsql:host={$host};port={$port};dbname={$db}", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected OK\n";

    // 1. Create nodes table (if not exists)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS nodes (
            id SERIAL PRIMARY KEY,
            arep VARCHAR(100) NOT NULL,
            node_sentral VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table 'nodes' OK\n";

    // Seed only if empty
    $count = $pdo->query("SELECT COUNT(*) FROM nodes")->fetchColumn();
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO nodes (arep, node_sentral) VALUES (?, ?)");
        $seeds = [
            ['Semarang',   'SMGHCPGA01'],
            ['Semarang',   'UNRIDSXR01'],
            ['Semarang',   'SMGHCPHW04'],
            ['Tegal',      'WLRMTIHW01'],
            ['Solo',       'SLGHCPGA01'],
            ['Yogyakarta', 'YKGHCPGA01'],
            ['Purwokerto', 'PWTIDSXR01'],
        ];
        foreach ($seeds as $s) {
            $stmt->execute($s);
        }
        echo "Seeded " . count($seeds) . " nodes\n";
    } else {
        echo "Nodes already has {$count} rows, skipping seed\n";
    }

    // 2. Create installation_requests table (if not exists)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS installation_requests (
            id SERIAL PRIMARY KEY,
            id_mutasi INTEGER NOT NULL REFERENCES mutasi(id) ON DELETE CASCADE,
            arep VARCHAR(100) NOT NULL,
            node_sentral VARCHAR(100) NOT NULL,
            status VARCHAR(20) DEFAULT 'Pending',
            is_read SMALLINT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table 'installation_requests' OK\n";

    echo "\nAll migrations completed successfully!\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
