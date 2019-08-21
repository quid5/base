<?php
declare(strict_types=1);
namespace Quid\Base;

// validate
class Validate extends Root
{
	// config
	public static $config = [
		
		// regex
		'regex'=>[ // liste de regex utilisé à travers le site
			'alpha'=>'/^[A-Za-z]{1,}$/',
			'alphanumeric'=>'/^[A-Za-z0-9]{1,}$/',
			'alphanumericDash'=> '/^[A-Za-z0-9\-\_]{1,}$/',
			'alphanumericSlug'=> '/^[A-Za-z0-9\-]{1,}$/',
			'alphanumericSlugPath'=> '/^[A-Za-z0-9\-\/]{1,}$/',
			'alphanumericPlus'=> '/^[A-Za-z0-9_\-\.\@]{1,}$/',
			'alphanumericPlusSpace'=> '/^[A-Za-z0-9_\-\.\@ ]{1,}$/',
			'username'=>'/^[a-z0-9_-]{4,50}$/i',
			'usernameLoose'=>'/^[a-z0-9_\-\.@]{4,}$/i',
			'password'=>'/^(?=.{5,50})(?=.*\\d)(?=.*[A-z]).*\\z/',
			'passwordLoose'=>'/^.{4,50}$/',
			'passwordHash'=>'/^.{50,100}$/',
			'passwordHashLoose'=>'/^.{50,100}$/',
			'email'=>'/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{1,4})+$/i',
			'hex'=>'/^([a-f0-9]{6}|[a-f0-9]{3})$/i',
			'tag'=>'/^<([a-z]+)([^<]+)*(?:>(.*)<\/\1>|\s+\/>)$/',
			'year'=>'/^\d{4}$/',
			'americanZipcode'=>'/^\d{5}(-\d{4})?$/',    
			'canadianPostalcode'=>'/^([a-z]\d[a-z][-]?\d[a-z]\d)$/i',    
			'northAmericanPhone'=>'/^([\(]{1}[0-9]{3}[\)]{1}[\.| |\-]{0,1}|^[0-9]{3}[\.|\-| ]?)?[0-9]{3}(\.|\-| )?[0-9]{4}$/',
			'phone'=>'/([^\d]*\d){10}/',
			'ip'=>'/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',
			'date'=>'/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/',
			'datetime'=>'/^(\d{2}|\d{4})(?:\-)?([0]{1}\d{1}|[1]{1}[0-2]{1})(?:\-)?([0-2]{1}\d{1}|[3]{1}[0-1]{1})(?:\s)?([0-1]{1}\d{1}|[2]{1}[0-3]{1})(?::)?([0-5]{1}\d{1})(?::)?([0-5]{1}\d{1})$/',                
			'time'=>'/^(([0-1][0-9])|([2][0-3])):([0-5][0-9]):([0-5][0-9])$/',
			'path'=>'/^[A-Za-z0-9_\-\.\/\*]{0,}$/',
			'fqcn'=>'/^[A-z\\\\]{1,}$/',
			'table'=>'/^[A-Za-z]{1}[A-Za-z0-9_]{1,}$/',
			'col'=>'/^[A-Za-z]{1}[A-Za-z0-9_]{1,}$/'
		],
		
		// pattern
		'pattern'=>[ // liste de regex utilisé dans les attributs pattern de html
			'int'=>'[0-9]',
			'numberWholeNotEmpty'=>'^[1-9][0-9]*$',
			'password'=>'^(?=.{5,30})(?=.*\\d)(?=.*[A-z]).*',
			'passwordLoose'=>'^.{4,50}$',
			'minLength'=>".{%%%,}",
			'email'=>'^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{1,4})+$',
			'phone'=>'([^\d]*\d){10}'
		],
		
		// compare
		'compare'=>[ // liste de symboles pour les comparaisons
			'='=>'===',
			'=='=>'==',
			'==='=>'===',
			'>'=>'>',
			'>='=>'>=',
			'<'=>'<',
			'<='=>'<=',
			'!'=>'!==',
			'!='=>'!=',
			'!=='=>'!=='
		],
		
		// one
		// liste de méthodes de validation de type avec un argument
		'one'=>[
			'array'=>'is_array',
			'bool'=>'is_bool',
			'callable'=>'is_callable',
			'float'=>'is_float',
			'int'=>'is_int',
			'numeric'=>'is_numeric',
			'null'=>'is_null',
			'object'=>'is_object',
			'resource'=>'is_resource',
			'scalar'=>'is_scalar',
			'string'=>'is_string',
			'empty'=>[self::class,'isEmpty'],
			'notEmpty'=>[self::class,'isNotEmpty'],
			'reallyEmpty'=>[self::class,'isReallyEmpty'],
			'notReallyEmpty'=>[self::class,'isNotReallyEmpty'],
			'arrKey'=>[Arr::class,'isKey'],
			'arrNotEmpty'=>[Arr::class,'isNotEmpty'],
			'dateToDay'=>[Date::class,'isFormatDateToDay'],
			'dateToMinute'=>[Date::class,'isFormatDateToMinute'],
			'dateToSecond'=>[Date::class,'isFormatDateToSecond'],
			'numberNotEmpty'=>[Number::class,'isNotEmpty'],
			'numberPositive'=>[Number::class,'isPositive'],
			'numberNegative'=>[Number::class,'isNegative'],
			'numberOdd'=>[Number::class,'isOdd'],
			'numberEven'=>[Number::class,'isEven'],
			'numberWhole'=>[Number::class,'isWhole'],
			'numberWholeNotEmpty'=>[Number::class,'isWholeNotEmpty'],
			'numberDecimal'=>[Number::class,'isDecimal'],
			'scalarNotBool'=>[Scalar::class,'isNotBool'],
			'slug'=>[Slug::class,'is'],
			'slugPath'=>[SlugPath::class,'is'],
			'fragment'=>[Slug::class,'is'],
			'strNotEmpty'=>[Str::class,"isNotEmpty"],
			'strLatin'=>[Str::class,"isLatin"],
			'uriRelative'=>[Uri::class,"isRelative"],
			'uriAbsolute'=>[Uri::class,"isAbsolute"],
			'fileUpload'=>[File::class,'uploadValidate'],
			'fileUploads'=>[File::class,'uploadValidates']
		],
		
		// two
		// liste de méthodes de validation de type avec deux arguments
		'two'=>[
			'length'=>[Scalar::class,'isLength'],
			'minLength'=>[Scalar::class,'isMinLength'],
			'maxLength'=>[Scalar::class,'isMaxLength'],
			'arrCount'=>[Arr::class,'isCount'],
			'arrMinCount'=>[Arr::class,'isMinCount'],
			'arrMaxCount'=>[Arr::class,'isMaxCount'],
			'dateFormat'=>[Date::class,'isFormat'],
			'fileCount'=>[File::class,'isCount'],
			'fileMinCount'=>[File::class,'isMinCount'],
			'fileMaxCount'=>[File::class,'isMaxCount'],
			'numberLength'=>[Number::class,'isLength'],
			'numberMinLength'=>[Number::class,'isMinLength'],
			'numberMaxLength'=>[Number::class,'isMaxLength'],
			'jsonCount'=>[Json::class,'isCount'],
			'jsonMinCount'=>[Json::class,'isMinCount'],
			'jsonMaxCount'=>[Json::class,'isMaxCount'],
			'setCount'=>[Set::class,'isCount'],
			'setMinCount'=>[Set::class,'isMinCount'],
			'setMaxCount'=>[Set::class,'isMaxCount'],
			'strLength'=>[Str::class,'isLength'],
			'strMinLength'=>[Str::class,'isMinLength'],
			'strMaxLength'=>[Str::class,'isMaxLength'],
			'uriHost'=>[Uri::class,'isHost'],
			'extension'=>[Path::class,'isExtension']]
	];
	
	
	// is
	// fonction de validation globale
	public static function is($condition,$value,bool $onlyBool=true)
	{
		$return = null;
		
		if(\is_string($condition))
		{
			if(!empty(static::$config['one'][$condition]))
			$return = static::$config['one'][$condition]($value);
			
			elseif(\is_object($value) && \is_a($value,$condition))
			$return = true;
			
			else
			$return = static::regex($condition,$value);
		}
		
		elseif($condition instanceof \Closure)
		$return = $condition($value);
		
		elseif(\is_object($condition))
		$return = static::instance($condition,$value);
		
		elseif(\is_array($condition) && \count($condition) === 1)
		{
			$k = \key($condition);
			$v = \current($condition);

			if(!empty(static::$config['compare'][$k]))
			$return = static::compare($value,$k,$v);
			
			elseif(!empty(static::$config['two'][$k]))
			$return = static::two($k,$v,$value);
		}
		
		if(!\is_bool($return))
		{
			if(static::classIsCallable($condition))
			$return = $condition($value);
			
			if(!\is_bool($return) && $onlyBool === true)
			$return = false;
		}

		return $return;
	}
	
	
	// isNot
	// inverse de is
	public static function isNot($condition,$value):bool
	{
		return (static::is($condition,$value))? false:true;
	}
	
	
	// isCom
	// fait la validation
	// si la validation est fausse, retourne un tableau qui sera utilisé pour retourner une explication via lang
	// si la condition est une closure, la closure peut retourner une string ou un array comme message (plutôt que la clé)
	public static function isCom($condition,$value,$key=null) 
	{
		$return = static::is($condition,$value,false);

		if($return !== true && !\is_string($return) && !\is_array($return))
		{
			if(\is_string($condition))
			$return = $condition;
			
			elseif($condition instanceof \Closure)
			$return = (\is_string($key))? $key:'closure';
			
			elseif(static::classIsCallable($condition))
			$return = (\is_string($key))? $key:'callable';
			
			elseif(\is_object($condition))
			$return = ['instance'=>\get_class($condition)];
			
			elseif(\is_array($condition) && \count($condition) === 1)
			$return = $condition;
		}
		
		return $return;
	}


