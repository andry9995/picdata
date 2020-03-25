<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 17/10/2018
 * Time: 17:25
 */

namespace AppBundle\Repository;

use AppBundle\Entity\CommentaireDossier;
use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class CommentaireDossierRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @return CommentaireDossier[]
     */
    public function getCommentaires(Dossier $dossier)
    {
        return $this->createQueryBuilder('cd')
            ->where('cd.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->orderBy('cd.code')
            ->getQuery()
            ->getResult();
    }
}