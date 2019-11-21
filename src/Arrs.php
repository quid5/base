<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 * Readme: https://github.com/quidphp/base/blob/master/README
 */

namespace Quid\Base;

// arrs
// class with static methods to work with multidimensional arrays (an array containing at least another array)
class Arrs extends Root
{
    // config
    public static $config = [
        'delimiter'=>'/' // séparateur pour les méthode comme gets et sets
    ];


    // typecast
    // typecasts des valeurs par référence
    final public static function typecast(&...$values):void
    {
        foreach ($values as &$value)
        {
            if(!is_array($value))
            $value = [[$value]];

            elseif(!static::is($value))
            $value = [$value];
        }

        return;
    }


    // cast
    // permet de ramener les valeurs contenus dans un tableau dans leur cast naturel
    // par défaut, seul les nombres sont convertis
    final public static function cast($return,int $numberCast=1,int $boolCast=0):array
    {
        $return = (array) $return;
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::cast($value,$numberCast,$boolCast);

            elseif(is_scalar($value))
            $return[$key] = Scalar::cast($value,$numberCast,$boolCast);
        }

        return $return;
    }


    // castMore
    // envoie à scalar cast avec paramètre 2,1
    // nombre sont convertis, virgule remplacer par décimal, et les string booleans sont transformés en bool
    final public static function castMore($return):array
    {
        $return = (array) $return;
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::castMore($value);

            elseif(is_scalar($value))
            $return[$key] = Scalar::castMore($value);
        }

        return $return;
    }


    // is
    // retourne vrai si la valeur est un tableau qui contient au moins un tableau, retourne faux si vide
    final public static function is($value):bool
    {
        $return = false;

        if(is_array($value) && !empty($value))
        {
            foreach ($value as $v)
            {
                if(is_array($v))
                {
                    $return = true;
                    break;
                }
            }
        }

        return $return;
    }


    // isCleanEmpty
    // retourne vrai si le tableau multidimensionnel est vide après avoir utiliser la methode clean
    final public static function isCleanEmpty(array $value):bool
    {
        $return = false;

        if(static::is($value))
        {
            $value = static::clean($value);
            if(empty($value))
            $return = true;
        }

        return $return;
    }


    // hasKeyCaseConflict
    // retourne vrai si le tableau multidimensionnel contient au moins une clé en conflit de case si le tableau est insensible à la case
    final public static function hasKeyCaseConflict(array $value):bool
    {
        return (static::is($value) && static::count($value) !== static::count(static::keysInsensitive($value)))? true:false;
    }


    // merge
    // wrapper pour array_merge_recursive
    // fonctionne si une valeur n'est pas un tableau
    final public static function merge(...$values):array
    {
        Arr::typecast(...$values);
        return array_merge_recursive(...$values);
    }


    // replace
    // wrapper pour array_replace_recursive
    // fonctionne si une valeur n'est pas un tableau
    final public static function replace(...$values):array
    {
        if(count($values) === 2 && is_array($values[0]) && ($values[1] === null || $values[0] === $values[1]))
        $return = $values[0];

        else
        {
            Arr::typecast(...$values);
            $return = array_replace_recursive(...$values);
        }

        return $return;
    }


    // replaceWithMode
    // permet de faire un replace entre plusieurs tableaux
    // si la clé est dans le tableau keys, alors le merge n'est pas récursif
    // si le format =clé est dans le tableau keys, alors c'est un replace
    // par défaut le merge est récursif
    final public static function replaceWithMode(?array $replaceKeys,...$values)
    {
        if(empty($replaceKeys))
        $return = static::replace(...$values);

        else
        {
            $return = [];
            $replaceKeys = (array) $replaceKeys;

            foreach ($values as $array)
            {
                if(!is_array($array))
                $array = (array) $array;

                foreach ($array as $key => $value)
                {
                    $type = 'replace';

                    if(is_string($key) && is_array($value) && array_key_exists($key,$return) && is_array($return[$key]))
                    {
                        $type = 'multi';

                        if(in_array($key,$replaceKeys,true))
                        $type = 'uni';

                        elseif(in_array("=$key",$replaceKeys,true))
                        $type = 'replace';
                    }

                    if($type === 'multi')
                    $return[$key] = self::replace($return[$key],$value);

                    elseif($type === 'uni')
                    $return[$key] = Arr::replace($return[$key],$value);

                    elseif($type === 'replace')
                    $return[$key] = $value;
                }
            }
        }


        return $return;
    }


    // replaceSpecial
    // fait un merge entre plusieurs tableaux devant être grimpés
    // les valeurs qui ne sont pas des tableaux ne sont pas considérés
    final public static function replaceSpecial(array $target,array $keys,?array $replaceKeys,...$values)
    {
        $return = [];

        foreach ($values as $key => $value)
        {
            if(!empty($value) && is_array($value))
            $values[$key] = static::climbReplaceMode($target,$keys,$replaceKeys,$value);

            else
            unset($values[$key]);
        }

        if(!empty($values))
        $return = static::replaceWithMode($replaceKeys,...$values);

        return $return;
    }


    // clean
    // enlève des éléments du tableau multidimensionnel vide comme '', null et array()
    // si reset est true, reset les clés du tableau
    final public static function clean(array $return,bool $reset=false):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::clean($value,$reset);
        }

        $return = Arr::clean($return,$reset);

        return $return;
    }


    // trim
    // fait un trim sur les clés et/ou valeurs string du tableau multidimensionel
    final public static function trim(array $return,bool $key=false,bool $value=true):array
    {
        foreach ($return as $k => $v)
        {
            if(is_array($v))
            $return[$k] = static::trim($v,$key,$value);
        }

        $return = Arr::trim($return,$key,$value);

        return $return;
    }


    // trimClean
    // fait trim et clean sur le tableau multidimensionel
    // trimKey permet de faire un trim sur les clés aussi
    final public static function trimClean(array $return,?bool $trimKey=false,?bool $trim=true,?bool $clean=true,?bool $reset=false):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::trimClean($value,$trimKey,$trim,$clean,$reset);
        }

        $return = Arr::trimClean($return,$trimKey,$trim,$clean,$reset);

        return $return;
    }


    // get
    // permet d'aller chercher une valeur dans un tableau multidimensionnel
    // le delimiteur est / ou array
    // support pour clé insensibe à la case
    final public static function get($key,array $array,bool $sensitive=true)
    {
        $return = null;

        if($sensitive === false)
        {
            $array = static::keysLower($array,true);
            $key = Str::map([Str::class,'lower'],$key,true);
        }

        if(Arr::isKey($key) && strpos((string) $key,static::$config['delimiter']) === false && array_key_exists($key,$array))
        $return = $array[$key];

        elseif($key !== null)
        {
            $x = static::keyExplode($key);
            $count = count($x);
            $current = 1;

            foreach ($x as $v)
            {
                if(Arr::isKey($v) && is_array($array))
                {
                    $array = (array_key_exists($v,$array))? $array[$v]:null;

                    if($current === $count)
                    $return = $array;

                    $current++;
                }

                else
                break;
            }
        }

        return $return;
    }


    // gets
    // faire plusieurs appels à la fonction get et retourne un tableau multi-dimensionnel
    // support pour clé insensibe à la case
    final public static function gets(array $value,array $array,bool $sensitive=true):array
    {
        $return = [];

        foreach ($value as $v)
        {
            $return[static::keyPrepare($v)] = static::get($v,$array,$sensitive);
        }

        return $return;
    }


    // indexPrepare
    // prépare une string ou tableau d'index
    // retourne la valeur positive d'un index négatif
    // retourne un tableau
    final public static function indexPrepare($index,array $array):array
    {
        $return = [];

        if(is_string($index))
        $index = explode(static::$config['delimiter'],$index);

        elseif(is_numeric($index))
        $index = [$index];

        if(is_array($index))
        {
            $index = array_values($index);

            foreach ($index as $key => $value)
            {
                $break = true;

                if(is_scalar($value))
                {
                    if(is_array($array))
                    {
                        $key = (int) $key;
                        $value = (int) $value;
                        $return[$key] = Arr::indexPrepare($value,$array);

                        if(is_int($return[$key]))
                        {
                            $array = Arr::index($return[$key],$array);
                            $break = false;
                        }
                    }
                }

                if($break === true)
                {
                    $return = [];
                    break;
                }
            }
        }

        return $return;
    }


    // keyPrepare
    // retourne la clé pour les méthodes gets et indexes
    // le delimiteur est trim de chaque clé
    final public static function keyPrepare($key):?string
    {
        $return = null;

        if(is_string($key) || is_numeric($key))
        {
            if(!is_string($key))
            $key = (string) $key;

            $return = $key;
        }

        elseif(is_array($key))
        {
            foreach ($key as $i => $k)
            {
                if(is_string($k))
                $key[$i] = trim($k,static::$config['delimiter']);
            }

            $return = implode(static::$config['delimiter'],$key);
        }

        return $return;
    }


    // keyPrepares
    // append une série de clés en argument et retourne une string implode
    final public static function keyPrepares(...$keys):?string
    {
        $return = null;
        $prepare = [];

        foreach ($keys as $key)
        {
            $prepare[] = static::keyPrepare($key);
        }

        if(!empty($prepare))
        $return = implode(static::$config['delimiter'],$prepare);

        return $return;
    }


    // keyExplode
    // retourne la clé en array
    final public static function keyExplode($key):array
    {
        $return = [];

        if(is_string($key) || is_numeric($key))
        $return = explode(static::$config['delimiter'],(string) $key);

        elseif(is_array($key))
        $return = array_values($key);

        return $return;
    }


    // index
    // permet d'aller chercher une valeur dans un tableau multidimensionnel par index
    // le delimiteur est / ou array
    final public static function index($index,array $array)
    {
        $return = null;

        $array = static::values($array);
        $index = static::indexPrepare($index,$array);
        $return = static::get($index,$array);

        return $return;
    }


    // indexes
    // permet d'aller chercher plusieurs valeurs dans un tableau multidimensionnel par index
    // faire plusieurs appels à la fonction get et retourne un tableau multi-dimensionnel
    final public static function indexes(array $indexes,array $array):array
    {
        $return = [];

        foreach ($indexes as $index)
        {
            $return[static::keyPrepare($index)] = static::index($index,$array);
        }

        return $return;
    }


    // climb
    // grimpe dans un tableau aussi haut que possible
    // ne retourne pas null si une clé n'existe pas
    // support pour clé insensible à la case
    final public static function climb($key,array $return,bool $sensitive=true)
    {
        if(Arr::isKey($key) && strpos((string) $key,static::$config['delimiter']) === false && array_key_exists($key,$return) && $sensitive === true)
        $return = $return[$key];

        elseif($key !== null)
        {
            if($sensitive === false)
            $key = Call::map('string',[Str::class,'lower'],$key);

            $x = static::keyExplode($key);

            foreach ($x as $v)
            {
                if(Arr::isKey($v) && is_array($return))
                {
                    if($sensitive === false)
                    $return = Arr::keysLower($return,true);

                    if(array_key_exists($v,$return))
                    $return = $return[$v];
                }
            }
        }

        return $return;
    }


    // climbReplaceMode
    // climb avancé et qui merge avec la racine
    // fait une recherche via arr::getExists
    // enlève les clés all du tableau de retour
    // passe les résultats dans replaceWithMode ou replace selon la présence du troisième argument
    // ceci est utilisé pour traiter les configurations dans config
    final public static function climbReplaceMode(array $target,array $all,?array $replaceKeys,array $return)
    {
        $ori = $return;
        $gets = Arr::getsExists($target,$return);
        $return = Arr::keysStrip($all,$return);

        if(is_array($gets) && !empty($gets))
        {
            foreach ($gets as $key => $value)
            {
                if(is_array($value))
                {
                    if(!is_array($return))
                    $return = [];

                    $return = static::replaceWithMode($replaceKeys,$return,$value);
                }

                else
                $return = $value;
            }

            if(is_array($return))
            $return = static::climbReplaceMode($target,$all,$replaceKeys,$return);
        }

        return $return;
    }


    // set
    // ajoute une valeur dans un tableau multi-dimensionnel
    // support pour clé insensible à la case
    // si key est null, append []
    final public static function set($key,$value,array $return,bool $sensitive=true):array
    {
        if(Arr::isKey($key) && strpos((string) $key,static::$config['delimiter']) === false && $sensitive === true)
        $return[$key] = $value;

        elseif($key === null)
        $return[] = $value;

        else
        {
            $x = static::keyExplode($key);
            $count = count($x);
            $current = 1;
            $target =& $return;

            // pour chaque niveau
            foreach ($x as $v)
            {
                if(is_array($target))
                {
                    if(Arr::isKey($v))
                    {
                        if($sensitive === false)
                        {
                            $ikey = Arr::ikey($v,$target);
                            if(!empty($ikey))
                            $v = $ikey;
                        }

                        // last
                        if($current === $count)
                        $target[$v] = $value;

                        // sinon
                        else
                        {
                            $slice = (array_key_exists($v,$target))? $target[$v]:null;
                            $target[$v] = (is_array($slice))? $slice:[];
                            $target =& $target[$v];
                            $current++;
                        }
                    }

                    elseif($v === null)
                    {
                        // last
                        if($current === $count)
                        $target[] = $value;

                        // sinon
                        else
                        {
                            $target[] = [];
                            $lastKey = Arr::keyLast($target);
                            $target =& $target[$lastKey];
                            $current++;
                        }
                    }
                }
            }
        }

        return $return;
    }


    // sets
    // fait plusieurs appels à la fonction set
    // support pour clé insensible à la case
    final public static function sets(array $value,array $return,bool $sensitive=true):array
    {
        foreach ($value as $k => $v)
        {
            $return = static::set($k,$v,$return,$sensitive);
        }

        return $return;
    }


    // setRef
    // change une valeur d'un tableau passé par référence
    // possibilité d'une opération insensible à la case
    // si key est null, append []
    final public static function setRef($key,$value,array &$array,bool $sensitive=true):void
    {
        $array = static::set($key,$value,$array,$sensitive);

        return;
    }


    // setsRef
    // change plusieurs valeurs d'un tableau passé par référence
    // possibilité d'une opération insensible à la case
    final public static function setsRef(array $keyValue,array &$array,bool $sensitive=true):void
    {
        foreach ($keyValue as $key => $value)
        {
            static::setRef($key,$value,$array,$sensitive);
        }

        return;
    }


    // unset
    // enlève un élément d'un tableau multidimensionnel
    // support pour clé insensible à la case
    final public static function unset($key,array $return,bool $sensitive=true):array
    {
        if(Arr::isKey($key) && strpos((string) $key,static::$config['delimiter']) === false && array_key_exists($key,$return) && $sensitive === true)
        unset($return[$key]);

        elseif($key !== null)
        {
            $x = static::keyExplode($key);
            $count = count($x);
            $current = 1;
            $target =& $return;

            // pour chaque niveau
            foreach ($x as $v)
            {
                if(Arr::isKey($v) && is_array($target))
                {
                    if($sensitive === false)
                    {
                        $ikey = Arr::ikey($v,$target);
                        if(!empty($ikey))
                        $v = $ikey;
                    }

                    // last
                    if($current === $count && array_key_exists($v,$target))
                    unset($target[$v]);

                    // sinon
                    else
                    {
                        $slice = (array_key_exists($v,$target))? $target[$v]:null;
                        if(is_array($slice))
                        {
                            $target =& $target[$v];
                            $current++;
                        }

                        else
                        break;
                    }
                }
            }
        }

        return $return;
    }


    // unsets
    // enlève plusieurs élément d'un tableau multidimensionnel
    // support pour clé insensible à la case
    final public static function unsets(array $value,array $return,bool $sensitive=true):array
    {
        foreach ($value as $v)
        {
            $return = static::unset($v,$return,$sensitive);
        }

        return $return;
    }


    // unsetRef
    // enlève la valeur d'un tableau passé par référence
    // possibilité d'une opération insensible à la case
    final public static function unsetRef($key,array &$array,bool $sensitive=true):void
    {
        $array = static::unset($key,$array,$sensitive);

        return;
    }


    // unsetsRef
    // enlève plusieurs valeurs d'un tableau passé par référence
    // possibilité d'une opération insensible à la case
    final public static function unsetsRef(array $keys,array &$array,bool $sensitive=true):void
    {
        $array = static::unsets($keys,$array,$sensitive);

        return;
    }


    // getSet
    // permet de faire des modifications get/set sur un tableau multidimensionnel
    // le tableau est passé par référence
    // pas de support pour clé insensible à la case
    final public static function getSet($get=null,$set=null,array &$source)
    {
        $return = null;

        // get tout
        if($get === null && $set === null)
        $return = $source;

        // get un
        elseif(Arr::isKey($get) && $set === null)
        $return = static::get($get,$source);

        // tableau, set tout
        elseif(is_array($get))
        {
            $source = static::replace($source,$get);
            $return = true;
        }

        // set un
        elseif(Arr::isKey($get) && $set !== null)
        {
            $source = static::set($get,$set,$source);
            $return = true;
        }

        return $return;
    }


    // count
    // count les clés d'un tableau multidimensionnel
    final public static function count(array $array):int
    {
        return count($array,COUNT_RECURSIVE);
    }


    // countLevel
    // count les clés d'un tableau multidimensionnel à un niveau donné
    final public static function countLevel(int $level,array $array):int
    {
        $return = 0;

        if($level === 0)
        $return = count($array,COUNT_NORMAL);

        elseif($level > 0)
        {
            foreach ($array as $key => $value)
            {
                if(is_array($value))
                $return += static::countLevel(($level - 1),$array[$key]);
            }
        }

        return $return;
    }


    // depth
    // retourne la profondeur maximale du tableau
    final public static function depth(array $array):int
    {
        $return = 1;

        foreach ($array as $value)
        {
            if(is_array($value))
            {
                $depth = static::depth($value) + 1;

                if($depth > $return)
                $return = $depth;
            }
        }

        return $return;
    }


    // keys
    // retourne toutes les clés d'un tableau multidimensionnel ou toutes les clés ayant la valeur donnée en deuxième argument
    // retourne dans un format compatible avec la méthode get
    // support pour recherche insensible à la case
    // si valeur est null, la fonction ne cherche pas à moins que searchNull soit true
    final public static function keys(array $array,$value=null,bool $sensitive=true,bool $searchNull=false,array $parent=[]):array
    {
        $return = [];
        $keys = Arr::keys($array,$value,$sensitive,$searchNull);

        foreach ($array as $k => $v)
        {
            if(!empty($parent))
            $current = Arr::append($parent,$k);
            else
            $current = [$k];

            if(is_array($v))
            {
                $append = static::keys($v,$value,$sensitive,$searchNull,$current);

                if(!empty($append))
                $return = Arr::append($return,$append);
            }

            elseif(in_array($k,$keys,true))
            $return[] = $current;
        }

        return $return;
    }


    // crush
    // écrase un tableau multidimensionel, concat en string les clés et met les valeurs pour chaque clé
    // les clés retournés sont compatibles avec la méthode get
    // support pour recherche insensible à la case
    final public static function crush(array $array,$value=null,bool $sensitive=true,string $parent=''):array
    {
        $return = [];
        $keys = Arr::keys($array,$value,$sensitive);
        $delimiter = static::$config['delimiter'];

        foreach ($array as $k => $v)
        {
            $current = (string) $k;

            if(!empty($parent))
            $current = $parent.$delimiter.$current;

            if(is_array($v))
            {
                $append = static::crush($v,$value,$sensitive,$current);

                if(!empty($append))
                $return = Arr::append($return,$append);
            }

            elseif(in_array($k,$keys,true))
            $return[$current] = $v;
        }

        return $return;
    }


    // crushReplace
    // écrase un tableau multidimensionnel en écrasant toutes les clés -> valeurs
    // support pour recherche insensible à la case
    final public static function crushReplace(array $array,$value=null,bool $sensitive=true):array
    {
        $return = [];
        $keys = Arr::keys($array,$value,$sensitive);

        foreach ($array as $k => $v)
        {
            if(is_array($v))
            {
                $replace = static::crushReplace($v,$value,$sensitive);

                if(!empty($replace))
                $return = Arr::replace($return,$replace);
            }

            elseif(in_array($k,$keys,true))
            $return[$k] = $v;
        }

        return $return;
    }


    // values
    // retourne les valeurs d'un tableau multidimensionnel, reset les clés
    // is permet de spécifier le type de valeurs à garder dans le tableau multidimensionnel réindexé
    final public static function values(array $array,$is=null):array
    {
        $return = [];

        if($is !== null)
        {
            foreach ($array as $value)
            {
                if(is_array($value))
                $return[] = static::values($value,$is);

                elseif(Validate::is($is,$value))
                $return[] = $value;
            }
        }

        else
        {
            $return = array_values($array);

            foreach ($return as $key => $value)
            {
                if(is_array($value))
                $return[$key] = static::values($value);
            }
        }

        return $return;
    }


    // search
    // retourne le chemin de la première clé de la valeur trouvé dans le tableau multidimensionnel
    // retourne un tableau compatible avec la fonction get
    // support pour recherche insensible à la case
    final public static function search($value,array $array,bool $sensitive=true):?array
    {
        $return = null;

        $search = Arr::search($value,$array,$sensitive);
        if($search !== null)
        $return = [$search];

        if($return === null)
        {
            foreach ($array as $k => $v)
            {
                if(is_array($v))
                {
                    $search = static::search($value,$v,$sensitive);

                    if($search !== null)
                    $return = Arr::append($k,$search);
                }
            }
        }

        return $return;
    }


    // searchFirst
    // retourne le chemin de la première clé de la première valeur trouvé dans le tableau multidimensionnel
    // support pour recherche insensible à la case
    final public static function searchFirst(array $values,array $array,bool $sensitive=true)
    {
        $return = null;

        foreach ($values as $v)
        {
            $search = static::search($v,$array,$sensitive);
            if($search !== null)
            {
                $return = $search;
                break;
            }
        }

        return $return;
    }


    // in
    // recherche si la valeur est dans un tableau multidimensionnel via la fonction in_array
    // support pour recherche insensible à la case
    final public static function in($value,array $array,bool $sensitive=true):bool
    {
        $return = Arr::in($value,$array,$sensitive);

        if($return === false)
        {
            foreach ($array as $k => $v)
            {
                if(is_array($v) && static::in($value,$v,$sensitive))
                {
                    $return = true;
                    break;
                }
            }
        }

        return $return;
    }


    // ins
    // recherche que toutes les valeurs fournis sont dans le tableau multidimensionnel via la fonction in_array
    // support pour recherche insensible à la case
    final public static function ins(array $values,array $array,bool $sensitive=true):bool
    {
        $return = false;

        if(!empty($values))
        {
            $return = true;

            foreach ($values as $k => $v)
            {
                if(!static::in($v,$array,$sensitive))
                {
                    $return = false;
                    break;
                }
            }
        }

        return $return;
    }


    // inFirst
    // retourne la première valeur trouvé dans le tableau multidimensionnel ou null si rien n'est trouvé
    // support pour recherche insensible à la case
    final public static function inFirst(array $values,array $array,bool $sensitive=true)
    {
        $return = null;

        foreach ($values as $value)
        {
            if(Arr::in($value,$array,$sensitive))
            {
                $return = $value;
                break;
            }

            else
            {
                foreach ($array as $k => $v)
                {
                    if(is_array($v) && Arr::in($value,$v,$sensitive))
                    {
                        $return = $value;
                        break 2;
                    }
                }
            }
        }

        return $return;
    }


    // map
    // array_map pour un tableau multidimensionnel
    // ermet de spécifier des arguments en troisième arguments
    // ne supporte pas plusieurs tableaux
    // si callable est closure à ce moment au moins trois arguments sont envoyés à la fonction = value, key et array
    final public static function map(callable $callable,array $return,...$args):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::map($callable,$value,...$args);

            else
            {
                if($callable instanceof \Closure)
                $return[$key] = $callable($value,$key,$return,...$args);
                else
                $return[$key] = $callable($value,...$args);
            }
        }

        return $return;
    }


    // walk
    // wrapper pour array_walk_recursive
    // array est passé par référence
    final public static function walk(callable $callable,array &$array,$data=null):bool
    {
        return array_walk_recursive($array,$callable,$data);
    }


    // shuffle
    // mélange un tableau multidimensionnel, mais conserve les clés
    final public static function shuffle(array $return):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::shuffle($value);
        }

        $return = Arr::shuffle($return);

        return $return;
    }


    // reverse
    // invertit un tableau multidimensionel
    final public static function reverse(array $return,bool $preserve=true):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::reverse($value,$preserve);
        }

        $return = Arr::reverse($return,$preserve);

        return $return;
    }


    // flip
    // reformat un tableau en s'assurant que la valeur devienne la clé
    // value permet de specifier la valeur des nouvelles valeurs du tableau, si null prend la clé
    // exception permet d'exclure le contenu d'une clé du reformatage
    final public static function flip(array $array,$value=null,$exception=null):array
    {
        $return = [];

        if(Arr::isKey($exception))
        $exception = [$exception];

        foreach ($array as $k => $v)
        {
            // exception
            if(!empty($exception) && is_array($exception) && in_array($k,$exception,true))
            $return[$k] = $v;

            // cle normal de tableau
            elseif(Arr::isKey($v))
            $return[$v] = ($value === null)? $k:$value;

            // tableau
            elseif(is_array($v))
            $return[$k] = static::flip($v,$value,$exception);

            // autre valeur
            else
            $return[$k] = $v;
        }

        return $return;
    }


    // implode
    // implode un tableau multidimensionnel en chaine
    // possibilité de mettre un delimiteur différent pour chaque niveau
    // possibilité de trim et clean
    final public static function implode($delimiter,array $array,bool $trim=false,bool $clean=false):string
    {
        $return = '';
        $deli = null;

        if(is_string($delimiter))
        $deli = $delimiter;

        elseif(is_array($delimiter) && !empty($delimiter))
        {
            $deli = array_shift($delimiter);
            $delimiter = (empty($delimiter))? $deli:$delimiter;
        }

        if(is_string($deli))
        {
            if($trim === true || $clean === true)
            $array = static::trimClean($array,$trim,$trim,$clean);

            foreach ($array as $k => $v)
            {
                $r = '';

                if(is_scalar($v))
                {
                    $v = (string) $v;
                    $r .= $v;
                }

                elseif(is_array($v))
                $r .= static::implode($delimiter,$v);

                if(strlen($r))
                {
                    if(strlen($return))
                    $return .= $deli;

                    $return .= $r;
                }
            }
        }

        return $return;
    }


    // explode
    // explose un tableau multidimensionnel selon un delimiter
    final public static function explode(string $delimiter,array $value,int $limit=PHP_INT_MAX):array
    {
        $return = [];

        foreach ($value as $k => $v)
        {
            if(is_scalar($v))
            {
                $v = (string) $v;
                $x = explode($delimiter,$v,$limit);
                $return = array_merge($return,$x);
            }

            elseif(is_array($v))
            $return = array_merge($return,static::explode($delimiter,$v,$limit));
        }

        return $return;
    }


    // fill
    // crée un tableau multidimensionnel en utilisant la fonction range
    final public static function fill(array $dimensions,$value=true):array
    {
        $return = [];

        foreach ($dimensions as $dimension)
        {
            $arg = [0,1,1];

            if(is_array($dimension) && Arr::validate('int',$dimension))
            $arg = array_replace($arg,array_values($dimension));

            $range = Arr::range(...$arg);

            if(!empty($range))
            {
                $fillKeys = array_fill_keys($range,$value);

                if(empty($return))
                $return = $fillKeys;

                else
                $return = static::valuesChange($value,$fillKeys,$return);
            }
        }

        return $return;
    }


    // fillKeys
    // crée un tableau multidimensionnel en utilisant des tableaux de keys
    final public static function fillKeys(array $dimensions,$value=true):array
    {
        $return = [];

        foreach ($dimensions as $dimension)
        {
            if(is_array($dimension) && !empty($dimension) && Arr::validate('arrKey',$dimension))
            {
                $fillKeys = array_fill_keys($dimension,$value);

                if(empty($return))
                $return = $fillKeys;

                else
                $return = static::valuesChange($value,$fillKeys,$return);
            }
        }

        return $return;
    }


    // hierarchy
    // retourne un tableau hierarchy sous une forme logique
    // un parent non existant peut être ajouté dans la hiérarchie si existe est true
    final public static function hierarchy(array $array,bool $exists=true):array
    {
        $return = [];
        $structure = static::hierarchyStructure($array,$exists);
        $sets = [];

        foreach ($structure as $key => $value)
        {
            $key = static::keyPrepare($value);
            $sets[$key] = null;
        }

        $return = static::sets($sets,$return);

        return $return;
    }


    // hierarchyStructure
    // prend un tableau unidimensionnel clé -> valeur
    // si une clé à un parent, le nom du parent est la valeur
    // retourne un tableau multidimensionnel avec la structure hiérarchique
    // un parent non existant peut être ajouté dans la hiérarchie si existe est true
    final public static function hierarchyStructure(array $array,bool $exists=true):array
    {
        $return = [];
        $remove = [];

        foreach ($array as $key => $value)
        {
            $keep = false;

            if(is_scalar($key))
            {
                if($value === null)
                $return[] = [$key];

                elseif(is_scalar($value))
                {
                    if(array_key_exists($value,$array))
                    $keep = true;

                    elseif($exists === false)
                    $return[] = [$value,$key];
                }
            }

            if($keep === false)
            $remove[] = $key;
        }

        if(!empty($remove))
        $array = Arr::keysStrip($remove,$array);

        $return = static::hierarchyAppend($array,$return);

        return $return;
    }


    // hierarchyAppend
    // méthode utilisé par hierarchy pour ajouter un tableau clé -> parent à une hiérarchie existante
    final public static function hierarchyAppend(array $array,array $return):array
    {
        if(!empty($return))
        {
            while (!empty($array))
            {
                $continue = false;

                foreach ($array as $key => $value)
                {
                    foreach ($return as $k => $v)
                    {
                        $last = Arr::valueLast($v);

                        if($value === $last)
                        {
                            $continue = true;
                            $return[] = Arr::append($v,$key);
                            unset($array[$key]);
                            break;
                        }
                    }
                }

                if($continue === false)
                break;
            }
        }

        return $return;
    }


    // keyExists
    // retourne vrai si la clé existe dans le tableau multidimensionnel
    // le delimiteur est / ou array
    // support pour clé insensibe à la case
    final public static function keyExists($key,array $array,bool $sensitive=true):bool
    {
        $return = false;

        if($sensitive === false)
        {
            $array = static::keysLower($array,true);
            $key = Str::map([Str::class,'lower'],$key,true);
        }

        if(Arr::isKey($key) && strpos((string) $key,static::$config['delimiter']) === false && array_key_exists($key,$array))
        $return = true;

        elseif($key !== null)
        {
            $x = static::keyExplode($key);
            $count = count($x);
            $current = 1;

            foreach ($x as $v)
            {
                if(Arr::isKey($v) && is_array($array))
                {
                    if($current === $count && array_key_exists($v,$array))
                    $return = true;

                    else
                    {
                        $array = (array_key_exists($v,$array))? $array[$v]:null;
                        $current++;
                    }
                }

                else
                break;
            }
        }

        return $return;
    }


    // keysExists
    // retourne vrai si les clés existent dans le tableau multidimensionnel
    // le delimiteur est / ou array
    // support pour clé insensibe à la case
    final public static function keysExists(array $keys,array $array,bool $sensitive=true):bool
    {
        $return = false;

        foreach ($keys as $key)
        {
            $return = static::keyExists($key,$array,$sensitive);

            if($return === false)
            break;
        }

        return $return;
    }


    // keyPath
    // retourne le chemin de la première clé trouvée
    final public static function keyPath($key,array $source,bool $first=true)
    {
        $return = null;

        if(Arr::isKey($key) && !empty($source))
        {
            if(array_key_exists($key,$source))
            $return = [$key=>null];

            else
            {
                foreach ($source as $k => $v)
                {
                    if(is_array($v))
                    {
                        $path = static::keyPath($key,$v,false);

                        if($path !== null)
                        {
                            if(is_array($path))
                            $return[$k] = $path;

                            else
                            $return[$k][$path] = true;

                            break;
                        }
                    }
                }
            }
        }

        if($first === true && is_array($return))
        {
            $keys = static::keys($return);
            if(is_array($keys) && count($keys) === 1)
            $return = current($keys);
        }

        return $return;
    }


    // keyPaths
    // retourne les chemins de toutes les occurences de la clé
    final public static function keyPaths($key,array $source):array
    {
        $return = [];

        while ($path = static::keyPath($key,$source))
        {
            $return[] = $path;
            $source = static::unset($path,$source);
        }

        return $return;
    }


    // keyValue
    // retourne la première valeur de la clé trouvé dans le tableau multidimensionnel
    final public static function keyValue($key,array $source)
    {
        $return = null;
        $path = static::keyPath($key,$source);

        if(is_array($path) && !empty($path))
        $return = static::get($path,$source);

        return $return;
    }


    // keyValues
    // retourne toutes les valeurs de la clé trouvé dans le tableau
    final public static function keyValues($key,array $source):array
    {
        $return = [];
        $paths = static::keyPaths($key,$source);

        if(is_array($paths) && !empty($paths))
        $return = static::gets($paths,$source);

        return $return;
    }


    // keysLower
    // change la case des clés dans le tableau multidimensionnel
    // support pour multibyte
    final public static function keysLower(array $return,?bool $mb=null):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::keysLower($value,$mb);
        }

        $return = Arr::keysLower($return,$mb);

        return $return;
    }


    // keysUpper
    // change la case des clés dans le tableau multidimensionnel
    // support pour multibyte
    final public static function keysUpper(array $return,?bool $mb=null):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::keysUpper($value,$mb);
        }

        $return = Arr::keysUpper($return,$mb);

        return $return;
    }


    // keysInsensitive
    // retourne une version du tableau multidimensionnel avec les clés en conflit de case retirés
    // garde la même case
    final public static function keysInsensitive(array $return):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::keysInsensitive($value);
        }

        $return = Arr::keysInsensitive($return);

        return $return;
    }


    // keysSort
    // sort un tableau par clé, gère le multidimensionnel
    // on peut mettre asc ou desc à sort (ksort ou krsort)
    final public static function keysSort(array $return,$sort=true,int $type=SORT_FLAG_CASE | SORT_NATURAL):array
    {
        $return = Arr::keysSort($return,$sort,$type);

        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::keysSort($value,$sort,$type);
        }

        return $return;
    }


    // keysReplace
    // str replace sur les clés du tableau multidimensionnel
    final public static function keysReplace(array $replace,array $return,bool $sensitive=true):array
    {
        $return = Arr::keysReplace($replace,$return,$sensitive);

        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::keysReplace($replace,$value,$sensitive);
        }

        return $return;
    }


    // valueKey
    // retourne toutes les clés contenant contenant la valeur donnée
    // support pour recherche insensible à la case
    final public static function valueKey($value,array $array,bool $sensitive=true):array
    {
        return static::keys($array,$value,$sensitive,true);
    }


    // valuesKey
    // retourne toutes les clés contenant les valeurs données
    // support pour recherche insensible à la case
    final public static function valuesKey(array $values,array $array,bool $sensitive=true):array
    {
        $return = [];

        foreach ($values as $value)
        {
            foreach (static::keys($array,$value,$sensitive,true) as $key)
            {
                if(!in_array($key,$return,true))
                $return[] = $key;
            }
        }

        return $return;
    }


    // valueStrip
    // retourne le tableau multidimensionnel sans toutes les slices avec la valeur donnée
    // permet la recherche insensible à la case
    final public static function valueStrip($value,array $return,bool $sensitive=true):array
    {
        foreach (static::valueKey($value,$return,$sensitive) as $key)
        {
            $return = static::unset($key,$return,$sensitive);
        }

        return $return;
    }


    // valuesStrip
    // retourne le tableau multidimensionnel sans toutes les slices avec les valeurs données
    // permet la recherche insensible à la case
    final public static function valuesStrip(array $values,array $return,bool $sensitive=true):array
    {
        foreach (static::valuesKey($values,$return,$sensitive) as $key)
        {
            $return = static::unset($key,$return,$sensitive);
        }

        return $return;
    }


    // valuesCrush
    // permet de crush les valeurs d'un tableau multidimensionnel de manière exponentielle
    // c'est à dire que toutes les valeurs de chaque niveau se combienent avec toutes les valeurs des niveaux supérieures
    // utiliser par base assert pour la méthode prepareClasse
    final public static function valuesCrush(array $array,?array $parent=[]):array
    {
        $return = [];

        if(static::is($array) && !empty($array))
        {
            $hasParent = (count($parent) > 0)? true:false;
            $first = Arr::valueFirst($array);
            $array = Arr::spliceFirst($array);

            if(!empty($array))
            {
                foreach ($first as $value)
                {
                    $value = ($hasParent === true)? Arr::append($parent,$value):[$value];
                    $return = Arr::append($return,static::valuesCrush($array,$value));
                }
            }

            elseif(!empty($first))
            {
                foreach ($first as $v)
                {
                    $v = ($hasParent === true)? Arr::append($parent,$v):[$v];
                    $return[] = $v;
                }
            }
        }

        return $return;
    }


    // valuesChange
    // changement de valeur dans un tableau multidimensionnel
    final public static function valuesChange($value,$change,array $return,?int $amount=null,int $i=0):array
    {
        foreach ($return as $k => $v)
        {
            if($v === $value)
            {
                $return[$k] = $change;
                $i++;

                if(is_int($amount) && $i >= $amount)
                break;
            }

            elseif(is_array($v))
            $return[$k] = static::valuesChange($value,$change,$v,$amount,$i);
        }

        return $return;
    }


    // valuesReplace
    // str_replace sur les valeurs du tableau multidimensionnel
    final public static function valuesReplace(array $replace,array $return,bool $once=true,bool $sensitive=true):array
    {
        if(!empty($replace))
        {
            foreach ($return as $key => $value)
            {
                if(is_string($value))
                {
                    $v = Str::replace($replace,$value,$once,$sensitive);

                    if($value !== $v)
                    $return[$key] = $v;
                }

                elseif(is_array($value))
                $return[$key] = static::valuesReplace($replace,$value,$once,$sensitive);
            }
        }

        return $return;
    }


    // valuesAppend
    // cette méthode est utilisé pour le remplacement dans les routes
    // si une même clé existe, efface la valeur et ensuite fait un arr::append entre le tableau restant et la nouvelle valeur
    final public static function valuesAppend(array $append,array $return,bool $once=true,bool $sensitive=true):array
    {
        foreach ($append as $k => $v)
        {
            foreach ($return as $key => $value)
            {
                if(is_string($value))
                {
                    foreach ($append as $k => $v)
                    {
                        if($value === $k)
                        {
                            unset($return[$key]);
                            $return = array_merge($return);
                            $return = Arr::append($return,$v);
                            break;
                        }
                    }
                }

                elseif(is_array($value))
                $return[$key] = static::valuesAppend($append,$value,$once,$sensitive);
            }
        }

        return $return;
    }


    // valuesLower
    // change la case des valeurs dans le tableau multidimensionnel
    // utilise multibyte
    final public static function valuesLower(array $return):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::valuesLower($value);
        }

        $return = Arr::valuesLower($return);

        return $return;
    }


    // valuesUpper
    // change la case des valeurs dans le tableau multidimensionnel
    // utilise multibyte
    final public static function valuesUpper(array $return):array
    {
        foreach ($return as $key => $value)
        {
            if(is_array($value))
            $return[$key] = static::valuesUpper($value);
        }

        $return = Arr::valuesUpper($return);

        return $return;
    }


    // keysValuesLower
    // change la case des valeurs et clés string dans le tableau multidimensionnel pour lowercase
    // valeur mb seulement pour keysLower, values utilise mb
    final public static function keysValuesLower(array $return,?bool $mb=null):array
    {
        $return = static::keysLower($return,$mb);
        $return = static::valuesLower($return);

        return $return;
    }


    // keysValuesUpper
    // change la case des valeurs et clés string dans le tableau multidimensionnel pour uppercase
    // valeur mb seulement pour keysUpper, values utilise mb
    final public static function keysValuesUpper(array $return,?bool $mb=null):array
    {
        $return = static::keysUpper($return,$mb);
        $return = static::valuesUpper($return);

        return $return;
    }
}
?>