<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/11/2016
 * Time: 10:29
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Indicateur;
use Doctrine\ORM\EntityRepository;

class IndicateurTypeGrapheRepository extends EntityRepository
{
    /**
     * @param Indicateur $indicateur
     * @return array
     */
    public function getGraphes(Indicateur $indicateur)
    {
        $result = array();
        $graphes = $this->createQueryBuilder('tg')
            ->where('tg.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur)
            ->getQuery()
            ->getResult();

        foreach ($graphes as $graphe) $result[] = $graphe->getTypeGraphe();
        return $result;
    }

    /**
     * @param Indicateur $indicateur
     */
    public function deleteOldGraphes(Indicateur $indicateur)
    {
        $olds = $this->createQueryBuilder('tg')
            ->where('tg.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur)
            ->getQuery()
            ->getResult();

        if(count($olds) > 0)
        {
            $em = $this->getEntityManager();
            foreach ($olds as $old) $em->remove($old);
            $em->flush();
        }
    }

    /**
     * @param Indicateur $indicateur
     * @return bool
     */
    public function getIfHasVal(Indicateur $indicateur)
    {
        $hasVal = false;
        $graphes = $this->getGraphes($indicateur);
        foreach ($graphes as $graphe)
        {
            if($graphe->getCode() == 'VAL')
            {
                $hasVal = true;
                break;
            }
        }
        return $hasVal;
    }

    /**
     * @param Indicateur $indicateur
     * @return bool
     */
    public function getIfHasTable(Indicateur $indicateur)
    {
        $hasVal = false;
        $graphes = $this->getGraphes($indicateur);
        foreach ($graphes as $graphe)
        {
            if($graphe->getCode() == 'TAB')
            {
                $hasVal = true;
                break;
            }
        }
        return $hasVal;
    }
}