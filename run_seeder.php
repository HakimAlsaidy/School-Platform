<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Database\Seeders\DatabaseSeeder;

echo "Starting seeder manually...\n";

try {
    $seeder = new DatabaseSeeder();
    // حقن الـ command للطباعة
    $seeder->setContainer($app);
    $seeder->run();
    echo "Seeder completed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
