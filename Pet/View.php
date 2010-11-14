<?php

class Pet_View extends Zend_View {

    public function renderString()
    {
        ob_start();
        eval('?>' . func_get_arg(0) . '<?');
        return ob_get_clean();
    }

}
