# Assetic - Fork for BlackCat CMS v2

Assetic is an asset management framework for PHP.

Please visit https://github.com/kriswallsmith/assetic if you are not going to
use this with BlackCat CMS v2!

# Fork

Changes in this fork:

New Filters
-----------

 * `CATCssRewriteFilter`: Rewrites CSS URLs to assets folder of current site
 * `CATDebugAddPathInfoFilter`: For debugging
 * `CATSourcemapFilter`: Find sourcemappingurl and add it to CAT Assets Helper
 
Please note that these filters cannot be used without BlackCat CMS v2! But you may use them to create your own filters.

Other
-----

 * Assetic/AssetWriter.php: Catch static::write() exceptions
 * Assetic/Factory/AssetFactory.php: FIX for empty extension; ADDED HttpAssetWithProxy
 * Assetic/Asset/HttpAssetWithProxy.php: ADDED - Load assets from http if you're behind a proxy

