<?php

/**
 * ZendeskController 
 *
 * @package Picdata
 *
 * @author Scriptura
 * @copyright Scriptura (c) 2019
 */

namespace ZendeskBundle\Controller;

use ZendeskBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;

class ZendeskController extends DefaultController
{

	public function indexAction()
	{
	    return $this->render('ZendeskBundle:Default:index.html.twig');
	}

	/**
	 * Informations utilisateur zendesk
	 *
	 * @param integer $i
	 *
	 * @return array
	 */
	public function getUserInfo($id)
	{
		$zendesk   = $this->container->get('api.zendesk');
		$APIUser   =  $zendesk->api('User');
		$info = array(
			'name'       => "-",
			'avatar'     => "<img class='img-rounded user-avatar' src='https://i2.wp.com/assets.zendesk.com/images/2016/default-avatar-80.png?ssl=1' /> ",
			'avatar_url' => "https://i2.wp.com/assets.zendesk.com/images/2016/default-avatar-80.png?ssl=1"
		);

		if (!$id || $id == null) {
			return $info;
		} else {
			$user = $APIUser->find($id)->user;
			$info['name'] = $user->name;
			if (!empty($user->photo) || $user->photo != null) {
				$info['avatar']     = "<img class='img-rounded user-avatar' src='{$user->photo->content_url}' /> " ;
				$info['avatar_url'] = $user->photo->content_url;
			}
			return $info;
		}
	}

	/**
	 * Liste des tickets
	 *
	 * @method GET
	 * @param string $status
	 * @param string $priority
	 *
	 * @return JsonResponse
	 */
	public function ticketsListAction(
		$status,
		$priority
	) {
		$zendesk   = $this->container->get('api.zendesk');
		$APITicket =  $zendesk->api('Ticket');
		$tickets   = $APITicket->findAll()->tickets;
		$response  = [];

		if ($status == "all") {
			if ($priority == "all") {
				foreach ($tickets as $key => $ticket) {
					if ($ticket->status != "closed") {
						$requester   = $this->getUserInfo($ticket->requester_id);
						$assignee    = $this->getUserInfo($ticket->assignee_id);
						$statusBadge = $this->getStatus($ticket->status);
						$response[]  = [
							'status'       => $statusBadge,
							'id'           => "$ticket->id",
							'subject'      => "<div class='subject-card priority-{$this->getPriority($ticket->priority)}'>{$ticket->subject}</div>",
							'requester'    => $requester['avatar'] . $requester['name'],
							'assignee'     => $assignee['name'],
							'status-label' => $ticket->status,
							'description'  => "<pre>{$ticket->description}</pre>",
							'show'         => "<button class='btn btn-default btn-circle show-ticket' type='button'><i class='fa fa-comment-o'></i></button>"
						];
					}
				}
			} else {
				if ($priority == "none") {
					foreach ($tickets as $key => $ticket) {
						if ($ticket->status != "closed" && $ticket->priority == null) {
							$requester   = $this->getUserInfo($ticket->requester_id);
							$assignee    = $this->getUserInfo($ticket->assignee_id);
							$statusBadge = $this->getStatus($ticket->status);
							$response[]  = [
								'status'       => $statusBadge,
								'id'           => "$ticket->id",
								'subject'      => "<div class='subject-card priority-{$this->getPriority($ticket->priority)}'>{$ticket->subject}</div>",
								'requester'    => $requester['avatar'] . $requester['name'],
								'assignee'     => $assignee['name'],
								'status-label' => $ticket->status,
								'description'  => "<pre>{$ticket->description}</pre>",
								'show'         => "<button class='btn btn-default btn-circle show-ticket' type='button'><i class='fa fa-comment-o'></i></button>"
							];
						}
					}
				} else {
					foreach ($tickets as $key => $ticket) {
						if ($ticket->status != "closed" && $ticket->priority == $priority) {
							$requester   = $this->getUserInfo($ticket->requester_id);
							$assignee    = $this->getUserInfo($ticket->assignee_id);
							$statusBadge = $this->getStatus($ticket->status);
							$response[]  = [
								'status'       => $statusBadge,
								'id'           => "$ticket->id",
								'subject'      => "<div class='subject-card priority-{$this->getPriority($ticket->priority)}'>{$ticket->subject}</div>",
								'requester'    => $requester['avatar'] . $requester['name'],
								'assignee'     => $assignee['name'],
								'status-label' => $ticket->status,
								'description'  => "<pre>{$ticket->description}</pre>",
								'show'         => "<button class='btn btn-default btn-circle show-ticket' type='button'><i class='fa fa-comment-o'></i></button>"
							];
						}
					}
				}
			}

		} else {

			if ($priority == "all") {
				foreach ($tickets as $key => $ticket) {
					if (($ticket->status != "closed") && ($ticket->status == $status)) {
						$requester   = $this->getUserInfo($ticket->requester_id);
						$assignee    = $this->getUserInfo($ticket->assignee_id);
						$statusBadge = $this->getStatus($ticket->status);
						$response[]  = [
							'status'       => $statusBadge,
							'id'           => "$ticket->id",
							'subject'      => "<div class='subject-card priority-{$this->getPriority($ticket->priority)}'>{$ticket->subject}</div>",
							'requester'    => $requester['avatar'] . $requester['name'],
							'assignee'     => $assignee['name'],
							'status-label' => $ticket->status,
							'description'  => $ticket->description,
							'show'         => "<button class='btn btn-default btn-circle show-ticket' type='button'><i class='fa fa-comment-o'></i></button>"
						];
					}
				}
			} else {
				if ($priority == "none") {
					foreach ($tickets as $key => $ticket) {
						if ($ticket->status != "closed" && $ticket->status == $status && $ticket->priority == null) {
							$requester   = $this->getUserInfo($ticket->requester_id);
							$assignee    = $this->getUserInfo($ticket->assignee_id);
							$statusBadge = $this->getStatus($ticket->status);
							$response[]  = [
								'status'       => $statusBadge,
								'id'           => "$ticket->id",
								'subject'      => "<div class='subject-card priority-{$this->getPriority($ticket->priority)}'>{$ticket->subject}</div>",
								'requester'    => $requester['avatar'] . $requester['name'],
								'assignee'     => $assignee['name'],
								'status-label' => $ticket->status,
								'description'  => "<pre>{$ticket->description}</pre>",
								'show'         => "<button class='btn btn-default btn-circle show-ticket' type='button'><i class='fa fa-comment-o'></i></button>"
							];
						}
					}
				} else {
					foreach ($tickets as $key => $ticket) {
						if ($ticket->status != "closed" && $ticket->status == $status && $ticket->priority == $priority) {
							$requester   = $this->getUserInfo($ticket->requester_id);
							$assignee    = $this->getUserInfo($ticket->assignee_id);
							$statusBadge = $this->getStatus($ticket->status);
							$response[]  = [
								'status'       => $statusBadge,
								'id'           => "$ticket->id",
								'subject'      => "<div class='subject-card priority-{$this->getPriority($ticket->priority)}'>{$ticket->subject}</div>",
								'requester'    => $requester['avatar'] . $requester['name'],
								'assignee'     => $assignee['name'],
								'status-label' => $ticket->status,
								'description'  => "<pre>{$ticket->description}</pre>",
								'show'         => "<button class='btn btn-default btn-circle show-ticket' type='button'><i class='fa fa-comment-o'></i></button>"
							];
						}
					}
				}
			}

		}

		return $this->response($response);
	}

