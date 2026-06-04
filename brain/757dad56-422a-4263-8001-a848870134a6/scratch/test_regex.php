<?php
$sql = file_get_contents('c:\\xampp2\\htdocs\\absensi-qrcode2\\database\\seeders\\data.sql');
preg_match_all('/INSERT INTO `([^`]+)` \((.*?)\) VALUES\s*(.*?);/is', $sql, $matches, PREG_SET_ORDER);

$allowed_tables = ['users', 'shifts', 'barcodes', 'absensis'];
$queries = [];

foreach ($matches as $match) {
    $table = $match[1];
    if (in_array($table, $allowed_tables)) {
        echo "Found insert for: $table\n";
    }
}