	// isAnd
	// plusieurs conditions
	// boucle de type and
	public static function isAnd(array $conditions,$value):bool
	{
		$return = false;
		
		foreach (static::prepareConditions($conditions) as $condition) 
		{
			$return = static::is($condition,$value);

			if($return === false)
			break;
		}
		
		return $return;
	}
	
	
	// isAndCom
	// plusieurs conditions, boucle de type and
	// retourne true ou un tableau utilisé pour générer des messages d'explications sur l'erreur de validation
	public static function isAndCom(array $conditions,$value)
	{
		$return = [];

		foreach (static::prepareConditions($conditions) as $k => $condition) 
		{
			$com = static::isCom($condition,$value,$k);

			if($com !== true)
			$return[] = $com;
		}
		
		if(empty($return))
		$return = true;
		
		return $return;
	}
	
	
	// isOr
	// plusieurs conditions
	// boucle de validation de type or
	public static function isOr(array $conditions,$value):bool
	{
		$return = false;
		
		foreach (static::prepareConditions($conditions) as $condition) 
		{
			$return = static::is($condition,$value);
			
			if($return === true)
			break;
		}
		
		return $return;
	}
	
	
	// isXor
	// plusieurs conditions
	// boucle de validation de type xor, une seule condition peut être vrai
	public static function isXor(array $conditions,$value):bool
	{
		$return = false;
		
		foreach (static::prepareConditions($conditions) as $condition) 
		{
			$bool = static::is($condition,$value);
			
			if($bool === true)
			{
				if($return === true)
				{
					$return = false;
					break;
				}
				
				else
				$return = true;
			}
		}
		
		return $return;
	}
	
	
	// are
	// plusieurs valeurs, une condition
	// une validation avec type ou closure est plus rapide
	public static function are($condition,...$values):bool
	{
		$return = false;
		$callable = null;
		
		if(\is_string($condition) && !empty(static::$config['type'][$condition]))
		$callable = static::$config['type'][$condition];
			
		elseif($condition instanceof \Closure)
		$callable = $condition;
		
		foreach ($values as $value) 
		{
			if(!empty($callable))
			$return = $callable($value);
			else
			$return = static::is($condition,$value);
			
			if(!$return)
			break;
		}
		
		return $return;
	}
	
	
	// areNot
	// plusieurs valeurs, une condition
	// inverse de are
	public static function areNot($condition,...$values):bool
	{
		return (static::are($condition,...$values))? false:true;
	}
	
	
	// areAnd
	// plusieurs valeurs, plusieurs conditions
	// boucle de type and
	public static function areAnd(array $conditions,...$values):bool
	{
		$return = false;
		
		foreach ($values as $value) 
		{
			$return = static::isAnd($conditions,$value);
			
			if($return === false)
			break;
		}
		
		return $return;
	}
	
	
	// areOr
	// plusieurs valeurs, plusieurs conditions
	// boucle de type or
	public static function areOr(array $conditions,...$values):bool
	{
		$return = false;
		
		foreach ($values as $value) 
		{
			$return = static::isOr($conditions,$value);
			
			if($return === false)
			break;
		}
		
		return $return;
	}
	
	
	// areXor
	// plusieurs valeurs, plusieurs conditions
	// boucle de type xor
	public static function areXor(array $conditions,...$values):bool 
	{
		$return = false;
		
		foreach ($values as $value) 
		{
			$return = static::isXor($conditions,$value);
			
			if($return === false)
			break;
		}
		
		return $return;
	}
	
	
	// one
	// permet de faire des appels de validation avec méthode de 1 argument
	public static function one(string $key,$arg):bool
	{
		$return = false;
		
		if(!empty(static::$config['one'][$key]))
		{
			$return = static::$config['one'][$key]($arg);
			
			if(!\is_bool($return))
			$return = false;
		}
		
		return $return;
	}
	
	
	// two
	// permet de faire des appels de validation avec méthode de 2 arguments
	public static function two(string $key,$value,$arg):bool 
	{
		$return = false;
		
		if(\array_key_exists($key,static::$config['two']) && static::classIsCallable(static::$config['two'][$key]))
		{
			$return = static::$config['two'][$key]($value,$arg);
			
			if(!\is_bool($return))
			$return = false;
		}
		
		return $return;
	}
	
	
	// regex
	// wrapper pour les regex
	// par défaut la classe va prendre les regex à partir du tableau config
	public static function regex(string $input,$value):bool 
	{
		$return = false;
		
		if(\is_scalar($value) && !\is_bool($value))
		{
			$regex = null;
			$value = (string) $value;
			
			if(!empty(static::$config['regex'][$input]))
			$regex = static::$config['regex'][$input];
			
			elseif(\strpos($input,'/') !== false)
			$regex = $input;
			
			if(\is_string($regex) && \preg_match($regex,$value))
			$return = true;
		}
		
		return $return;
	}
	
	
	// preg
	// wrapper pour la fonction preg_match
	public static function preg(string $regex,$value):bool
	{
		$return = false;
		
		if(\is_scalar($value))
		{
			$value = (string) $value;
			if(\preg_match($regex,$value))
			$return = true;
		}
		
		return $return;
	}
	
	
	// instance
	// valide que la valeur est une instance de la classe
	public static function instance($class,$value):bool 
	{
		$return = false;
		
		if((\is_object($class) || \is_string($class)) && (\is_object($value) || \is_string($value)))
		{
			$class = \get_class($class);
			if(\is_a($value,$class,true))
			$return = true;
		}
		
		return $return;
	}
	
	
	// isCompareSymbol
	// retourne vrai si le symbole est un symbol de comparaison valide
	public static function isCompareSymbol($symbol):bool 
	{
		return (\is_string($symbol) && \array_key_exists($symbol,static::$config['compare']))? true:false;
	}
	
	
	// compare
	// fonction de comparaison entre deux chaines
	public static function compare($value1,string $symbol='===',$value2):bool
	{
		$return = false;
		
		if(static::isCompareSymbol($symbol))
		{
			$symbol = static::$config['compare'][$symbol];
			
			if($symbol==='===')
			$return = ($value1 === $value2)? true:false;

			elseif($symbol==='==')
			$return = ($value1 == $value2)? true:false;

			elseif($symbol==='!=')
			$return = ($value1 != $value2)? true:false;

			elseif($symbol==='!==')
			$return = ($value1 !== $value2)? true:false;

			elseif($symbol==='<')
			$return = ($value1 < $value2)? true:false;

			elseif($symbol==='<=')
			$return = ($value1 <= $value2)? true:false;

			elseif($symbol==='>')
			$return = ($value1 > $value2)? true:false;

			elseif($symbol==='>=')
			$return = ($value1 >= $value2)? true:false;
		}
		
		return $return;
	}
	
	
	// pattern
	// retourne la valeur regex du premier pattern trouvé à partir de l'argument value
	// value peut être une string ou un array
	// si c'est un array et que la clé est string, value est utiliser pour remplacer %%% dans le pattern
	// si rien n'a été trouvé mais que value est une string non vide, retourne value
	public static function pattern($value):?string 
	{
		$return = null;
		$key = static::patternKey($value);
		
		if($key !== null)
		{
			if(\is_string($key))
			$return = static::$config['pattern'][$key] ?? null;
			
			elseif(\is_array($key) && \count($key) === 1)
			{
				$k = \key($key);
				$v = \current($key);
				
				$return = static::$config['pattern'][$k] ?? null;
				
				if(\is_scalar($v) && !\is_bool($v) && \is_string($return))
				{
					$v = (string) $v;
					$return = \str_replace("%%%",$v,$return);
				}
			}
		}

		if(empty($return) && \is_string($value) && !empty($value))
		$return = $value;
		
		return $return;
	}
	
	
	// patternKey
	// retourne le nom du premier pattern trouvé à partir de l'argument value
	// peut retourner string ou array
	public static function patternKey($value) 
	{
		$return = null;
		$array = (!\is_array($value))? [$value]:$value;
		
		foreach ($array as $k => $v) 
		{
			if(\is_numeric($k) && \is_string($v) && \array_key_exists($v,static::$config['pattern']))
			$return = $v;
			
			elseif(\is_string($k) && \array_key_exists($k,static::$config['pattern']))
			$return = [$k=>$v];
			
			if($return !== null)
			break;
		}
		
		return $return;
	}
	
	
	// prepareConditions
	// prépare le tableau pour plusieurs conditions
	// méthode protégé
	protected static function prepareConditions(array $conditions):array 
	{
		$return = [];
		
		foreach ($conditions as $key => $value) 
		{
			if(\is_numeric($key))
			$return[] = $value;
			
			elseif(static::classIsCallable($value))
			$return[$key] = $value;
			
			else
			$return[] = [$key=>$value];
		}
		
		return $return;
	}
	

