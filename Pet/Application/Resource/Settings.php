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
        $settingsoptions = $this->getOptions();

        $settings = new Zend_Config($settingsoptions,true);

        if ($settingsoptions['file']) {
            foreach($settingsoptions['file'] as $file) {
                if (is_file($file)) {
                    $settings->merge(new Zend_Config_Ini($file,$this->getBootstrap()->getEnvironment()));
                }
            }
        }

        if (Zend_Registry::isRegistered('settings')) {
            $settings->merge(Zend_Registry::get('settings'));
        }

        Zend_Registry::set('settings', $settings);
        return $settings;
    }

}