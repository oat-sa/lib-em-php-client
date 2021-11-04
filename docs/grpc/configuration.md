# Fetching Configuration through gRPC

- Interface: [ConfigurationRepositoryInterface](../../src/Repository/ConfigurationRepositoryInterface.php)
- gRPC implementation: [ConfigurationRepository](../../src/Grpc/ConfigurationRepository.php)

```php
<?php

use OAT\Library\EnvironmentManagementClient\Repository\ConfigurationRepositoryInterface;
use OAT\Library\EnvironmentManagementClient\Grpc\ConfigurationRepository;

class MyService {
    /** @var ConfigurationRepositoryInterface  */
    private $configurationRepository;
    
    public function __construct(ConfigurationRepositoryInterface $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }
    
    public function myMethod(): void
    {
        //...
        
        $configuration = $this->configurationRepository->find(new TenantId('t1'), 'conf-1');
        
        //...
        
        $configCollection = $this->configurationRepository->findAll(new TenantId('t1'));
        
        $configCollection->isEmpty();
        $configCollection->has('conf-1');
        $configCollection->get('conf-1');
        $configCollection->all();
        
        //...
    }
}

$myService = new MyService(new ConfigurationRepository());
$myService->myMethod();
```
