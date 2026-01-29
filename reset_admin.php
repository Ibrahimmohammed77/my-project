<?php

use Illuminate\Support\Facades\Hash;
use App\Domain\Identity\Models\Account;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = Account::where('username', 'admin')->first();

if ($admin) {
    echo "Found admin account: " . $admin->email . "\n";
    $password = 'password';
    $admin->password_hash = Hash::make($password);
    $admin->save();
    echo "Password reset to: 'password'\n";
    
    // Verify immediate check
    $check = Hash::check($password, $admin->password_hash);
    echo "Immediate verification: " . ($check ? "SUCCESS" : "FAILED") . "\n";
} else {
    echo "Admin account not found!\n";
}