	// sameType
	// vérifie que toutes les valeurs donnés ont le même type ou la même instance de classe
	public static function sameType(...$values):bool
	{
		$return = false;
		
		foreach ($values as $v) 
		{
			if(!empty($type))
			{
				if(\gettype($v) !== $type)
				$return = false;
				
				elseif(!empty($class) && (!\is_object($v) || !\is_a($v,$class)))
				$return = false;
				
				else
				$return = true;
				
				if($return === false)
				break;
			}
			
			$type = \gettype($v);
			$class = (\is_object($v))? \get_class($v):false;
		}
		
		return $return;
	}
	

	// isEmpty
	// retourne vrai si empty
	public static function isEmpty($value):bool
	{
		return (empty($value))? true:false;
	}
	
	
	// isNotEmpty
	// inverse de isEmpty
	public static function isNotEmpty($value):bool
	{
		return (!static::isEmpty($value))? true:false;
	}
	
	
	// isReallyEmpty
	// retourne vrai si empty, sans etre numérique ni boolean ni une string avec une longueur
	// en somme, ca retourne faux pour 0, '0' et false
	// si removeWhiteSpace est true et que c'est une string, envoie dans str::removeWhiteSpace avant
	public static function isReallyEmpty($value,bool $removeWhiteSpace=false):bool
	{
		$return = false;
		
		if($removeWhiteSpace === true && \is_string($value))
		$value = Str::removeWhiteSpace($value);
		
		$return = (empty($value) && !\is_numeric($value) && !\is_bool($value) && !(\is_string($value) && \strlen($value)))? true:false;
		
		return $return;
	}
	
	
	// isNotReallyEmpty
	// inverse de isReallyEmpty
	public static function isNotReallyEmpty($value,bool $removeWhiteSpace=false):bool
	{
		return (static::isReallyEmpty($value,$removeWhiteSpace))? false:true;
	}
	

