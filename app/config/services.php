<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Twm\Db\Adapter\Pdo\Mssql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Translate\Adapter\NativeArray as Translation;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

/**
 * The Dispatcher component
 */
$di->setShared('dispatcher', function() use ($di) {
    $eventsManager = $di->getShared('eventsManager');
    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config, $di) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->setRenderLevel(View::LEVEL_ACTION_VIEW);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_',
                'compileAlways' => true
            ));

            //add filter functions
            $compiler = $volt->getCompiler();
            $compiler->addFilter('uniform_time', function($resolvedArgs, $exprAgs){
                return "date('Y-m-d H:i:s', strtotime(".$resolvedArgs."))";
            });
            $compiler->addFilter('number_format', function($resolveArgs, $exprAgs){
                return 'number_format('.$resolveArgs.')';
            });

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => 'UTF-8',
        'pdoType' => $config->database->pdoType,
        'dialectClass' => $config->database->dialectClass,
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Set Crypt Component
 */

$di->set('crypt', function () {
    $crypt = new Phalcon\Crypt();
    $crypt->setKey('car_mate');
    return $crypt;
});

/**
 * Set translation component
 */
$di->setShared('trans', function() use($di) {
    $language = $di->getShared('request')->getBestLanguage();
    $language = strtolower($language);
    $messages = include __DIR__.'/../messages/'.$language.'.php';
    return new Translation(array('content' => $messages));
});


