<?php
/**
 * Created by PhpStorm.
 * User: info
 * Date: 27/02/2019
 * Time: 14:18
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Operateur;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class OperateurUtilisateurRepository extends EntityRepository
{

    public function getUtilisateurOperateur(Operateur $operateur = null, Utilisateur $utilisateur = null)
    {
        if (!$operateur && !$utilisateur) return null;

        $res = $this->createQueryBuilder('uo');
        if ($operateur)
        {
            $res = $res
                ->where('uo.operateur = :operateur')
                ->setParameter('operateur',$operateur);
        }
        elseif ($utilisateur)
        {
            $res = $res
                ->where('uo.utilisateur = :utilisateur')
                ->setParameter('utilisateur',$utilisateur);
        }
        return $res->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }


    public function getAllCouple(Operateur $op = null)
    {
        /** @var Operateur[] $operateurs */
        $operateurs = [];

        if ($op)
            $operateurs = [$op];
        else
            $operateurs = $this->getEntityManager()->getRepository('AppBundle:Operateur')
                ->getAllOperateur();

        $results = [];
        foreach ($operateurs as $operateur)
        {
            $utilisateurOperateur = $this->getUtilisateurOperateur($operateur);
            $results[] = (object)
            [
                'op' => $operateur,
                'uo' => $utilisateurOperateur
            ];
        }

        return $results;
    }
}