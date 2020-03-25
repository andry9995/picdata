<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 16/10/2019
 * Time: 09:30
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\Etat;
use AppBundle\Entity\EtatCommentaire;
use Doctrine\ORM\EntityRepository;

class EtatCommentaireRepository extends EntityRepository
{
    /**
     * @param Etat $etat
     * @param Dossier $dossier
     * @return EtatCommentaire
     */
    public function getByEtatDossier(Etat $etat, Dossier $dossier)
    {
        return $this->createQueryBuilder('ec')
            ->where('ec.etat = :etat')
            ->andWhere('ec.dossier = :dossier')
            ->setParameters([
                'etat' => $etat,
                'dossier' => $dossier
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}