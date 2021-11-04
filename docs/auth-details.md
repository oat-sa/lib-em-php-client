# Request Authorization Details

Using [AuthorizationDetailsHeaderMarker](../src/Http/AuthorizationDetailsHeaderMarker.php), there is a way to set a special header
on any PSR-7 Response which is an indicator for Envoy to add authorization information to the response before forwarding.

```php
<?php

use OAT\Library\EnvironmentManagementClient\Http\AuthorizationDetailsMarkerInterface;
use OAT\Library\EnvironmentManagementClient\Http\AuthorizationDetailsHeaderMarker;
use Psr\Http\Message\ResponseInterface;

class MyService {
    /** @var AuthorizationDetailsMarkerInterface  */
    private $authorizationDetailsMarker;
    
    public function __construct(AuthorizationDetailsMarkerInterface $authorizationDetailsMarker)
    {
        $this->authorizationDetailsMarker = $authorizationDetailsMarker;
    }
    
    public function myMethod(): void
    {
        //...
        
        /** @var ResponseInterface $response */
        $response = ...
        
        $response = $this->authorizationDetailsMarker->withAuthDetails($response);
        
        //...
    }
}

$myService = new MyService(new AuthorizationDetailsHeaderMarker());
$myService->myMethod();
```
