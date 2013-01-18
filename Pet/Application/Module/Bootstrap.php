<?php

class Pet_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap
{

    /**
     * Constructor
     *
     * @param Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        parent::__construct($application);
        $this->init();
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function init()
    {
        $resource = "moduleconfig";
        $this->registerPluginResource($resource);
        $this->getPluginResource($resource);
        $this->_executeResource($resource);
    }

}