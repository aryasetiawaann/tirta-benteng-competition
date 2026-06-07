<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;

$path = __DIR__ . '/../manual/manual-form.xlsx';
if (!file_exists($path)) {
    echo "Error: {$path} not found.\n";
    exit(1);
}

$spreadsheet = IOFactory::load($path);

$infoKlub = $spreadsheet->getSheetByName('Info Klub');
$infoKlub->removeTableByName('TableInfoKlub');
$infoKlub->addTable(new Table('A1:E2', 'TableInfoKlub'));

$inputAtlet = $spreadsheet->getSheetByName('Input Atlet');
$inputAtlet->removeTableByName('TableInputAtlet');
$inputAtlet->addTable(new Table('A1:N500', 'TableInputAtlet'));

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save($path);
echo "Done.\n";
