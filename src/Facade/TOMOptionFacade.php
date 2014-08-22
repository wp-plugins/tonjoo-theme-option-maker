<?php

namespace Tonjoo\TOM\Facade;

use Tonjoo\TOM\Facade\Facade as Facade;

class TOMOptionFacade extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    
     public static function getFacadeAccessor() { 
     	return 'tom_option';
     }

}