<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 30.06.2020
 * Time: 15:20
 */

namespace App\Enums;

class BaseEnum
{
    public static function getVariables()
    {
        $oClass = new \ReflectionClass(get_called_class());
        return $oClass->getConstants();
    }
}