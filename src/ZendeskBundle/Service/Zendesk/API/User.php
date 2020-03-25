<?php

/**
 * Classe User
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk\API;

use ZendeskBundle\Service\Zendesk\API\Resource;

class User extends Resource 
{
	
	/**
	 * @var String
	 *
	 */
	public $endpoint;

	/**
	 * @var array
	 *
	 * Routes pour les methodes spécifiques
	 */
	public $routes = [];

	/**
	 * Constructeur
	 */
	function __construct($config) {
		parent::__construct($config);
		$this->endpoint = $config['subdomaine'];
	}

}