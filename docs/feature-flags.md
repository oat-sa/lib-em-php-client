# Feature Flags Extractor

- Interface: [FeatureFlagExtractorInterface](../src/Http/FeatureFlagExtractorInterface.php)
- Default implementation: [FeatureFlagHeaderExtractor](../src/Http/FeatureFlagHeaderExtractor.php) - extracts any Feature Flag from the headers
  of an HTTP Message

```php
<?php

use OAT\Library\EnvironmentManagementClient\Http\FeatureFlagExtractorInterface;
use OAT\Library\EnvironmentManagementClient\Http\FeatureFlagHeaderExtractor;
use OAT\Library\EnvironmentManagementClient\Model\FeatureFlag;
use Psr\Http\Message\MessageInterface;

class MyService {
    /** @var FeatureFlagExtractorInterface  */
    private $featureFlagExtractor;
    
    public function __construct(FeatureFlagExtractorInterface $featureFlagExtractor)
    {
        $this->featureFlagExtractor = $featureFlagExtractor;
    }
    
    public function myMethod(): void
    {
        //...
        
        /** @var MessageInterface $message */
        $message = ...
        
        $flagCollection = $this->featureFlagExtractor->extract($message);
        
        $flagCollection->isEmpty();
        $flagCollection->has('flag-1');
        
        /** @var FeatureFlag $flag */
        $flag = $flagCollection->get('flag-1');
        $flag->getName();
        $flag->getValue();
        
        $flagCollection->all();
        
        //...
    }
}

$myService = new MyService(new FeatureFlagHeaderExtractor());
$myService->myMethod();
```
