<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 * Readme: https://github.com/quidphp/base/blob/master/README.md
 */

namespace Quid\Base;

// _shortcut
// trait that grants static methods to declare and replace shortcuts (bracketed segments within strings)
trait _shortcut
{
    // config
    protected static $shortcut = []; // conserve les shortcuts de la classe


    // isShortcut
    // retourne vrai si le shortcut existe
    final public static function isShortcut(string $key):bool
    {
        return (array_key_exists($key,static::$shortcut));
    }


    // getShortcut
    // retourne la valeur du shortcut ou null si non existant
    final public static function getShortcut(string $key):?string
    {
        return Arr::get($key,static::$shortcut);
    }


    // setShortcut
    // ajoute ou change un shortcut
    // le shortcut est passé dans la méthode shortcut avant d'être conservé dans config
    final public static function setShortcut(string $key,string $value):void
    {
        $method = static::setShortcutMethod();
        Arr::setRef($key,$method($value),static::$shortcut);

        return;
    }


    // setsShortcut
    // ajoute ou change plusieurs shortcuts
    final public static function setsShortcut(array $keyValue):void
    {
        foreach ($keyValue as $key => $value)
        {
            if(is_string($key) && is_string($value))
            static::setShortcut($key,$value);
        }

        return;
    }


    // setShortcutMethod
    // méthode utilisé lors de l'ajout du shortcut
    final public static function setShortcutMethod():callable
    {
        return [static::class,'shortcut'];
    }


    // unsetShortcut
    // enlève un shortcut
    final public static function unsetShortcut(string $key):void
    {
        Arr::unsetRef($key,static::$shortcut);

        return;
    }


    // shortcut
    // remplace des segments dans une string ou un tableau à partir des shortcuts
    final public static function shortcut($return)
    {
        if(!empty(static::$shortcut))
        {
            if(is_string($return))
            $return = Segment::sets(null,static::$shortcut,$return);

            elseif(is_array($return))
            $return = Segment::setsArray(null,static::$shortcut,$return);
        }

        return $return;
    }


    // shortcuts
    // permet de remplacer plusieurs valeurs contenants un shortcut
    final public static function shortcuts(array $return):array
    {
        foreach ($return as $key => $value)
        {
            $return[$key] = static::shortcut($value);
        }

        return $return;
    }


    // allShortcuts
    // retourne tous les shortcuts
    final public static function allShortcuts():array
    {
        return static::$shortcut;
    }


    // emptyShortcut
    // vide les shortcuts
    final public static function emptyShortcut():void
    {
        static::$shortcut = [];

        return;
    }
}
?>