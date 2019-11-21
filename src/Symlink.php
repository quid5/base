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

// symlink
// class with static methods to manage symlinks
class Symlink extends Finder
{
    // config
    public static $config = [];


    // is
    // retourne vrai si le chemin est un lien symbolique
    final public static function is($path,bool $makePath=true):bool
    {
        $return = false;

        if($makePath === true)
        $path = static::path($path);

        if(is_string($path) && is_link($path))
        $return = true;

        return $return;
    }


    // isReadable
    // retourne vrai si le chemin est un lien symbolique existant et lisible
    final public static function isReadable($path,bool $makePath=true):bool
    {
        $return = false;

        if($makePath === true)
        $path = static::path($path);

        if(static::is($path,false) && static::isPermission('readable',$path))
        $return = true;

        return $return;
    }


    // isWritable
    // retourne vrai si le chemin est un lien symbolique existant et accessible en écriture
    final public static function isWritable($path,bool $makePath=true):bool
    {
        $return = false;

        if($makePath === true)
        $path = static::path($path);

        if(static::is($path,false) && static::isPermission('writable',$path))
        $return = true;

        return $return;
    }


    // isExecutable
    // retourne vrai le chemin est un lien symbolique existant et éxécutable
    final public static function isExecutable($path,bool $makePath=true):bool
    {
        $return = false;

        if($makePath === true)
        $path = static::path($path);

        if(static::is($path,false) && static::isPermission('executable',$path))
        $return = true;

        return $return;
    }


    // inode
    // retourne le numéro d'inode du symlink
    final public static function inode($path):?int
    {
        $return = null;
        $ino = static::statValue('ino',$path);

        if(is_int($ino))
        $return = $ino;

        return $return;
    }


    // permission
    // retourne la permission du symlink
    final public static function permission($path,bool $format=false)
    {
        $return = null;
        $mode = static::statValue('mode',$path);

        if(is_int($mode))
        $return = ($format === true)? static::formatValue('permission',$mode):$mode;

        return $return;
    }


    // owner
    // retourne l'identifiant du propriétaire du symlink
    final public static function owner($path,bool $format=false)
    {
        $return = null;
        $owner = static::statValue('uid',$path);

        if(is_int($owner))
        $return = ($format === true)? static::formatValue('owner',$owner):$owner;

        return $return;
    }


    // ownerChange
    // change le owner du symlink
    final public static function ownerChange($user,$path):bool
    {
        $return = false;
        $path = static::path($path);

        if(is_scalar($user) && static::isWritable($path,false) && lchown($path,$user))
        $return = true;

        return $return;
    }


    // group
    // retourne l'identifiant du groupe du symlink
    final public static function group($path,bool $format=false)
    {
        $return = null;
        $group = static::statValue('gid',$path);

        if(is_int($group))
        $return = ($format === true)? static::formatValue('group',$group):$group;

        return $return;
    }


    // groupChange
    // change le groupe du symlink
    final public static function groupChange($group,$path):bool
    {
        $return = false;
        $path = static::path($path);

        if(is_scalar($group) && static::isWritable($path,false) && lchgrp($path,$group))
        $return = true;

        return $return;
    }


    // size
    // retourne la taille du symlink
    final public static function size($path,bool $format=false)
    {
        $return = null;
        $size = static::statValue('size',$path);

        if(is_int($size))
        $return = ($format === true)? static::formatValue('size',$size):$size;

        return $return;
    }


    // dateAccess
    // retourne la dernière date d'accès du symlink
    final public static function dateAccess($path,bool $format=false)
    {
        $return = null;
        $atime = static::statValue('atime',$path);

        if(is_int($atime))
        $return = ($format === true)? static::formatValue('dateAccess',$atime):$atime;

        return $return;
    }


    // dateModify
    // retourne la date de modification du symlink
    final public static function dateModify($path,bool $format=false)
    {
        $return = null;
        $mtime = static::statValue('mtime',$path);

        if(is_int($mtime))
        $return = ($format === true)? static::formatValue('dateModify',$mtime):$mtime;

        return $return;
    }


    // dateInodeModify
    // retourne la date de modification de l'inode du symlink
    final public static function dateInodeModify($path,bool $format=false)
    {
        $return = null;
        $ctime = static::statValue('ctime',$path);

        if(is_int($ctime))
        $return = ($format === true)? static::formatValue('dateInodeModify',$ctime):$ctime;

        return $return;
    }


    // stat
    // retourne les informations stat du symlink
    // le chemin de symlink n'est pas suivi
    final public static function stat($path,bool $formatKey=false,bool $formatValue=false):?array
    {
        $return = null;
        $path = static::path($path);

        if(static::is($path,false))
        {
            $stat = lstat($path);

            if(is_array($stat))
            {
                $return = $stat;

                if($formatKey === true)
                $return = static::statReformat($return,$formatValue);
            }
        }

        return $return;
    }


    // info
    // étend la méthode info de finder
    // ajoute la target, enlève size
    final public static function info($path,bool $clearStatCache=false,bool $format=true):?array
    {
        $return = null;
        $path = static::path($path);
        $is = static::isReadable($path,false);

        if($is === true)
        {
            $return = [];

            if($clearStatCache === true)
            static::clearStatCache();

            $return['path'] = $path;
            $return['target'] = static::get($path);
            $return['type'] = filetype($path);
            $return['readable'] = $is;
            $return['writable'] = static::isWritable($path,false);
            $return['executable'] = static::isExecutable($path,false);
            $return['dir'] = is_dir($path);
            $return['file'] = is_file($path);
            $return['link'] = is_link($path);
            $return['pathinfo'] = Path::info($path);
            $return['stat'] = (array) static::stat($path,$format);
        }

        return $return;
    }


