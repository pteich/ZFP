<?php
/**
 * Application Resource to get any settings from application.ini or an dedicated settings file
 * Settings are made available through registry key settings
 * @throws Zend_Exception
 *
 */
class Pet_Application_Resource_Settings
    extends Zend_Application_Resource_ResourceAbstract
{

    public function init()
    {
        $settings = $this->getOptions();

        if (is_array($settings)) {
            $settings = new Zend_Config($settings);
        } else {
            if (is_file($settings)) {
                $settings = new Zend_Config_Ini($settings,$this->getBootstrap()->getEnvironment());
            } else {
                throw new Zend_Exception('settings can either be an path to an ini file or an array with values');
            }
        }
        Zend_Registry::set('settings', $settings);
        return $settings;
    }

}