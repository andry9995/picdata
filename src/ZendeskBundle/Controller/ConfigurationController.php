<?php

namespace ZendeskBundle\Controller;

use ZendeskBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ZendeskClient;
use AppBundle\Controller\Boost;

class ConfigurationController extends DefaultController
{
    public function indexAction()
    {
        return $this->render('ZendeskBundle:Configuration:index.html.twig');    
    }

    public function saveConfigMailAction(Request $request)
    {
        if($request->isXmlHttpRequest()){
    		$post = $request->request;
    		$client = $post->get('client');
    		$mail = $post->get('mail');
    		$exist = $post->get('exist');
    		$repository = $this->loadRepository('ZendeskClient');

    		$is_used = $repository->mailExist($mail);

    		if ($is_used) {
    			return $this->response(0);
    		} else{
	    		$clientRepository = $this->loadRepository('Client');
	    		$em = $this->getDoctrine()
	                	   ->getManager();
				$zendeskClient = new ZendeskClient();

	    		if ($exist == 0) {
	        		$client_id = Boost::deboost($client, $this);
	        		$client = $clientRepository->find($client_id);
	    			$zendeskClient->setMailSupport($mail);
	        		$zendeskClient->setClient($client);
	            	$em->persist($zendeskClient);
	    			$em->flush();
	    			return $this->response(1);

	    		} else {
	    			// Modification
	    			var_dump("expression");die();
	    		}
    		}
    	}
    }

    public function getAllRecipientAddressesAction()
    {
    	$zendesk   = $this->container->get('api.zendesk');
		$APIMail =  $zendesk->api('RecipientAddresse');
		$mails = $APIMail->findAll()->recipient_addresses;
		return $this->response($mails);
    }

    public function getMailAction($client_id)
    {
    	$repository = $this->loadRepository('ZendeskClient');
    	$mail = $repository->getMailSupportByClient($client_id);
		
    	if (empty($mail)) {
    		return $this->response(0);
    	} 

    	return $this->response($mail);
    	
    }

}
