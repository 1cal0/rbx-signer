<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use RobloxSigner\Signer;

$privateKeyPath = __DIR__ . '/../keys/PrivateKey.pem';
$scriptPath     = __DIR__ . '/../scripts/example.lua';
$era            = 'v2019'; // 'pre_2012', 'v2013', 'v2017', or 'v2019'

$scriptContent = file_get_contents($scriptPath);
if ($scriptContent === false) {
    http_response_code(500);
    exit("Script not found\n");
}

header('Content-Type: text/plain');

try {
    echo Signer::sign($scriptContent, $privateKeyPath, $era);
} catch (\Throwable $e) {
    http_response_code(500);
    echo "Signing error: " . $e->getMessage();
}