<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 02/03/2017
 * Time: 13:50
 */

namespace AppBundle\Repository;


use AppBundle\Entity\EchangeItem;
use AppBundle\Entity\EchangeReponse;
use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;

class EchangeReponseRepository extends EntityRepository
{
    public function getLastEchangeReponse($echange_item_id){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "SELECT * 
                  FROM echange_reponse er 
                  WHERE er.echange_item_id = ".$echange_item_id."
                  order by er.id desc limit 1";
        $prep = $pdo->prepare($query);
        $prep->execute();
        $res = $prep->fetchAll();
        return (count($res) > 0) ? $res[0] : $res;
    }

    /**
     * @param EchangeItem $echangeItem
     * @return EchangeReponse[]
     */
    public function getEchangeReponses(EchangeItem $echangeItem)
    {
        return $this->createQueryBuilder('er')
            ->where('er.echangeItem = :echangeItem')
            ->setParameter('echangeItem',$echangeItem)
            ->getQuery()
            ->getResult();
    }
}