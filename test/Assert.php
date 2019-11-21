<?php
declare(strict_types=1);

/*
 * This file is part of the QuidPHP package.
 * Author: Pierre-Philippe Emond <emondpph@gmail.com>
 * Website: https://quidphp.com
 * License: https://github.com/quidphp/base/blob/master/LICENSE
 * Readme: https://github.com/quidphp/base/blob/master/README
 */

namespace Quid\Test\Base;
use Quid\Base;

// assert
// class for testing Quid\Base\Assert
class Assert extends Base\Test
{
    // trigger
    final public static function trigger(array $data):bool
    {
        // call
        assert(Base\Assert::call(function() { return true; },'what'));

        // get
        assert(Base\Assert::get(ASSERT_ACTIVE) === 1);

        // set

        // setHandler

        return true;
    }
}
?>