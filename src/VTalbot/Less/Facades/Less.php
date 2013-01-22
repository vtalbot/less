<?php

namespace VTalbot\Less\Facades;

use Illuminate\Support\Facades\Facade;

class Less extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'less'; }
    
}