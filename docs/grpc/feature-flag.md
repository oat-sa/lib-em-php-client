# Fetching Feature Flag through gRPC

- Interface: [FeatureFlagRepositoryInterface](../../src/Repository/FeatureFlagRepositoryInterface.php)
- gRPC implementation: [FeatureFlagRepository](../../src/Grpc/FeatureFlagRepository.php)

```php
<?php

use OAT\Library\EnvironmentManagementClient\Repository\FeatureFlagRepositoryInterface;
use OAT\Library\EnvironmentManagementClient\Grpc\FeatureFlagRepository;

class MyService {
    /** @var FeatureFlagRepositoryInterface  */
    private $featureFlagRepository;
    
    public function __construct(FeatureFlagRepositoryInterface $featureFlagRepository)
    {
        $this->featureFlagRepository = $featureFlagRepository;
    }
    
    public function myMethod(): void
    {
        //...
        
        $flag = $this->featureFlagRepository->find(new TenantId('t1'), 'flag-1');
        
        //...
        
        $flagCollection = $this->featureFlagRepository->findAll(new TenantId('t1'));
        
        $flagCollection->isEmpty();
        $flagCollection->has('flag-1');
        $flagCollection->get('flag-1');
        $flagCollection->all();
        
        //...
    }
}

$myService = new MyService(new FeatureFlagRepository());
$myService->myMethod();
```
