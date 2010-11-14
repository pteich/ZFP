<?php

class Pet_Application_Resource_Maintenance
    extends Zend_Application_Resource_ResourceAbstract
{

    // init() Methode wird automatisch beim Bootstrapping aufgerufen
    public function init()
    {
        // hole Optionen aus der application.ini
        $arrOptions = $this->getOptions();

        if ($arrOptions['enabled']) {
            // wir brauchen den FrontController, um das Plugin zu setzen, also erst bootstrapen, dann holen
            $this->getBootstrap()->bootstrap('frontcontroller');
            /** @var $objFrontcontroller Zend_Controller_Front */
            $objFrontcontroller = $this->getBootstrap()->getResource('frontcontroller');

            $objPlugin = new $arrOptions['pluginname'](
                $arrOptions['target']['action'],
                $arrOptions['target']['controller'],
                $arrOptions['target']['module'],
                $arrOptions['accessparam']
            );
            $objFrontcontroller->registerPlugin($objPlugin);
        }
    }

}