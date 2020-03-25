<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/05/2019
 * Time: 17:31
 */

namespace AppBundle\Repository;


use AppBundle\Entity\CfonbCode;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveComplementaire;
use Doctrine\ORM\EntityRepository;

class ReleveComplementaireRepository extends EntityRepository
{
    /**
     * @param Releve $releve
     * @param CfonbCode[] $cfonbCodeActives
     * @return ReleveComplementaire[]
     */
    public function getActives(Releve $releve, $cfonbCodeActives = [])
    {
        if (count($cfonbCodeActives) <= 0) return [];

        return $this->createQueryBuilder('rc')
            ->where('rc.releve = :releve')
            ->andWhere('rc.cfonbCode IN (:cfonbCodes)')
            ->setParameters([
                'releve' => $releve,
                'cfonbCodes' => $cfonbCodeActives
            ])
            ->getQuery()
            ->getResult();
    }
}