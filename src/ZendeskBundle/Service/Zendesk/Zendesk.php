<?php

/**
 * Classe Zendesk
 * Point d'entré du service api.zendesk
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk;

use ZendeskBundle\Service\Zendesk\API\Factory;
use ZendeskBundle\Service\Zendesk\Core\HttpClient;

class Zendesk
{

	/**
	 * @var array()
	 *
	 * Tableau des paramètres de configuration
	 */
	protected $config;

	/**
	 * Constructeur
	 * Réccupération des variables de configuration
	 *
	 * @param array $config
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Instancier la classe correspondant à la base du ressource demandé
	 *
	 * @param string $name
	 *
	 * @return instance of $name class
	 */
	public function api($name)
	{
		return Factory::instanciator(
			$name,
			$this->config
		);
	}

}