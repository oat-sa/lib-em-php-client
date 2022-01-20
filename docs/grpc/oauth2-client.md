# Fetching OAuth2 Client through gRPC

- Interface: [OAuth2ClientRepositoryInterface](../../src/Repository/OAuth2ClientRepositoryInterface.php)
- gRPC implementation: [OAuth2ClientRepository](../../src/Grpc/OAuth2ClientRepository.php)

```php
<?php

use OAT\Library\EnvironmentManagementClient\Repository\OAuth2ClientRepositoryInterface;
use OAT\Library\EnvironmentManagementClient\Grpc\OAuth2ClientRepository;

class MyService {
    /** @var OAuth2ClientRepositoryInterface  */
    private $oAuth2ClientRepository;
    
    public function __construct(OAuth2ClientRepositoryInterface $oAuth2ClientRepository)
    {
        $this->oAuth2ClientRepository = $oAuth2ClientRepository;
    }
    
    public function myMethod(): void
    {
        //...
        
        $oAuth2Client = $this->oAuth2ClientRepository->find('client-1');
        
        //...
    }
}

$myService = new MyService(new OAuth2ClientRepository());
$myService->myMethod();
```
