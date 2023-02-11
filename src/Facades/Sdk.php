<?php

namespace Coretrek\Idp\Facades;

use Illuminate\Support\Facades\Facade;

class Sdk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'coretrekSdk';
    }
}
