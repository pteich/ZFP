<?php

class Pet_Application_Resource_Moduleconfig
    extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Initialize
     *
     * @return Zend_Config
     */
    public function init()
    {
        return $this->_getModuleconfig();
    }

    /**
     * Load the module's config
     *
     * @return Zend_Config
     */
    protected function _getModuleconfig()
    {
        $bootstrap = $this->getBootstrap();
        if (!($bootstrap instanceof Zend_Application_Module_Bootstrap)) {
            throw new Zend_Application_Exception('Invalid bootstrap class');
        }
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules'
            . DIRECTORY_SEPARATOR . strtolower($bootstrap->getModuleName())
            . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR;

        $cfgdir = new DirectoryIterator($path);
        $modOptions = $this->getBootstrap()->getOptions();
        foreach ($cfgdir as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                $options = $this->_loadOptions($path . $filename);
                if (($len = strpos($filename, '.')) !== false) {
                    $cfgtype = substr($filename, 0, $len);
                } else {
                    $cfgtype = $filename;
                }
                if (strtolower($cfgtype) == 'module') {
                    $modOptions = array_merge($modOptions, $options);
                } else {
                    $modOptions['resources'][$cfgtype] = $options;
                }
            }
        }

        if (Zend_Registry::isRegistered('settings')) {
            $settings = Zend_Registry::get('settings')->toArray();
        } else {
            $settings = array();
        }

        $settings[strtolower($bootstrap->getModuleName())] = $options;

        $settings = new Zend_Config($settings);
        Zend_Registry::set('settings',$settings);

        $this->getBootstrap()->setOptions($modOptions);
    }

    /**
     * Load the config file
     *
     * @param string $fullpath
     * @return array
     */
    protected function _loadOptions($fullpath)
    {
        if (file_exists($fullpath)) {
            switch (substr(trim(strtolower($fullpath)), -3)) {
                case 'ini':
                    $cfg = new Zend_Config_Ini($fullpath, $this->getBootstrap()
                        ->getEnvironment());
                    break;
                case 'xml':
                    $cfg = new Zend_Config_Xml($fullpath, $this->getBootstrap()
                        ->getEnvironment());
                    break;
                default:
                    throw new Zend_Config_Exception('Invalid format for config file');
                    break;
            }
        } else {
            throw new Zend_Application_Resource_Exception('File does not exist');
        }
        return $cfg->toArray();
    }

}