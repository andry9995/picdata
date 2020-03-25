<?php

/**
 * Class HttpClient
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk\Core;

use ZendeskBundle\Service\Zendesk\Core\Curl;
use ZendeskBundle\Service\Zendesk\Core\Http;

class HttpClient
{

	/**
	 * @var string
	 *
	 * Nom d'utilisateur
	 */
	private $_user;

	/**
	 * @var string
	 *
	 * Mots de passe de l'utilisateur
	 */
	private $_password;

	/**
	 * @var string
	 *
	 * Authorisation
	 */
	private $_authorization;

	/**
	 * @var string
	 *
	 * Sous dommaine
	 */
	public $endpoint;

	/**
	 * @var const
	 *
	 */
	const API_BASE_ROOT = "/api/v2/";

	/**
	 * Contructeur
	 * Création de l'authorisation
	 *
	 * @param string $user
	 * @param string $password
	 */
	function __construct($config) {
		$this->_user        = $config['username'];
		$this->_password    = $config['password'];

		$this->authEncoder();
	}

	/**
	 * Encodage des informations
	 */
	protected function authEncoder()
	{
		$infos = array(
			'username' => $this->_user,
			'password' => $this->_password
		);

		$this->_authorization = Http::encoder($infos);
	}

	/**
	 * Appel a une methode Http
	 *
	 * @param string $endpoint
	 * @param string $method
	 * @param array/boolean $data
	 *
	 * @return json
	 */
	protected function call(
		$endpoint, 
		$method = 'GET', 
		$data = false
	) {

		return Http::send(
					$endpoint,
					$this->_authorization,
					$method,
					$data
				);
	}

	/**
	 * Création d'un ressource à partir du sous dommaine
	 *
	 * @param string $resourceRoot
	 *
	 * @return string
	 */
	protected function createResourceEndpoint($resourceRoot)
	{

		/**
		 * @example $this->endpoint     => //scriptura6903.zendesk.com
		 *			self::API_BASE_ROOT => /api/v2/
		 *			$resourceRoot 		=> tickets
		 *			return //scriptura6903.zendesk.com/api/tickets
		 */
		return $this->endpoint . self::API_BASE_ROOT . $resourceRoot;
	}
}