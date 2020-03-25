<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 09/09/2019
 * Time: 08:40
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurCommentaire;
use Doctrine\ORM\EntityRepository;

class IndicateurCommentaireRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param Indicateur $indicateur
     * @return IndicateurCommentaire
     */
    public function getIndicateurCommentaire(Dossier $dossier, Indicateur $indicateur)
    {
        return $this->createQueryBuilder('ic')
            ->where('ic.dossier = :dossier')
            ->andWhere('ic.indicateur = :indicateur')
            ->setParameters([
                'dossier' => $dossier,
                'indicateur' => $indicateur
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}