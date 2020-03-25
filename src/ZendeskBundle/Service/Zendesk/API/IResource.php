<?php

/**
 * Interface Resource 
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk\API;

interface IResource {

	/**
	 * Réccupération par id
	 *
	 * @method GET
	 * @param integer $id
	 */
	public function find($id);

	/**
	 * Réccupération de tous les données
	 *
	 * @method GET
	 */
	public function findAll();

	/**
	 * Enregistrement
	 *
	 * @param array $request
	 */
	public function save($request);

	/**
	 * Mise à jour
	 *
	 * @param integer $id
	 * @param array $request
	 */
	public function update(
		$id,
		$request
	);

	/**
	 * Suppression
	 *
	 * @param integer $id
	 */
	public function delete($id);

}