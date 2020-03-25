<?php
/**
 * Created by PhpStorm.
 * User: DINOH
 * Date: 05/04/2019
 * Time: 10:47
 */

namespace AppBundle\Repository;
use AppBundle\Entity\CfonbCode;
use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;


class CfonbCodeRepository extends EntityRepository
{
    public function getListCfonb(){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "select cfb_c.id as cfonb_code_id, cfb_c.code as cfonb_code, cfb_a.active as cfonb_activation, cfb_c.libelle as cfonb_libelle
                  from cfonb_code cfb_c
                  left join cfonb_activation cfb_a on (cfb_c.id = cfb_a.cfonb_code_id)";
        $prep = $pdo->prepare($query);
        $prep->execute();

        return $prep->fetchAll();
    }

    /**
     * @return CfonbCode[]
     */
    public function getAlls()
    {
        return $this->createQueryBuilder('cc')
            ->orderBy('cc.code')
            ->getQuery()
            ->getResult();
    }
}