    // get
    // retourne le contenu d'un symlink
    final public static function get($path):?string
    {
        $return = null;
        $path = static::path($path);

        if(static::isReadable($path,false))
        {
            $link = readlink($path);

            if(is_string($link) && !empty($link))
            $return = static::normalize($link,false);
        }

        return $return;
    }


    // getStat
    // retourne le tableau stat du fichier ou directoire référencé, si existant
    // sinon retourne null
    final public static function getStat($path,bool $format=true):?array
    {
        $return = null;
        $path = static::get($path);

        if(!empty($path))
        $return = Finder::stat($path,$format);

        return $return;
    }


    // getInfo
    // retourne le tableau info du fichier ou directoire référencé, si le symlink existe
    // sinon retourne null
    final public static function getInfo($path,bool $format=true,bool $clearStatCache=false):?array
    {
        $return = null;
        $path = static::get($path);

        if(!empty($path))
        $return = Finder::info($path,$format,$clearStatCache);

        return $return;
    }


    // set
    // créer un nouveau lien symbolique si la target est lisible et que la destination est créable
    // ne remplace pas si existant par défaut (mais on peut mettre true)
    final public static function set($to,$from,bool $replace=false):bool
    {
        $return = false;
        $to = static::path($to);
        $from = static::path($from);

        if(is_string($to) && is_string($from))
        {
            if($replace === true && Finder::is($to,false))
            {
                if(is_link($to))
                static::unset($to);
                else
                static::unlink($to);
            }

            if(Finder::isReadable($from,false) && !Finder::is($to,false))
            {
                $dirname = Path::dirname($to);

                if(!empty($dirname) && Dir::setOrWritable($dirname))
                $return = symlink($from,$to);
            }
        }

        return $return;
    }


    // sets
    // permet de créer plusieurs symlinks, un tableau from->to doit être fourni
    // retourne un tableau multidimensionnel avec status et from
    // un status a null signifie que le symlink existe déjà (vers le bon chemin)
    // support pour catchAll, et dig si from et to sont des directoires
    final public static function sets(array $array,bool $replace=false,bool $dig=false):array
    {
        $return = [];

        foreach ($array as $from => $to)
        {
            $r = ['status'=>null,'from'=>$from];
            $from = static::normalize($from);
            $to = static::normalize($to);
            $get = static::get($to);
            $go = true;

            // symlink impossible, mais dig et deux directoires
            if($dig === true && $get === null && !static::is($to) && Dir::is($to) && Dir::is($from))
            {
                $go = false;
                $from = Path::append($from,'*');
                $catchAll = Dir::fromToCatchAll([$from=>$to]);

                if(!empty($catchAll))
                {
                    $sets = static::sets($catchAll,$replace);
                    $return = Arr::append($return,$sets);
                }
            }

            // mauvais symlink
            elseif(is_string($get) && !Finder::is($get))
            {
                static::unset($to);
                $get = null;
            }

            if($go === true)
            {
                if($get !== $from)
                $r['status'] = static::set($to,$from,$replace);

                $return[$to] = $r;
            }
        }

        return $return;
    }


    // touch
    // touche un symlink, pour ce faire il faut le détruire et le recréer
    final public static function touch($path):bool
    {
        $return = false;
        $path = static::path($path);

        if(static::isWritable($path,false))
        {
            $target = static::get($path);
            if(!empty($target) && static::unset($path))
            $return = static::set($path,$target);
        }

        return $return;
    }


    // rename
    // renome un symlink
    // le symlink garde le même target après avoir été déplacé
    final public static function rename($target,$path):bool
    {
        $return = false;
        $target = static::path($target);
        $path = static::path($path);

        if(static::isWritable($path,false) && self::isCreatable($target))
        {
            $dirname = Path::dirname($target);
            if(!empty($dirname) && Dir::setOrWritable($dirname))
            $return = rename($path,$target);
        }

        return $return;
    }


    // copy
    // copy un symlink, pour ce faire il faut le recréer
    // le symlink garde la même target, il fait juste se créer à un nouvel endroit
    final public static function copy($to,$path):bool
    {
        $return = false;
        $to = static::path($to);
        $path = static::path($path);

        if(is_string($to) && is_string($path) && static::isReadable($path,false))
        {
            $target = static::get($path);
            if(!empty($target))
            $return = static::set($to,$target);
        }

        return $return;
    }


    // unset
    // efface un symlink, n'efface pas le fichier vers lequel il pointe
    // gestion d'un problème sous windows ou il faut utiliser rmdir si le symlink pointe vers un directoire
    final public static function unset($path):bool
    {
        $return = false;
        $path = static::path($path);

        if(static::isWritable($path,false))
        {
            if(Server::isWindows() && Dir::is($path))
            $return = rmdir($path);

            else
            $return = unlink($path);
        }

        return $return;
    }


    // reset
    // efface et recrée un symlink avec une nouvelle target
    final public static function reset($target,$path):bool
    {
        $return = false;
        $target = static::path($target);
        $path = static::path($path);

        if(is_string($target) && is_string($path))
        {
            static::unset($path);

            if(static::set($path,$target))
            $return = true;
        }

        return $return;
    }
}

// init
Symlink::__init();
?>