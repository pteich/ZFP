<?php

class Pet_Controller_Plugin_DefaultContentType extends Zend_Controller_Plugin_Abstract
{

    public function __construct()
    {
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $response = $this->getResponse();
        if ($response->canSendHeaders()) {
            $response->setHeader('Content-type', 'text/html; charset=UTF-8', false);
        }
    }

}