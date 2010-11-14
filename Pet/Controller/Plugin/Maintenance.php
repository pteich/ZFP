<?php

class Pet_Controller_Plugin_Maintenance extends Zend_Controller_Plugin_Abstract
{

    protected $action = 'index';
    protected $controller = 'maintenance';
    protected $module = 'default';
    protected $accessParam = 'skipmaintenance';

    public function __construct($action=false,$controller=false,$module=false,$accessparam=false)
    {
        if ($action) {
            $this->action = $action;
        }
        if ($controller) {
            $this->controller = $controller;
        }
        if ($module) {
            $this->module = $module;
        }
        if ($accessparam) {
            $this->accessParam = $accessparam;
        }
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $objSession = new Zend_Session_Namespace('maintenance');
        if ($request->getParam($this->accessParam,false)) {
            $objSession->disabled = true;
        }
        if (!$objSession->disabled) {
             $request->setActionName($this->action)->setControllerName($this->controller)->setModuleName($this->module);
        }
    }
}