<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 28.06.2020
 * Time: 15:20
 */

namespace App\Enums;

abstract class HttpStatusCodes extends BaseEnum
{
    CONST BAD_REQUEST = 400;
    CONST INVALID_LOGIN_CREDENTIAL = 401;
    CONST INVALID_TOKEN = 403;
}