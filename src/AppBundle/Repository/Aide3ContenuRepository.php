<?php
/**
 * Created by PhpStorm.
 * User: Maharo
 * Date: 14/02/2018
 * Time: 16:18
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class Aide3ContenuRepository extends EntityRepository
{
    public function getContenuAideByUtilisateur($aide3 ,$utilisateur){
        $typeContenu = 0;
        /** @var Utilisateur $utilisateur */
        if($utilisateur->getAccesUtilisateur()->getType() == 6){
            $typeContenu = 1;
        }

        return  $this->getEntityManager()
            ->getRepository('AppBundle:Aide3Contenu')
            ->createQueryBuilder('a')
            ->where('a.typeContenu = :typeContenu')
            ->andWhere('a.aide3 = :aide3')
            ->setParameter('typeContenu', $typeContenu)
            ->setParameter('aide3', $aide3)
            ->getQuery()
            ->getOneOrNullResult();
    }
}