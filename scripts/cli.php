<?php

// initialize the application path, library and autoloading
defined('APPLICATION_PATH') ||
    define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));

$paths = explode(PATH_SEPARATOR, get_include_path());
$paths[] = realpath(__DIR__.'/../library');
set_include_path(implode(PATH_SEPARATOR, $paths));
unset($paths);

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

$loader->registerNamespace('Pet_');

$getopt = new Zend_Console_Getopt(array(
    'action|a=s' => 'action to perform in format of "module/controller/action/param1_name/param1_value/param2_name/param2_value/.."',
    'env|e-s'    => 'defines application environment (defaults to "production")',
    'help|h'     => 'displays usage information',
));

try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    return false;
}

// show help message in case it was requested or params were incorrect (module, controller and action)
if ($getopt->getOption('h') || !$getopt->getOption('a')) {
    echo $getopt->getUsageMessage();
    return true;
}

// initialize values based on presence or absence of CLI options
$env      = $getopt->getOption('e');
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (null === $env) ? 'production' : $env);

// initialize Zend_Application
$application = new Zend_Application (
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// bootstrap and retrive the frontController resource
$front = $application->getBootstrap()
    ->bootstrap('frontController')
    ->getResource('frontController');


$params = explode('/', $getopt->getOption('a'));

$module = array_shift($params);
$controller = array_shift($params);
$action = array_shift($params);

$data=array();
for($i=0;$i<count($params);$i+=2) {
    $data[$params[$i]] = $params[$i+1];
}

$request = new Zend_Controller_Request_Simple($action, $controller, $module, $data);

// set front controller options to make everything operational from CLI
$front->setRequest($request)
    ->setResponse(new Zend_Controller_Response_Cli())
    ->setRouter(new Pet_Controller_Router_Cli())
    ->throwExceptions(true);

$application->bootstrap()
    ->run();