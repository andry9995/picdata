<?php

/**
 * Classe Abstrait Resource
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk\API;

use ZendeskBundle\Service\Zendesk\API\IResource;
use ZendeskBundle\Service\Zendesk\Core\HttpClient;

abstract class Resource extends HttpClient implements IResource {

	/**
	 * @var string
	 *
	 */
	protected $basepath = "";


	/**
	 * @var array
	 */
	public $routes = [];
	
	/**
	 * Constructeur
	 * Appel de la constructeur du parent HttpClient
	 * Création du basepath
	 */
	function __construct($config) {
		parent::__construct($config);

		$this->basepath = $this->getResourceName();
	}

	/**
	 * Nom du resource par class fille
	 *
	 * @return string
	 */
	private function getResourceName()
	{

		/**
		 * namespace avec nom de la classe
		 */
		$namespacedClassName = get_class($this);

        /**
         *  Nom de la classe sans namespace
         */
        $className           = join('', array_slice(explode('\\', $namespacedClassName), -1));

        /**
         * Transformer en minuscule et espacer d'underscore
         * @example Ticket  => ticket
         *       	MyClass => my_class
         */
        $underscored         = strtolower(preg_replace('/(?<!^)([A-Z])/', '_$1', $className));

        /**
         * Ajout de "s" a la fin pour avoir une ressource valide
         */
        $resourceName        = $underscored . "s";

        /**
         * @example return tickets
         */
        return $resourceName;

	}

	/**
	 * Lien de l'api
	 *
	 * @return string
	 */
	private function apiPath()
	{
		/**
		 * @example //scriptura6903.zendesk.com/api/tickets
		 */
		return $this->createResourceEndpoint($this->basepath);
	}

	protected function getContent($url)
	{
		return json_decode(
			$this->call($url)
		);
	}

	/**
	 * Appel de l'api
	 *
	 * @param array $params
	 *
	 * @return json
	 */
	protected function execute($params = [])
	{
		if (array_key_exists('subresource', $params)) {

			if (array_key_exists('method', $params)) {
				switch ($params['method']) {		
					case 'PUT':
						$response = $this->call(
							$this->apiPath() . "/" . $params['subresource'],
							'PUT',
							$params['data']
						);
						return json_decode($response);
						break;
				}
			} else{
				return json_decode($this->call($this->apiPath() . "/" . $params['subresource']));
			}


		} else {
			$caller = debug_backtrace()[1]['function'];
			$route = $this->getRoute($caller,$params);
			$route = $this->encodeQuery($route);
			return json_decode(
				$this->call(
					$this->createResourceEndpoint(
						$route
					)
				)
			);
		}
	}

	public function encodeQuery($route)
	{
		$part = substr($route,0,18);
		if ( $part === 'search.json?query=') {
			$query = substr($route, 18, strlen($route));
			$query = str_replace(':', '%3A', $query);
			$query = str_replace(' ', '+', $query);
			$route = $part . $query;			
		}
		return $route;
	}


	/**
	 * getRoute
	 *
	 * @param string $path
	 * @param array $params
	 *
	 * @return string
	 */
	public function getRoute($path, array $params = [])
	{
		if (empty($params)) {
			return $this->routes[$path];
		} else {
			$route = $this->routes[$path];
			foreach ($params as $name => $value) {
				if (is_scalar($value)) {
                	$route = str_replace(
                		'{' . $name . '}', $value, $route);
				} 
			}
        	return $route;
		}

	}


	/**
	 * Réccupération d'un Ticket par id
	 *
	 * @param integer $id
	 *
	 * @return json
	 */
	public function find($id){
		return $this->execute(array(
			'subresource' => $id
		));
	}

	/**
	 * Réccupération de tous les Tickets
	 *
	 * @return json
	 */
	public function findAll(){
		return $this->execute(array(
			'subresource' => '/'
		));
	}

	/**
	 * Enregistrement d'un ticket
	 *
	 * @todo à faire
	 * @param array $request
	 */
	public function save($request){
		# code...
	}

	/**
	 * Mise à jour d'un Ticket
	 *
	 * @param integer $id
	 * @param array $request
	 */
	public function update(
		$id,
		$request
	){
		$response =  $this->execute(array(
			'subresource' => $id,
			'method'	  => 'PUT',
			'data'		  => $request
		));

		return $response;
	}

	/**
	 * Suppression d'un Ticket
	 *
	 * @todo à faire
	 * @param integer $id
	 */
	public function delete($id){
		# code...
	}


}