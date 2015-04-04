# LRouter
## Version: 0.1
===
Simple PHP router.
===
###Usage
```php
<?php
$router = new \LRouter\Router();

$route = new \LRouter\Route('/foo-bar/', 'GET', function () {
    return 'foo bar';
});
// If you are setting this up in a subdirectory, add a base path
$route->setBasePath('/base-path');

// Initializes the routing
$router->route();
```
