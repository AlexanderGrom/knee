<?php
/*
 * Расширение stdClass
 */

namespace Knee;

use stdClass;

class stdObject extends stdClass
{
    public function __get($key)
    {
        return null;
    }
}

?>