<?php

namespace Booni3\Dwms\Laravel;

use Illuminate\Support\Facades\Facade;

class DwmsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dwms';
    }
}