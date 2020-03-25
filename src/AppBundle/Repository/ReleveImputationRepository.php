<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 27/11/2018
 * Time: 08:56
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveImputation;
use Doctrine\ORM\EntityRepository;

class ReleveImputationRepository extends EntityRepository
{
    /**
     * @param Releve $releve
     * @return ReleveImputation[]
     */
    public function getImputation(Releve $releve)
    {
        $results = [];
        if ($releve->getEcritureChange() == 1)
        {
            $results = $this->createQueryBuilder('ri')
                ->where('ri.releve = :releve')
                ->setParameter('releve',$releve)
                ->getQuery()
                ->getResult();
        }
        return $results;
    }

    /**
     * @param Releve $releve
     * @return ReleveImputation[]
     */
    public function getReleveImputation(Releve $releve)
    {
        return $this->createQueryBuilder('ri')
            ->where('ri.releve = :releve')
            ->setParameter('releve',$releve)
            ->getQuery()
            ->getResult();
    }
}