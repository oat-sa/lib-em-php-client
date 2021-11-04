# Fetching LTI Registration through gRPC

- Interface: [LtiRegistrationRepositoryInterface](../../src/Repository/LtiRegistrationRepositoryInterface.php)
- gRPC implementation: [LtiRegistrationRepository](../../src/Grpc/LtiRegistrationRepository.php)

```php
<?php

use OAT\Library\EnvironmentManagementClient\Repository\LtiRegistrationRepositoryInterface;
use OAT\Library\EnvironmentManagementClient\Grpc\LtiRegistrationRepository;

class MyService {
    /** @var LtiRegistrationRepositoryInterface  */
    private $ltiRegistrationRepository;
    
    public function __construct(LtiRegistrationRepositoryInterface $ltiRegistrationRepository)
    {
        $this->ltiRegistrationRepository = $ltiRegistrationRepository;
    }
    
    public function myMethod(): void
    {
        //...
        
        $registration = $this->ltiRegistrationRepository->find(new TenantId('t1'), 'reg-1');
        
        //...
        
        $ltiRegistrationCollection = $this->ltiRegistrationRepository->findAll(
            new TenantId('t1'),
            'client-id',
            'platform-iss', 
            'tool-iss'
        );
        
        $ltiRegistrationCollection->isEmpty();
        $ltiRegistrationCollection->has('reg-1');
        $ltiRegistrationCollection->get('reg-1');
        $ltiRegistrationCollection->all();
        
        //...
    }
}

$myService = new MyService(new LtiRegistrationRepository());
$myService->myMethod();
```
