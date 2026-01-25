$files = Get-ChildItem -Path "app\Domain" -Recurse -Filter *.php

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw

    # 1. Fix Namespace and Uses (Double Backslashes)
    # Be careful not to replace legitimate ones if any exist, but here we know "App\Domain\\" is bad.
    # We replace "Domain\\" with "Domain\" globally seems safe given strict context.
    # Also "Contracts\\"
    $content = $content -replace "Domain\\\\", "Domain\"
    $content = $content -replace "Contracts\\\\", "Contracts\"
    
    # 2. Fix Service Class Corruption
    # Check if it's a Service file or looks like one (has "protected \;")
    if ($content -match "protected \\;") {
        # Fix property
        $content = $content -replace "protected \\;", "protected `$repository;"
        
        # Fix constructor argument
        # public function __construct(SomethingRepositoryInterface \)
        $content = $content -replace "public function __construct\((.*) \\\)", "public function __construct(`$1 `$repository)"
        
        # Fix assignment in constructor
        # \->repository = \;
        $content = $content -replace "\\->repository = \\;", "`$this->repository = `$repository;"
        
        # Fix getAll usage (usually \->repository->all())
        $content = $content -replace "\\->repository->all\(\)", "`$this->repository->all()"
        
        # Fix create signature
        # public function create(array \)
        $content = $content -replace "public function create\(array \\\)", "public function create(array `$data)"
        
        # Fix create call
        # \->repository->create(\)
        $content = $content -replace "\\->repository->create\(\\\)", "`$this->repository->create(`$data)"
        
        # Fix update signature
        # public function update(\, array \)
        $content = $content -replace "public function update\(\\, array \\\)", "public function update(`$id, array `$data)"
        
        # Fix update call
        # \->repository->update(\, \)
        $content = $content -replace "\\->repository->update\(\\, \\\)", "`$this->repository->update(`$id, `$data)"
        
        # Fix delete signature
        # public function delete(\)
        $content = $content -replace "public function delete\(\\\)", "public function delete(`$id)"
        
        # Fix delete call
        # \->repository->delete(\)
        $content = $content -replace "\\->repository->delete\(\\\)", "`$this->repository->delete(`$id)"
        
        # Fix find signature
        # public function find(\)
        $content = $content -replace "public function find\(\\\)", "public function find(`$id)"
        
        # Fix find call
        # \->repository->find(\)
        $content = $content -replace "\\->repository->find\(\\\)", "`$this->repository->find(`$id)"
    }

    Set-Content -Path $file.FullName -Value $content -NoNewline
    Write-Host "Processed $($file.Name)"
}
