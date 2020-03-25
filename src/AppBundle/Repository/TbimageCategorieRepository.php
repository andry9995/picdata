<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 11/04/2017
 * Time: 11:11
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class TbimageCategorieRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @return null
     */
    public function getTbImageCategorieByDossier(Dossier $dossier){
        $categories = $this->createQueryBuilder('tc')
            ->where('tc.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getResult();

        $tbImageCategorie = null;
        if(count($categories) > 0){
            $tbImageCategorie = $categories[0];
        }

        return $tbImageCategorie;
    }

}