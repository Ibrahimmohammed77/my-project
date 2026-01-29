<?php
require 'vendor/autoload.php';

echo "Testing Photo model photo_id() resolution...\n";

try {
    $p = new App\Models\Photo;
    echo "Instance created.\n";
    $p->photo_id();
    echo "Method photo_id() called successfully.\n";
} catch (Throwable $e) {
    echo "ERROR CAUGHT: " . $e->getMessage() . "\n";
    echo "TRACE:\n" . $e->getTraceAsString() . "\n";
}
