<?php

declare(strict_types=1);

namespace App\Model;

use RedBeanPHP\Facade as R;

class Db
{

    public function __construct($path)
    {
	R::setup($path);
	R::freeze(false);
    }  
}
