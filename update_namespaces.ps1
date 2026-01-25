
$mappings = @{
    "Shared" = @("LookupMaster", "LookupValue", "Setting", "DailyStat", "ActivityLog");
    "Identity" = @("Account", "Role", "Permission");
    "Core" = @("Studio", "School", "Subscriber", "Customer", "Office");
    "Finance" = @("Plan", "Subscription", "Invoice", "InvoiceItem", "Payment", "Commission");
    "Media" = @("Album", "Photo", "StorageAccount");
    "Access" = @("Card", "CardGroup");
    "Communications" = @("Notification");
}

function Update-Namespace ($path, $domain, $type) {
    $files = Get-ChildItem -Path $path -Filter "*.php"
    foreach ($file in $files) {
        $content = Get-Content $file.FullName -Raw
        $newNamespace = "namespace App\Domain\$domain\$type;"
        if ($content -match "namespace App\\Models;") {
            $content = $content -replace "namespace App\\Models;", $newNamespace
        }
        elseif ($content -match "namespace App\\Observers;") {
             $content = $content -replace "namespace App\\Observers;", $newNamespace
        }
        Set-Content -Path $file.FullName -Value $content
        Write-Host "Updated namespace for $($file.Name)"
    }
}

# 1. Update Declarations in Moved Files
foreach ($domain in $mappings.Keys) {
    $modelsPath = "app/Domain/$domain/Models"
    if (Test-Path $modelsPath) {
        Update-Namespace $modelsPath $domain "Models"
    }
     
    # Observers
    $observersPath = "app/Domain/$domain/Observers"
    if (Test-Path $observersPath) {
        Update-Namespace $observersPath $domain "Observers"
    }
}

# 2. Update Usage References Globally
$allFiles = Get-ChildItem -Path "app", "database" -Recurse -Filter "*.php"

foreach ($file in $allFiles) {
    $content = Get-Content $file.FullName -Raw
    $originalContent = $content

    foreach ($domain in $mappings.Keys) {
        foreach ($class in $mappings[$domain]) {
            # Update Models
            $content = $content -replace "App\\Models\\$class", "App\Domain\$domain\Models\$class"
            
            # Update Observers (AccountObserver, etc)
            $observerClass = "${class}Observer"
            if ($content -match "App\\Observers\\$observerClass") {
                 $content = $content -replace "App\\Observers\\$observerClass", "App\Domain\$domain\Observers\$observerClass"
            }
        }
    }
    
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content
        Write-Host "Updated references in $($file.Name)"
    }
}
