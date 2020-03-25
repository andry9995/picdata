<?php

/**
 * Ticket class
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Service\Zendesk\API;

use ZendeskBundle\Service\Zendesk\API\Resource;
use ZendeskBundle\Service\Zendesk\API\User;

class Ticket extends Resource 
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
	public $routes = [
		'comments'           => "tickets/{id}/comments.json",
		'audits'           => "tickets/{id}/audits.json",
		'metrics'           => "tickets/{id}/metrics.json",
		'showmany'           => "tickets/show_many.json?ids={ids}",
		'findProblemsByTag' => "problems/autocomplete.json?text={tag}",
		'findByRecipient'    => "search.json?query=recipient:{recipient}",
		'findByRecipientWhereStatus'    => "search.json?query=recipient:{recipient} status:{status}",
		'search' => "search.json?query={word} recipient:{recipient}"
	];

	public function search($recipient,$word)
	{
		$params = array(
			'word' => $word,
			'recipient' => $recipient
		);

		return $this->execute($params);
	}

	/**
	 * Constructeur
	 */
	function __construct($config) {
		parent::__construct($config);

		$this->endpoint = $config['subdomaine'];
	}

	public function findByRecipientWhereStatus($recipient,$status)
	{
		$params = array(
			'recipient' => $recipient,
			'status' => $status
		);

		return $this->execute($params);
	}

	public function findByRecipient($recipient)
	{
		$params = array(
			'recipient' => $recipient
		);

		return $this->execute($params);
	}


	/**
	 * Afficher plusieurs tickets
	 *
	 * @param array $listId
	 *
	 * @return array
	 */
	public function showmany($listId)
	{
		$ids    = implode(",", $listId);
		
		$params = array(
			'ids' => $ids
		);
		return $this->execute($params);

	}

	/**
	 * Liste des commentaires d'un ticket
	 *
	 * @param integer $id
	 *
	 * @return array
	 */
	public function comments($id)
	{
		$params = array(
			'id' => $id
		);
		return $this->execute($params);
	}

	/**
	 * Liste des audits d'un ticket
	 *
	 * @param integer $id
	 *
	 * @return array
	 */
	public function audits($id)
	{
		$params = array(
			'id' => $id
		);
		return $this->execute($params);
	}

	/**
	 * Liste des metrics d'un ticket
	 *
	 * @param integer $id
	 *
	 * @return array
	 */
	public function metrics($id)
	{
		$params = array(
			'id' => $id
		);
		return $this->execute($params);
	}

	/**
	 * Recherche tickets par type
	 *
	 * @param string $type
	 *
	 * @return array
	 */
	public function findByType($type)
	{
		$all     = $this->findAll()->tickets;
		$tickets = array();

		foreach ($all as $key => $ticket) {
			if ($ticket->type == $type) {
				array_push($tickets, $ticket);
			}
		}

		return $tickets;

	}

	/**
	 * Recherche tickets de type problème par mots clé
	 *
	 * @param string $tag
	 *
	 * @return array 
	 */
	public function findProblemsByTag($tag)
	{
		$params = array(
			'tag' => $tag
		);

		return $this->execute($params);

	}

	/**
	 * Recherche tickets par priorités
	 *
	 * @param string $priority
	 *
	 * @return array
	 */
	public function findByPriority($priority)
	{
		$all     = $this->findAll()->tickets;
		$tickets = array();

		foreach ($all as $key => $ticket) {
			if ($ticket->priority == $priority) {
				array_push($tickets, $ticket);
			}
		}

		return $tickets;
	}

	/**
	 * Recheche tickets par attribut
	 *
	 * @param string attribute
	 * @param string $argument
	 * @example findBy('priority','urgent')
	 *
	 * @return array
	 *
	 */
	public function findBy($attribute, $argument)
	{
		$method = __FUNCTION__ . ucfirst($attribute);

		return $this->$method($argument);

	}

	/**
	 * Recherche tickets par status
	 *
	 * @param string $status
	 *
	 * @return array
	 */
	public function findByStatus($status)
	{
		$all     = $this->findAll()->tickets;
		$tickets = array();

		foreach ($all as $key => $ticket) {
			if ($ticket->status == $status) {
				array_push($tickets, $ticket);
			}
		}

		return $tickets;
	}

	/**
	 * Recherche tickets par mots clé
	 *
	 * @param string $tag
	 *
	 * @return array
	 */
	public function findAllByTag($tag)
	{
		$all     = $this->findAll()->tickets;
		$tickets = array();

		foreach ($all as $key => $ticket) {
			$tagged = false;

			/** Recheche dans la liste des tags tu ticket */
			if (!empty($ticket->tags)) {
				$tags = $ticket->tags;
				foreach ($tags as $word) {
					if ((strtoupper($word) == strtoupper($tag)) || (levenshtein(strtoupper($word),strtoupper($tag)) <= 2)) {
						array_push($tickets, $ticket);
						$tagged = true;
					}					
				}
			}

			if (!$tagged) {
				/** Recherche du mots clé dans la description */
				$descriptionText = strtoupper($ticket->subject) . ' ' . strtoupper($ticket->description); 
				if(strpos($descriptionText,strtoupper($tag)) !== false) {
					array_push($tickets, $ticket);
				} else {
					/**Recherche par distance de levenshtein entre mots clé et chaque mots du description */
					$descriptions = explode(' ', $ticket->subject . ' ' . $ticket->description);
					foreach ($descriptions as $word) {
						/** Meilleur coût = distanc inférieur à 3 */
						if (levenshtein(strtoupper($word), strtoupper($tag)) <= 2) {
							array_push($tickets, $ticket);
							break;
						}
					}
				}
			}

		}

		return $tickets;
	}

	/**
	 * Recherche tickets avec multiple condition
	 *
	 * @param array $where
	 * @example findWhere(array(
	 *			 'type'     => 'incident',
	 *			 'priority' => 'normal',
	 *			 'status'   => 'open'
	 *			));
	 *
	 * @return array
	 */
	public function findWhere($where)
	{
		$all     = $this->findAll()->tickets;
		$tickets = array();

		foreach ($all as $ticket) {
			$is = true;
			foreach ($where as $key => $value) {
				if ($ticket->$key != $value) {
					$is = false;
					break;
				}
			}
			if ($is) 
				array_push($tickets, $ticket);
		}
		return $tickets;
	}

	/**
	 * Mise à jour d'un ticket
	 *
	 * @param integer $id
	 * @param array $fields
	 *
	 * @return array
	 */
	public function update($id,$fields)
	{
		$data = array(
			'ticket' => $fields
		);

		return parent::update($id,$data);
	}

	/**
	 * Mise à jour du statut
	 *
	 * @param integer $id
	 * @param string $newStat
	 *
	 * @return array
	 */
	public function changeStatus($id, $newStat)
	{
		$fields = array(
			'status' => $newStat
		);

		return $this->update($id,$fields);
	}

	/**
	 * Mise à jour de la priorité
	 *
	 * @param integer $id
	 * @param string $newPriority
	 *
	 * @return array
	 */
	public function changePriority($id, $newPriority)
	{
		$fields = array(
			'priority' => $newPriority
		);

		return $this->update($id,$fields);
	}

	/**
	 * Mise à jour type
	 *
	 * @param integer $id
	 * @param string $newType
	 *
	 * @return array
	 */
	public function changeType($id, $newType)
	{
		$fields = array(
			'type' => $newType
		);

		return $this->update($id,$fields);
	}

	public function getNextPage($nextPageUrl)
	{
		return $this->getContent($nextPageUrl);
	}

}