	// isAlpha
	// retourne vrai si la valeur passe le regex alpha
	public static function isAlpha($value):bool
	{
		return static::regex("alpha",$value);
	}
	
	
	// isAlphanumeric
	// retourne vrai si la valeur passe le regex alphanumeric
	public static function isAlphanumeric($value):bool
	{
		return static::regex("alphanumeric",$value);
	}
	
	
	// isAlphanumericDash
	// retourne vrai si la valeur passe le regex isAlphanumericDash
	public static function isAlphanumericDash($value):bool
	{
		return static::regex("alphanumericDash",$value);
	}
	
	
	// isAlphanumericSlug
	// retourne vrai si la valeur passe le regex alphanumericSlug
	public static function isAlphanumericSlug($value):bool
	{
		return static::regex("alphanumericSlug",$value);
	}
	
	
	// isAlphanumericSlugPath
	// retourne vrai si la valeur passe le regex isAlphanumericSlugPath
	public static function isAlphanumericSlugPath($value):bool
	{
		return static::regex("alphanumericSlugPath",$value);
	}
	
	
	// isAlphanumericPlus
	// retourne vrai si la valeur passe le regex alphanumericPlus
	public static function isAlphanumericPlus($value):bool
	{
		return static::regex("alphanumericPlus",$value);
	}
	
	
	// isAlphanumericPlusSpace
	// retourne vrai si la valeur passe le regex alphanumericPlusSpace
	public static function isAlphanumericPlusSpace($value):bool
	{
		return static::regex("alphanumericPlusSpace",$value);
	}
	
	
	// isUsername
	// retourne vrai si la valeur passe le regex username
	// possible de spécifier un niveau de sécurité pour le regex
	public static function isUsername($value,?string $security=null):bool
	{
		return static::regex('username'.((\is_string($security))? \ucfirst($security):''),$value);
	}
	
	
	// isPassword
	// retourne vrai si la valeur passe le regex password
	// possible de spécifier un niveau de sécurité pour le regex
	public static function isPassword($value,?string $security=null):bool
	{
		return static::regex('password'.((\is_string($security))? \ucfirst($security):''),$value);
	}
	
	
	// isEmail
	// retourne vrai si la valeur passe le regex email
	public static function isEmail($value):bool
	{
		return static::regex("email",$value);
	}
	
	
	// isHex
	// retourne vrai si la valeur passe le regex hex
	public static function isHex($value):bool
	{
		return static::regex("hex",$value);
	}
	
	
	// isTag
	// retourne vrai si la valeur passe le regex tag
	public static function isTag($value):bool
	{
		return static::regex("tag",$value);
	}
	
	
	// isYear
	// retourne vrai si la valeur passe le regex year
	public static function isYear($value):bool
	{
		return static::regex("year",$value);
	}
	
	
	// isAmericanZipcode
	// retourne vrai si la valeur passe le regex zipcode
	public static function isAmericanZipcode($value):bool
	{
		return static::regex("americanZipcode",$value);
	}
	
	
	// isCanadianPostalcode
	// retourne vrai si la valeur passe le regex postalcode
	public static function isCanadianPostalcode($value):bool
	{
		return static::regex("canadianPostalcode",$value);
	}
	
	
	// isNorthAmericanPhone
	// retourne vrai si la valeur passe le regex americanPhone
	public static function isNorthAmericanPhone($value):bool
	{
		return static::regex("northAmericanPhone",$value);
	}
	
	
	// isPhone
	// retourne vrai si la valeur passe le regex phone
	public static function isPhone($value):bool
	{
		return static::regex("phone",$value);
	}
	
	
	// isIp
	// retourne vrai si la valeur passe le regex ip
	public static function isIp($value):bool
	{
		return static::regex("ip",$value);
	}
	
	
	// isDate
	// retourne vrai si la valeur passe le regex date
	public static function isDate($value):bool
	{
		return static::regex("date",$value);
	}
	
	
	// isDatetime
	// retourne vrai si la valeur passe le regex datetime
	public static function isDatetime($value):bool
	{
		return static::regex("datetime",$value);
	}
	
	
	// isTime
	// retourne vrai si la valeur passe le regex time
	public static function isTime($value):bool
	{
		return static::regex("time",$value);
	}


