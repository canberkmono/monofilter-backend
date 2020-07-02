<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 30.06.2020
 * Time: 15:19
 */

namespace App\Util\Logger;

use Monolog\Formatter\LineFormatter;

class CustomLineFormatter extends LineFormatter
{
    const SIMPLE_FORMAT = "[%datetime%] %level_name% %message%\n"; // %context% %extra%
    const SIMPLE_DATE = "d-m-Y H:i:s";

    public function __construct()
    {
        parent::__construct($format = null, $dateFormat = null, $allowInlineLineBreaks = true, $ignoreEmptyContextAndExtra = false);
    }
}