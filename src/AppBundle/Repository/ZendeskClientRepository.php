<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Controller\Boost;
use AppBundle\Functions\CustomPdoConnection;

class ZendeskClientRepository extends EntityRepository
{

	private $pdo;

	public function getMailSupportByClient($clientId)
	{
		$con = new CustomPdoConnection();
        $this->pdo = $con->connect();
        $client_id = Boost::deboost($clientId,$this);

     	$query = "  select *
                    from zendesk_client zc 
                    where zc.client_id = " . $client_id ;
        $prep = $this->pdo->prepare($query);
        $prep->execute();
        $result = $prep->fetchAll();
        return $result;
	}

	public function mailExist($mail)
	{
		$con = new CustomPdoConnection();
        $this->pdo = $con->connect();
     	$query = "	select * 
					from zendesk_client zc
					where zc.mail_support = '${mail}'";

        $prep = $this->pdo->prepare($query);
        $prep->execute();
        $result = $prep->fetchAll();

        if (empty($result)) {
        	return false;
        }
        return true;
	}

}