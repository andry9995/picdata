<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 22/07/2019
 * Time: 11:24
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveInstruction;
use Doctrine\ORM\EntityRepository;

class ReleveInstructionRepository extends EntityRepository
{
    /**
     * @param Releve $releve
     * @return ReleveInstruction
     */
    public function getByReleve(Releve $releve)
    {
        return $this->createQueryBuilder('ri')
            ->where('ri.releve = :releve')
            ->setParameter('releve',$releve)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}