<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 28.06.2020
 * Time: 15:21
 */

namespace App\Enums;

abstract class UserStatusCodes extends BaseEnum
{
    CONST ACTIVE_USER_STATUS = 1;
    CONST PASSIVE_USER_STATUS = 0;
}