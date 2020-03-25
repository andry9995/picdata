<?php
/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 13/02/2017
 * Time: 15:54
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class InstructionTexteRepository extends EntityRepository
{
    public function getInstructionTexteByInstructionType($instructionType)
    {
        $qb = $this->getEntityManager()
            ->getRepository('AppBundle:InstructionTexte')->createQueryBuilder('it');

        $qb->where('it.typeInstruction = :typeInstruction')
            ->setParameter('typeInstruction', $instructionType);

        return $qb->getQuery()->getOneOrNullResult();
    }
}