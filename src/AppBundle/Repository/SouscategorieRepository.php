<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 07/04/2017
 * Time: 14:26
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Souscategorie;
use Doctrine\ORM\EntityRepository;

class SouscategorieRepository extends EntityRepository
{
    /**
     * @return Souscategorie[]
     */
    public function getObs()
    {
        $libelleNotIns = [
            'Doublon',
            'Mal affectée',
            'En instance',
            'Relevés bancaires',
            'Invalides',
            'pièces de banques ANPC'
        ];

        $categorie = $this->getEntityManager()->getRepository('AppBundle:Categorie')
            ->find(16);

        return $this->createQueryBuilder('sc')
            ->where('sc.libelleNew NOT IN (:libelleNotIns)')
            ->andWhere('sc.actif = 1')
            ->andWhere('sc.categorie = :categorie')
            ->setParameters([
                'libelleNotIns' => $libelleNotIns,
                'categorie' => $categorie
            ])
            ->orderBy('sc.libelleNew')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param bool $entity
     * @return array
     */
    public function getObsDaily($entity = true)
    {
        //0: par defaut, 1: Remise en banque => 7, 2: LCR => 5
        $ids = [0,7,5];
        if (!$entity) return $ids;

        return $this->createQueryBuilder('sc')
            ->where('sc.id IN (:ids)')
            ->setParameter('ids',$ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $nature
     * @return Souscategorie
     */
    public function getCategorieByNatureReleve($nature = 0)
    {
        $ids = $this->getObsDaily(false);
        if ($nature > count($ids) - 1) return null;

        return $this->createQueryBuilder('sc')
            ->where('sc.id = :id')
            ->setParameter('id', $ids[$nature])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}