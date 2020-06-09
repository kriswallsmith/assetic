Building and Dumping Assets
---------------------------

The is the simplest approach to using Assetic. It involves two steps:

 1. Create a PHP script in your web directory that uses the Assetic OOP API to
    create and output an asset.
 2. Reference that file from your template.

For example, you could create a file in your web directory at
`assets/javascripts.php` with the following code:
```php
<?php

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\JsMinFilter;

$js = new AssetCollection(array(
    new FileAsset(__DIR__.'/jquery.js'),
    new FileAsset(__DIR__.'/application.js'),
), array(
    new JsMinFilter(),
));

header('Content-Type: application/js');
echo $js->dump();
```

In your HTML template you would include this generated Javascript using a
simple `<script>` tag:
```html
<script src="/assets/javascripts.php"></script>
```

Next: [Basic Concepts](concepts.md)
