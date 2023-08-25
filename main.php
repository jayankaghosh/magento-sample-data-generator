<?php

require_once __DIR__ . '/vendor/autoload.php';

$generator = new \MagentoSampleDataGenerator\Generator(__DIR__);
$rows = $generator->generate(intval($argv[1] ?? 10));

$fileName = getcwd() . '/products.csv';
$csvFile = new SplFileObject($fileName, 'w');

foreach ($rows as $key => $row) {
    if ($key === 0) {
        $csvFile->fputcsv(array_keys($row));
    }
    $csvFile->fputcsv($row);
}

echo "\n" . $fileName . "\n\n";