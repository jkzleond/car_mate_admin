<?php
use Phalcon\DI,
    Phalcon\DI\FactoryDefault;

ini_set('display_errors',1);
error_reporting(E_ALL);

define('ROOT_PATH', __DIR__);
define('PATH_LIBRARY', __DIR__ . '/../app/library/');
define('PATH_SERVICES', __DIR__ . '/../app/services/');
define('PATH_RESOURCES', __DIR__ . '/../app/resources/');

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

// required for phalcon/incubator
include __DIR__ . "/../vendor/autoload.php";

// use the application autoloader to autoload the classes
// autoload the dependencies found in composer
/**
 * Read the configuration
 */
$config = include __DIR__ . "/../app/config/config.php";

/**
 * Read auto-loader
 */
include __DIR__ . "/../app/config/loader.php";

$dirs = $loader->getDirs();

$dirs[] = ROOT_PATH;

$loader->registerDirs($dirs);

//$loader->register();

DI::reset();

/**
 * Read services
 */
include __DIR__ . "/../app/config/services.php";


DI::setDefault($di);