	/**
	 * Priorité d'un ticket
	 *
	 * @param string $priority
	 *
	 * @return string
	 */
	public function getPriority($priority)
	{
		if (!$priority || $priority == null) {
			return "none";
		} else {
			return $priority;
		}
	}

	/**
	 * Balise pour afficher statut d'un ticket
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public function getStatus($status)
	{
		if ($status == 'pending') {
			$badge = "A";
		} elseif ($status == 'solved') {
			$badge = "R";
		} else{
			$badge = strtoupper(substr($status,0,1));
		}
		$status = "<span class='status-badge status-{$status}'>{$badge}</span>";
		return $status;
	}

	/**
	 * Afficher un ticket dans un modal
	 *
	 * @method GET
	 * @param integer $id
	 *
	 * @return html
	 */
	public function ticketShowAction($id)
	{
		$zendesk      = $this->container->get('api.zendesk');
		$APITicket    =  $zendesk->api('Ticket');
		$ticket       = $APITicket->find($id)->ticket;
		$commentsList = $APITicket->comments($id)->comments;
		$comments     = array();
		$tags         = implode(",", $ticket->tags);
		$requester    = $this->getUserInfo($ticket->requester_id);
		
		foreach ($commentsList as $key => $comment) {
			$item = array(
				'html_body'  => $comment->html_body,
				'author'     => $this->getUserInfo($comment->author_id),
				'created_at' => $comment->created_at
			);
			array_push($comments, $item);
		}

		/** Liste des priority */
		$priorities  = array(
			'none'   => 'Aucune',
			'urgent' => 'Urgente',
			'high'   => 'Elevée',
			'normal' => 'Normale',
			'low'    => 'Basse'
		);

		$statuts = array(
			'new' => 'Nouveau',
			'open' => 'Ouvert',
			'pending' => 'En Attente',
			'solved' => 'Résolu'
		);

		return $this->render('ZendeskBundle:Default:modal-ticket-show.html.twig',array(
			'ticket'    => $ticket,
			'requester' => $requester,
			'priorities'  => $priorities,
			'comments'  => $comments,
			'tags'      => $tags,
			'statuts' => $statuts
		));
	}

	public function ticketSendUpdateAction(Request $request)
	{
        if($request->isXmlHttpRequest()){
	        $post = $request->request;

	        $status = $post->get('status');
	        $id = $post->get('id');

	        $comment = $post->get('comment');

	        $zendesk      = $this->container->get('api.zendesk');
			$APITicket    =  $zendesk->api('Ticket');

			$fields = array(
				'status' => $status,
				'comment' => array(
					'body' => $comment
				)
			);

			$APITicket->update($id,$fields);

			return $this->response(1);
		}
	}
}
