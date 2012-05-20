# ZfAssetic

ZfAssetic is two ViewHelper that helps developper to integrate 
[Assetic](https://github.com/kriswallsmith/assetic) in Zend Framework views.

    <?php
    // In your view...
    $this->cssAsset()->captureStart();
    ?>
    <link rel="stylesheet" type="text/css" href="/media/css/styles.css" />
    <link rel="stylesheet" type="text/css" href="/media/css/box.css" />
    <?php
    $this->cssAsset()->captureStart();
    echo $this->cssAsset();
    // Will output something like this :
    // <link rel="stylesheet" type="text/css" href="/asset/0141522526d6be5302041ffa6093933b.css" />
    ?>

## Install

 1. Add the directory ZfAssetic to your include\_path
 1. Use a psr-0 compatible autoloader. Add the namespace "ZfAssetic" to your
    autoloader
 1. Register the classes ZfAssetic\_ViewHelper\_CssAsset and 
    ZfAssetic\_ViewHelper\_ScriptAsset in your view.

## Configuration

To use these helpers, you have to create an Assetic AssetFactory that manages
filters. The factory can then be added directly to the helper using the method
`setAssetFactory()` or by setting the key AssetFactory to the Zend\_Registry :

    <?php
    $factory = new Assetic\Factory\AssetFactory('/path/where/files/to/be/filtered/are');

    $helper = new ZfAssetic_ViewHelper_CssAsset();

    $helper->setAssetFactory($factory);
    // Or...
    Zend_Registry::set('AssetFactory', $factory);

Also, you have to set the path where to put the generated asset and the path 
where your webserver is configured to serve files :

    <?php
    
    $helper = new ZfAssetic_ViewHelper_CssAsset();

    $public_dir = "/var/www";
    $asset_dir = "/var/www/assets";

    $helper->setPublicDirectory($public_dir);
    $helper->setAssetDirectory($asset_dir);
