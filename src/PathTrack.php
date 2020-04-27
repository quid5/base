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

// pathTrack
// class with static methods to deal with filesystem paths (without a starting slash)
final class PathTrack extends Path
{
    // config
    protected static array $config = [
        'option'=>[ // tableau d'options
            'start'=>null, // aucun changement au séparateur au début lors du implode
            'end'=>null] // aucun changement au séparateur à la fin lors du implode
    ];
}

// init
PathTrack::__init();
?>