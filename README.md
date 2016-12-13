# DatabaseBundle

LAG DatabaseBundle is a bundle to ease database manipulation in Symfony environment.
 It provides command to load and backup database.
 
## Installation

Install via composer :

```
    composer require larriereguichet/database-bundle
```

Enable the bundle :

```php

    use Symfony\Component\HttpKernel\Kernel;
    use Symfony\Component\Config\Loader\LoaderInterface;
    
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
                // ...
                new LAG\DatabaseBundle\LAGDatabaseBundle(),

```
