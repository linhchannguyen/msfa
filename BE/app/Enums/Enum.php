<?php

/**
 *
 * ENUM CLASS
 *
 */

namespace App\Enums;

class Enum
{
    /**
     * @return mixed    False if description not found
     */
    public static function describe($enumName)
    {
        if(!isset(static::__DESCRIPTIONS__[$enumName])) {

            $name = static::find($enumName);
            return static::__DESCRIPTIONS__[$name] ?? false;
        }

        return static::__DESCRIPTIONS__[$enumName];
    }

    protected static function listRaw()
    {
        $cls = new \ReflectionClass (get_called_class());
        $constants = $cls->getConstants();

        return $constants;
    }

    public static function list()
    {
        $constants = static::listRaw();

        $return = [];

        foreach($constants as $name => $value) {

            if($name != '__DESCRIPTIONS__') {
                $return[] = [
                    'name' => $name,
                    'value' => $value,
                    'description' => $constants['__DESCRIPTIONS__'][$name]
                ];
            }
        }

        return $return;
    }

    public static function find($enumValue)
    {
            $constants = static::listRaw();

            foreach($constants as $name => $value)
            {
                if($value == $enumValue)
                {
                    return $name;
                }
            }
    }
}