	// isPath
	// retourne vrai si la valeur passe le regex path
	public static function isPath($value):bool
	{
		return static::regex("path",$value);
	}


	// isFqcn
	// retourne vrai si la valeur passe le regex fqcn
	public static function isFqcn($value):bool
	{
		return static::regex("fqcn",$value);
	}
	
	
	// isTable
	// retourne vrai si la valeur passe le regex table
	public static function isTable($value):bool
	{
		return static::regex("table",$value);
	}
	
	
	// isCol
	// retourne vrai si la valeur passe le regex col
	public static function isCol($value):bool
	{
		return static::regex("col",$value);
	}
	
	
	// isEqual
	// retourne vrai si les deux valeurs sont égales ===
	public static function isEqual($value1,$value2):bool 
	{
		return static::compare($value1,'===',$value2);
	}
	
	
	// isSoftEqual
	// retourne vrai si les deux valeurs sont égales ==
	public static function isSoftEqual($value1,$value2):bool 
	{
		return static::compare($value1,'==',$value2);
	}
	
	
	// isInequal
	// retourne vrai si les deux valeurs sont inégales !==
	public static function isInequal($value1,$value2):bool 
	{
		return static::compare($value1,'!==',$value2);
	}
	
	
	// isSoftInequal
	// retourne vrai si les deux valeurs sont inégales !=
	public static function isSoftInequal($value1,$value2):bool 
	{
		return static::compare($value1,'!=',$value2);
	}
	
	
	// isBigger
	// retourne vrai si la première valeur est plus grande
	public static function isBigger($value1,$value2):bool 
	{
		return static::compare($value1,'>',$value2);
	}
	
	
	// isBiggerOrEqual
	// retourne vrai si la première valeur est plus grande ou égale
	public static function isBiggerOrEqual($value1,$value2):bool 
	{
		return static::compare($value1,'>=',$value2);
	}
	
	
	// isSmaller
	// retourne vrai si la première valeur est plus petite
	public static function isSmaller($value1,$value2):bool 
	{
		return static::compare($value1,'<',$value2);
	}
	
	
	// isSmallerOrEqual
	// retourne vrai si la première valeur est plus petite ou égale
	public static function isSmallerOrEqual($value1,$value2):bool 
	{
		return static::compare($value1,'<=',$value2);
	}
	
	
	// arr
	// permet de faire un maximum de validation sur un tableau
	// on peut faire une validation sur l'ensemble ou sur certaine clé
	// les méthodes isEmpty et isNotEmpty sont utilisés pour la validation de clé dans le tableau (true/false)
	// utiliser par core/routeMatch
	public static function arr($is,array $array,bool $removeWhiteSpace=false):bool
	{
		$return = false;
		
		if($is === null)
		$return = true;
		
		elseif($is === true && !empty($array))
		$return = true;
		
		elseif($is === false && empty($array))
		$return = true;
		
		elseif(\is_int($is) && \count($array) === $is)
		$return = true;
		
		elseif(\is_string($is) && \array_key_exists($is,$array))
		$return = true;
		
		// array
		elseif(\is_array($is))
		{
			$r = false;
			
			foreach ($is as $k => $v) 
			{
				$r = false;
				
				if(\is_numeric($k) && \is_string($v) && \array_key_exists($v,$array))
				$r = true;
				
				elseif(\is_string($k) && \array_key_exists($k,$array))
				{
					if($v === true && static::isNotReallyEmpty($array[$k],$removeWhiteSpace))
					$r = true;
					
					elseif($v === false && static::isReallyEmpty($array[$k],$removeWhiteSpace))
					$r = true;
					
					elseif(\is_int($v) && $array[$k] === $v)
					$r = true;
					
					elseif(!empty($v) && static::is($v,$array[$k]))
					$r = true;
				}
				
				if($r === false)
				break;
			}
			
			if($r === true)
			$return = true;
		}
		
		return $return;
	}
	
	
	// dig
	// valide que toutes les valeurs non array dans la valeur remplisse la condition
	// creuse dans le tableau s'il est multidimensionnel
	public static function dig($condition,$value):bool
	{
		$return = false;

		if(\is_array($value))
		{
			foreach ($value as $v) 
			{
				if(\is_array($v))
				{
					$return = static::dig($condition,$v);
				}
				
				else
				$return = static::is($condition,$v);
				
				if($return === false)
				break;
			}
		}
		
		elseif(static::is($condition,$value))
		$return = true;
		
		return $return;
	}
}
?>