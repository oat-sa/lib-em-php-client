# Tenant Id Extractor

- Interface: [TenantIdExtractorInterface](../src/Http/TenantIdExtractorInterface.php)
- Default implementation: [TenantIdHeaderExtractor](../src/Http/TenantIdHeaderExtractor.php) - extracts the Tenant Id from the headers
  of an HTTP Message

```php
<?php

use OAT\Library\EnvironmentManagementClient\Http\TenantIdExtractorInterface;
use OAT\Library\EnvironmentManagementClient\Http\TenantIdExtractor;
use OAT\Library\EnvironmentManagementClient\Exception\TenantIdNotFoundException;
use OAT\Library\EnvironmentManagementClient\Model\TenantId;
use Psr\Http\Message\MessageInterface;

class MyService {
    /** @var TenantIdExtractorInterface  */
    private $tenantIdExtractor;
    
    public function __construct(TenantIdExtractorInterface $tenantIdExtractor)
    {
        $this->tenantIdExtractor = $tenantIdExtractor;
    }
    
    public function myMethod(): void
    {
        //...
        
        /** @var MessageInterface $message */
        $message = ...
        
        try {
            /** @var TenantId $tenantId */
            $tenantId = $this->tenantIdExtractor->extract($message);
            
            //...
        } catch (TenantIdNotFoundException $exception) {
            //...
        }
        
        //...
    }
}

$myService = new MyService(new TenantIdExtractor());
$myService->myMethod();
```
