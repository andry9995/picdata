<?php

/**
 * Class Http
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk\Core;

use ZendeskBundle\Service\Zendesk\Core\Curl;

class Http
{
	use Curl;

	/**
	 * Envoi d'une requêtte
	 *
	 * @param string $endpoint
	 * @param string $authorization
	 * @param string $method
	 * @param array/boolean $data
	 *
	 * @return json
	 */
	public static function send(
		$endpoint,
		$authorization,
		$method = 'GET',
		$data   = false
	) {
		switch ($method) {
			case 'GET':
				return self::get($endpoint,$authorization);
				break;
			case 'PUT':
				return self::put($endpoint,$authorization,$data);
				break;
		}
	}

	/**
	 * Création d'une authorisation
	 *
	 * @param array $values
	 *
	 * @return 
	 */
	public static function encoder($value)
	{
		return self::getAuthorization($value);
	}
	
	
}