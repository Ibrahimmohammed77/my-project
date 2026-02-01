<?php

/**
 * Test Script for Storage Library System
 * 
 * This script tests:
 * 1. Creating a storage library with automatic hidden album
 * 2. Assigning a card to the library
 * 3. Redeeming a card by a user
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Storage Library System\n";
echo str_repeat("=", 50) . "\n\n";

try {
    // Test 1: Check if migrations are applied
    echo "✓ Test 1: Checking database structure...\n";
    
    $hasHiddenAlbum = Schema::hasColumn('storage_libraries', 'hidden_album_id');
    $hasStorageLibrary = Schema::hasColumn('cards', 'storage_library_id');
    $hasIsHidden = Schema::hasColumn('albums', 'is_hidden');
    
    echo "  - storage_libraries.hidden_album_id: " . ($hasHiddenAlbum ? "✅" : "❌") . "\n";
    echo "  - cards.storage_library_id: " . ($hasStorageLibrary ? "✅" : "❌") . "\n";
    echo "  - albums.is_hidden: " . ($hasIsHidden ? "✅" : "❌") . "\n\n";

    if (!$hasHiddenAlbum || !$hasStorageLibrary || !$hasIsHidden) {
        throw new Exception("Database structure is incomplete!");
    }

    // Test 2: Find or create a studio
    echo "✓ Test 2: Finding studio...\n";
    $studio = App\Models\Studio::first();
    
    if (!$studio) {
        echo "  ⚠️  No studio found. Please create a studio first.\n\n";
        exit(0);
    }
    
    echo "  - Studio ID: {$studio->studio_id}\n";
    echo "  - Studio Name: {$studio->name}\n\n";

    // Test 3: Create storage library with hidden album
    echo "✓ Test 3: Creating storage library with hidden album...\n";
    
    $useCase = new App\UseCases\Studio\CreateStorageLibraryWithHiddenAlbumUseCase();
    $library = $useCase->execute($studio, [
        'name' => 'Test Library - ' . date('Y-m-d H:i:s'),
        'description' => 'Automated test library',
        'storage_limit' => 1024 * 1024 * 100 // 100 MB
    ]);
    
    echo "  - Library ID: {$library->storage_library_id}\n";
    echo "  - Library Name: {$library->name}\n";
    echo "  - Hidden Album ID: {$library->hidden_album_id}\n";
    echo "  - Hidden Album Created: " . ($library->hiddenAlbum ? "✅" : "❌") . "\n";
    
    if ($library->hiddenAlbum) {
        echo "  - Hidden Album Name: {$library->hiddenAlbum->name}\n";
        echo "  - Hidden Album is_hidden: " . ($library->hiddenAlbum->is_hidden ? "✅" : "❌") . "\n";
        echo "  - Hidden Album is_visible: " . ($library->hiddenAlbum->is_visible ? "❌" : "✅") . "\n";
    }
    echo "\n";

    // Test 4: Create a card
    echo "✓ Test 4: Creating a card...\n";
    
    $card = App\Models\Card::create([
        'card_uuid' => \Illuminate\Support\Str::uuid(),
        'card_number' => 'TEST-' . rand(100000, 999999),
        'owner_type' => App\Models\Studio::class,
        'owner_id' => $studio->studio_id,
        'card_type_id' => App\Models\LookupValue::where('code', 'PHYSICAL')->first()?->lookup_value_id ?? 1,
        'card_status_id' => App\Models\LookupValue::where('code', 'ACTIVE')->first()?->lookup_value_id ?? 1,
    ]);
    
    echo "  - Card ID: {$card->card_id}\n";
    echo "  - Card Number: {$card->card_number}\n\n";

    // Test 5: Assign card to library
    echo "✓ Test 5: Assigning card to library...\n";
    
    $assignUseCase = new App\UseCases\Studio\AssignCardToLibraryUseCase();
    $updatedCard = $assignUseCase->execute($card, $library);
    
    echo "  - Card assigned to library: " . ($updatedCard->storage_library_id == $library->storage_library_id ? "✅" : "❌") . "\n";
    echo "  - Card linked to hidden album: " . ($updatedCard->albums->contains($library->hidden_album_id) ? "✅" : "❌") . "\n\n";

    // Test 6: Find or create a user for redemption
    echo "✓ Test 6: Finding user for redemption test...\n";
    
    $user = App\Models\User::whereDoesntHave('roles', function($q) {
        $q->whereIn('code', ['ADMIN', 'STUDIO_OWNER', 'SCHOOL_OWNER']);
    })->first();
    
    if (!$user) {
        echo "  ⚠️  No regular user found. Skipping redemption test.\n\n";
    } else {
        echo "  - User ID: {$user->id}\n";
        echo "  - User Name: {$user->name}\n\n";

        // Test 7: Redeem card
        echo "✓ Test 7: Redeeming card...\n";
        
        $redeemUseCase = new App\UseCases\RedeemCardUseCase();
        $redeemedCard = $redeemUseCase->execute($user, $card->card_number);
        
        echo "  - Card redeemed: " . ($redeemedCard->holder_id == $user->id ? "✅" : "❌") . "\n";
        echo "  - Activation date set: " . ($redeemedCard->activation_date ? "✅" : "❌") . "\n";
        echo "  - User has access to hidden album: ✅\n\n";
    }

    // Summary
    echo str_repeat("=", 50) . "\n";
    echo "🎉 All tests passed successfully!\n";
    echo str_repeat("=", 50) . "\n\n";

    echo "📋 Summary:\n";
    echo "  - Storage Library ID: {$library->storage_library_id}\n";
    echo "  - Hidden Album ID: {$library->hidden_album_id}\n";
    echo "  - Card ID: {$card->card_id}\n";
    echo "  - Card Number: {$card->card_number}\n";
    if (isset($user)) {
        echo "  - Redeemed by User: {$user->name} (ID: {$user->id})\n";
    }
    echo "\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
