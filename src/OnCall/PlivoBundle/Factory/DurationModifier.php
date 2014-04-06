<?php

namespace OnCall\PlivoBundle\Factory;

class DurationModifier
{
    static $mods = array(
        'less' => 'Less Than',
        'greater' => 'Greater Than',
        'equal' => 'Equal To'
    );

    public static function getAll()
    {
        return static::$mods;
    }
}
