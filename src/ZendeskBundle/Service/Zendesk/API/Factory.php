<?php 

/**
 * Classe Factory
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk\API;

class Factory
{
	/**
	 * @var array()
	 *
	 * Tableau d'instance de classe
	 */
	static $instances = array();
	
	/**
	 * Permet d'intancier une classe et de l'enregistrer dans Tableau $instances 
	 * pour éviter les redondances d'instanciation
	 *
	 * @param string $name
	 */
	public static function &instanciator($name, $config)
	{
		$exist = array_key_exists($name, self::$instances);

		if ($exist) {
			return self::$instances[$name]['instance'];
		} else {

			$className  = self::check($name);
			
			$interfaces = class_implements($className);

			if (isset($interfaces[ __NAMESPACE__ . '\IResource'])) {
				$instance = new $className($config);

				self::$instances[$name] = array(
					'instance' => $instance
				);

				$reference =& $instance;

				return $reference;
			}
		}
	}

	/**
	 * Vérification de la classe si le nom existe
	 *
	 * @param string $name
	 *
	 * @return instance of class $name
	 */
	public static function check($name)
	{
		$namespace =  __NAMESPACE__ ;
		$className = "${namespace}\\${name}";

		if (class_exists($className)) {
			return $className;
		}
	}
}