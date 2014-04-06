<?php

namespace OnCall\AdminBundle\Model;

abstract class NamedValue
{
    public static function isValid($type)
    {
        if (isset(static::$names[$type]))
            return true;

        return false;
    }

    public static function getName($value)
    {
        if (!static::isValid($value))
            return 'Unknown';

        return static::$names[$value];
    }

    public static function getAll()
    {
        return static::$names;
    }
}
