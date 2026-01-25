
$modelMap = @{
    "LookupMaster" = "App\Domain\Shared\Models\LookupMaster";
    "LookupValue" = "App\Domain\Shared\Models\LookupValue";
    "Setting" = "App\Domain\Shared\Models\Setting";
    "DailyStat" = "App\Domain\Shared\Models\DailyStat";
    "ActivityLog" = "App\Domain\Shared\Models\ActivityLog";

    "Account" = "App\Domain\Identity\Models\Account";
    "Role" = "App\Domain\Identity\Models\Role";
    "Permission" = "App\Domain\Identity\Models\Permission";

    "Studio" = "App\Domain\Core\Models\Studio";
    "School" = "App\Domain\Core\Models\School";
    "Subscriber" = "App\Domain\Core\Models\Subscriber";
    "Customer" = "App\Domain\Core\Models\Customer";
    "Office" = "App\Domain\Core\Models\Office";

    "Plan" = "App\Domain\Finance\Models\Plan";
    "Subscription" = "App\Domain\Finance\Models\Subscription";
    "Invoice" = "App\Domain\Finance\Models\Invoice";
    "InvoiceItem" = "App\Domain\Finance\Models\InvoiceItem";
    "Payment" = "App\Domain\Finance\Models\Payment";
    "Commission" = "App\Domain\Finance\Models\Commission";

    "Album" = "App\Domain\Media\Models\Album";
    "Photo" = "App\Domain\Media\Models\Photo";
    "StorageAccount" = "App\Domain\Media\Models\StorageAccount";

    "Card" = "App\Domain\Access\Models\Card";
    "CardGroup" = "App\Domain\Access\Models\CardGroup";

    "Notification" = "App\Domain\Communications\Models\Notification";
}

$files = Get-ChildItem -Path "app/Domain" -Recurse -Filter "*.php"

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $originalContent = $content
    
    # Get current namespace of the file
    $currentNamespace = ""
    if ($content -match "namespace\s+([\w\\]+);") {
        $currentNamespace = $matches[1]
    }

    foreach ($modelName in $modelMap.Keys) {
        $fqcn = $modelMap[$modelName]
        $modelNamespace = $fqcn.Substring(0, $fqcn.LastIndexOf("\"))
        
        # Only replace if model is in a DIFFERENT namespace
        if ($modelNamespace -ne $currentNamespace) {
            # Regex to match 'ModelName::class' but not '\App\...\ModelName::class'
            # Lookbehind is hard in basic regex, so we'll match and check
            
            # Simple replace:  " ModelName::class" -> " \FQCN::class"
            # We add a backslash to make it absolute global
            
            $pattern = "(?<![\w\\])$modelName::class"
            $replacement = "\$fqcn::class"
            
            $content = $content -replace $pattern, $replacement
        }
    }
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content
        Write-Host "Fixed imports in $($file.Name)"
    }
}
