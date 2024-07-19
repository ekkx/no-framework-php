<?php

declare(strict_types=1);

$db = new \PDO("sqlite:" . __DIR__ . "/database.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = file_get_contents(__DIR__ . "/init.sql");

try {
    $db->exec($sql);
    echo "Database migrated successfully!\n";
    exit(0);
} catch (\Exception $e) {
    echo $e->getMessage();
    exit(1);
}
