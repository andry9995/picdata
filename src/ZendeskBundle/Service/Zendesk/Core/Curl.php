<?php

/**
 * Classe Curl
 *
 * @package Picdata
 *
 * @author Scriptura
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk\Core;

trait Curl
{

	/**
	 * Methode PUT
	 *
	 * @param string $endpoint
	 * @param string $authorization
	 * @param array $data
	 *
	 * @return string
	 */
	public static function put(
		$endpoint, 
		$authorization, 
		$data
	) {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt_array($curl, array(
			CURLOPT_URL            => $endpoint,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "PUT",
			CURLOPT_POSTFIELDS     => json_encode($data),
			CURLOPT_HTTPHEADER     => array(
			    "authorization: " . $authorization,
			    "cache-control: no-cache",
			    "content-type: application/json",
		  	),
		));

		$response = curl_exec($curl);
		$err      = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  $response = "cURL Error #:" . $err;
		}

		return $response;
	}

	/**
	 * Methode GET 
	 *
	 * @param string $endpoint
	 * @param string $authorization
	 *
	 * @return string
	 */
	public static function get(
		$endpoint,
		$authorization
	) {
		$curl = curl_init();

		ini_set("max_execution_time", 2000);

		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt_array($curl, array(
			CURLOPT_URL            => $endpoint,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "GET",
			CURLOPT_HTTPHEADER     => array(
				"authorization: " . $authorization,
				"cache-control: no-cache"
		  	),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  $response = "cURL Error #:" . $err;
		} 

		return $response;
	}

	/**
	 * Methode POST
	 *
	 * @param array $request
	 * @param string $authorization
	 *
	 */
	public static function post(
		$request, 
		$authorization
	) {
		# code...
	}

	/**
	 * Creation d'authorisation
	 *
	 * @param string $type
	 * @param array $informations
	 */
	public static function getAuthorization(
		$informations,
		$type = 'Basic'
	) {

		$authorization = '';

		switch ($type) {
			case 'Basic':
				$authorization = 'Basic ' . base64_encode($informations['username'] . ':' . $informations['password']);
				break;
		}

		return $authorization;


	}
	
	
}