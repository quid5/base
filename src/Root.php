<?php
declare(strict_types=1);

/*
 * This file is part of the Quid 5 package | https://quid5.com
 * (c) Pierre-Philippe Emond <emondpph@gmail.com>
 * License: https://github.com/quid5/base/blob/master/LICENSE
 */

namespace Quid\Base;

// root
abstract class Root
{
	// trait
	use _root;


	// config
	public static $config = [];


	// static
	// remet une copie des propriétés des traits incluent car c'est plus facile à gérer
	protected static $initConfig = []; // voir _config
	protected static $callableConfig = null; // voir _config
	protected static $cacheStatic = []; // voir _cacheStatic
	protected static $cacheFile = null; // voir _cacheFile


	// construct
	// constructeur protégé
	protected function __construct() { }
}
?>