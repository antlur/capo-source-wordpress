```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use CapoSourceWordpress\Api;

$api = new Api(env('WP_API_URL'));

$pages = $api->getPages();
```
