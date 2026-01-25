
# Map Context -> Models to generate layers for
$contextMap = @{
    "Identity"       = @("Account", "Role");
    "Core"           = @("Studio", "School", "Subscriber", "Customer", "Office");
    "Finance"        = @("Plan", "Subscription", "Invoice", "Payment", "Commission");
    "Media"          = @("Album", "Photo", "StorageAccount");
    "Access"         = @("Card", "CardGroup");
    "Communications" = @("Notification");
    "Shared"         = @("LookupValue");
}

function Create-Files ($domain, $model) {
    $basePath = "app/Domain/$domain"
    
    # 1. Repository Interface
    $repoInterfacePath = "$basePath/Repositories/Contracts/${model}RepositoryInterface.php"
    $repoInterfaceContent = @"
<?php

namespace App\Domain\\$domain\Repositories\Contracts;

use App\Domain\Shared\Repositories\Contracts\BaseRepositoryInterface;

interface ${model}RepositoryInterface extends BaseRepositoryInterface
{
    // Add custom repository methods here
}
"@
    Set-Content -Path $repoInterfacePath -Value $repoInterfaceContent

    # 2. Repository Implementation
    $repoImplPath = "$basePath/Repositories/Eloquent/${model}Repository.php"
    $repoImplContent = @"
<?php

namespace App\Domain\\$domain\Repositories\Eloquent;

use App\Domain\Shared\Repositories\Eloquent\BaseRepository;
use App\Domain\\$domain\Repositories\Contracts\\${model}RepositoryInterface;
use App\Domain\\$domain\Models\\$model;

class ${model}Repository extends BaseRepository implements ${model}RepositoryInterface
{
    public function __construct($model \$model)
    {
        parent::__construct(\$model);
    }
}
"@
    Set-Content -Path $repoImplPath -Value $repoImplContent

    # 3. Service
    $servicePath = "$basePath/Services/${model}Service.php"
    $serviceContent = @"
<?php

namespace App\Domain\\$domain\Services;

use App\Domain\\$domain\Repositories\Contracts\\${model}RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ${model}Service
{
    protected \$repository;

    public function __construct(${model}RepositoryInterface \$repository)
    {
        \$this->repository = \$repository;
    }

    public function getAll(): Collection
    {
        return \$this->repository->all();
    }

    public function create(array \$data): Model
    {
        return \$this->repository->create(\$data);
    }

    public function update(\$id, array \$data): bool
    {
        return \$this->repository->update(\$id, \$data);
    }
    
    public function delete(\$id): bool
    {
        return \$this->repository->delete(\$id);
    }
    
    public function find(\$id): ?Model
    {
        return \$this->repository->find(\$id);
    }
}
"@
    Set-Content -Path $servicePath -Value $serviceContent

    # 4. Store Request (Basic)
    $requestPath = "$basePath/Requests/Store${model}Request.php"
    $requestContent = @"
<?php

namespace App\Domain\\$domain\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store${model}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Define validation rules for creating $model
        ];
    }
}
"@
    Set-Content -Path $requestPath -Value $requestContent
    
    Write-Host "Generated layers for $model in $domain"
}

# Execute
foreach ($domain in $contextMap.Keys) {
    foreach ($model in $contextMap[$domain]) {
        Create-Files $domain $model
    }
}
