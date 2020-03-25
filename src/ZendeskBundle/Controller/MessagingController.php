<?php

/**
 * MessagingController
 *
 * @package Picdata
 *
 * @author Scriptura <andry>
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Controller;

use ZendeskBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;

class MessagingController extends DefaultController
{
	/**
	 * Liste des auteurs
	 *
	 * @var array()
	 */
	public $author = array();

	/**
	 * Titre sur la boite de recherche
	 *
	 * @var string
	 */
	public $title = "";

	/**
	 * Nombre total des tickets
	 *
	 * @var integer
	 */
	public $count = 0;
	
	/**
	 * Liste des badge par statut
	 *
	 * @var array()
	 */
	public $status = array(
		'new'    => '<span class="status-badge status-new badge-container" title="Nouveau"> <i class="fa fa-lightbulb-o"></i> </span>',
		'open'   => '<span class="status-badge status-open badge-container" title="Nouveau"> <i class="fa fa-pencil"></i> </span>',
		'solved' => '<span class="status-badge status-solved badge-container" title="Résolu"> <i class="fa fa-check"></i> </span>'
	);

    /**
     * Messagerie
     *
     * @return view
     */
    public function indexAction()
    {
        return $this->render('ZendeskBundle:Messaging:index.html.twig');
    }

    /**
     * Réccupération du mail support zendesk du client
     *
     * @param string $client
     *
     * @return string
     */
    public function getClientMail($client)
    {
		$repository = $this->loadRepository('ZendeskClient');
		$mail       = $repository->getMailSupportByClient($client);

    	if (!empty($mail)) {
    		return $mail[0]->mail_support;
    	} else {
    		return null;
    	}

    }

    /**
     * Liste des tickets par client
     *
     * @param string $client
     * @param integer $status
     *
     * @return view
     */
    public function messagingListAction($client, $status)
    {
    	$email = $this->getClientMail($client);

    	if ($email == "") {
    		# Sans email
    		return $this->render('ZendeskBundle:Messaging:messaging-list.html.twig',array(
				'tickets' => array(),
				'title'   => "Le client n'a pas de mail de support sur zendesk",
				'count'   => 0
			));
    	} else {
			$tickets   = $this->listTicketsByEmailSupport($email,$status);
			return $this->render('ZendeskBundle:Messaging:messaging-list.html.twig',array(
				'tickets' => $tickets,
				'title'   => $this->title,
				'count'   => $this->count
			));
    	}
    }

    /**
     * Recherche de ticket
     *
     * @method GET
     * @param Request $request
     *
     * @return view
     */
    public function searchAction(Request $request)
    {
    	if($request->isXmlHttpRequest()){
			$tickets   = array();
			$post      = $request->request;
			$zendesk   = $this->container->get('api.zendesk');
			$APITicket =  $zendesk->api('Ticket');
			$word      = $post->get('word');
			$client    = $post->get('client');
			$email     = $this->getClientMail($client);
			$all       = $APITicket->search($email,$word);
			$results   = $this->getAllResult($all);

			foreach ($results as $ticket) {

				if ($ticket->status != 'closed') {
					$ticket->requester      = $this->getUserInfo($ticket->requester_id);
					$ticket->status_element = $this->status[$ticket->status];
					$ticket->button         = $this->getButton($ticket->status,$ticket->id);
					array_push($tickets, $ticket);
				}

			}

			$tickets = array_chunk($tickets, 5);
			$title   = 'Resultat de recherche "' . $word . '"';

			if (count($tickets) == 0) {
				$title = 'Aucun résultat pour "' . $word . '"';
			}

			$count = 0;

			if (isset($all->count)) {
				$count = $all->count;
			}

			return $this->render('ZendeskBundle:Messaging:messaging-list.html.twig',array(
				'tickets' => $tickets,
				'title'   => $title,
				'count'   => $count
			));
		}
    }

    /**
     * Button pour résoudre ou réouvrir un ticket
     *
     * @param string $status
     * @param integer $ticket
     *
     * @return string 
     */
    public function getButton($status,$ticket)
    {
    	$button = "";

    	switch ($status) {
    		case 'new':
    			$button = '<button data-id="'. $ticket .'" class="btn-solved btn btn-primary btn-outline pull-right" style="width: 100%;">Résolu ?</button>';
    			break;
    		case 'open':
    			$button = '<button data-id="'. $ticket .'" class="btn-solved btn btn-primary btn-outline pull-right" style="width: 100%;">Résolu ?</button>';
    			break;
    		case 'solved':
    			$button = '<button data-id="'. $ticket .'" class="btn-open btn btn-default btn-outline pull-right" style="width: 100%;">Réouvrir ?</button>';
    			break;
    	}

    	return $button;
    }

    /**
     * Fonction récurssive qui permet de réccupérer 
     * les données avec tous les next_page retourné par l'api
     *
     * @param array $data
     * @param array $results
     *
     * @return array
     */
    public function getAllResult($data,$results = array())
    {

    	if (isset($data->results)) {
			$results   = array_merge($results, $data->results);
			$zendesk   = $this->container->get('api.zendesk');
			$APITicket =  $zendesk->api('Ticket');

	    	if (isset($data->next_page)) {
	    		$next_result = $APITicket->getNextPage($data->next_page);
	    		return $this->getAllResult($next_result,$results);
	    	}
    	}

    	return  $results;
    }

    /**
     * Liste des tickets par email support zendesk
     *
     * @param string $email
     * @param integer $status
     *
     * @return array
     */
    public function listTicketsByEmailSupport($email,$status)
    {

		$zendesk   = $this->container->get('api.zendesk');
		$APITicket =  $zendesk->api('Ticket');
		$APIUser   =  $zendesk->api('User');
		$tickets   = array();
		$results   = array();

		switch ($status) {
			case 0:
				# Tous les tickets
				$all     = $APITicket->findByRecipient($email);
				$results = $this->getAllResult($all);
				$count = 0;
				foreach ($results as $ticket) {
					if ($ticket->status != 'closed') {
						$count += 1;
						$ticket->requester      = $this->getUserInfo($ticket->requester_id);
						$ticket->status_element = $this->status[$ticket->status];
						$ticket->button = $this->getButton($ticket->status,$ticket->id);
						array_push($tickets, $ticket);
					}
				}
				$this->title = "Tous les tickets";
				$this->count = $count;
				break;
			case 1:
				# Nouveaux tickets
				$all     = $APITicket->findByRecipientWhereStatus($email,'new');
				$results = $this->getAllResult($all);
				foreach ($results as $ticket) {
					$ticket->requester      = $this->getUserInfo($ticket->requester_id);
					$ticket->status_element = $this->status[$ticket->status];
					$ticket->button = $this->getButton($ticket->status,$ticket->id);
					array_push($tickets, $ticket);
				}
				$this->title = "Nouveaux tickets";
				$this->count = $all->count;
				break;

			case 2:
				# Tickets ouverts
				$all     = $APITicket->findByRecipientWhereStatus($email,'open');
				$results = $this->getAllResult($all);
				foreach ($results as $ticket) {
					$ticket->requester      = $this->getUserInfo($ticket->requester_id);
					$ticket->status_element = $this->status[$ticket->status];
					$ticket->button = $this->getButton($ticket->status,$ticket->id);
					array_push($tickets, $ticket);
				}
				$this->title = "Tickets ouverts";
				$this->count = $all->count;
				break;

			case 3:
				# Tickets résolus
				$all       = $APITicket->findByRecipientWhereStatus($email,'solved');
				$results = $this->getAllResult($all);
				foreach ($results as $ticket) {
					$ticket->requester      = $this->getUserInfo($ticket->requester_id);
					$ticket->status_element = $this->status[$ticket->status];
					$ticket->button = $this->getButton($ticket->status,$ticket->id);
					array_push($tickets, $ticket);
				}
				$this->title = "Tickets résolus";
				$this->count = $all->count;
				break;
		}

		return array_chunk($tickets, 5);
    }

    /**
     * Conversation du ticket
     *
     * @param integer $id
     */
    public function messagingConversationAction($id)
    {
    	$zendesk   = $this->container->get('api.zendesk');
		$APITicket =  $zendesk->api('Ticket');
		$commentsList = $APITicket->comments($id)->comments;
		$comments = array();
		$ticket = array();

		$ticket = $APITicket->find($id)->ticket;
		$ticket->requester = $this->getUserInfo($ticket->requester_id);
		$ticket->status_element = $this->status[$ticket->status];
		$ticket->button = $this->getButton($ticket->status,$ticket->id);

		foreach ($commentsList as $key => $comment) {

			if ($key != 0) {
				$comment->author = $this->getUserInfo($comment->author_id);
				array_push($comments, $comment);
			}

		}

		return $this->render('ZendeskBundle:Messaging:show-conversation.html.twig',array(
			'comments' => $comments,
			'ticket' => $ticket,
			'id' => $id
		));		
    }

    public function getLastComment($ticketId)
    {
		$zendesk   = $this->container->get('api.zendesk');
    	
    	$APITicket =  $zendesk->api('Ticket');
		$APIUser =  $zendesk->api('User');
		
		$comments = $APITicket->comments($ticketId)->comments;

		if (!empty($comments)) {
			$last_comment = $comments[count($comments) - 1];
			$last_comment->author = $this->getUserInfo($last_comment->author_id);
		} else {
			$last_comment = false;
		}

		return $last_comment;
    }

    public function getUserInfo($id)
	{
		if (isset($this->author[$id])) {
			return $this->author[$id];
		} else {
			$zendesk   = $this->container->get('api.zendesk');
			$APIUser   =  $zendesk->api('User');
			$info = array(
				'name'       => "-",
				'avatar_url' => "/bundles/zendesk/images/avatar.png",
				'default' => true
			);

			if (!$id || $id == null) {
				return $info;
			} else {
				$user = $APIUser->find($id)->user;

				$info['name'] = $user->name;
				$info['email'] = $user->email;
				$info['default'] = false;
				if (!empty($user->photo) || $user->photo != null) {
					$info['avatar_url'] = $user->photo->content_url;
				}
				$this->author[$id] = $info;
				return $info;
			}
		}
	}

	public function changeStatusAction(Request $request)
	{
		if($request->isXmlHttpRequest()){
			$post       = $request->request;
			$id         = $post->get('id');
			$new_status = $post->get('new_status');
			$zendesk    = $this->container->get('api.zendesk');
			$APITicket  =  $zendesk->api('Ticket');
			$fields     = array(
				'status' => $new_status,
			);
			$APITicket->update($id,$fields);

			return $this->response($id);
		}
	}

	public function sendCommentAction(Request $request)
	{
		if($request->isXmlHttpRequest()){
			$post       = $request->request;
			$id = $post->get('id');
			$comment = $post->get('comment');
			$status = $post->get('status');

			$zendesk      = $this->container->get('api.zendesk');
			$APITicket    =  $zendesk->api('Ticket');

			$fields = array(
				'status' => $status,
				'comment' => array(
					'body' => $comment
				)
			);

			$APITicket->update($id,$fields);

			return $this->response($id);
		}
	}

}
