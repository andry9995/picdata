<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 17/03/2017
 * Time: 17:36
 */

namespace AppBundle\Repository;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Indicateur;
use AppBundle\Entity\IndicateurCache;
use Doctrine\ORM\EntityRepository;


class IndicateurCacheRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param Indicateur $indicateur
     * @param $exercice
     * @return mixed
     */
    public function getCache(Dossier $dossier,Indicateur $indicateur,$exercice)
    {
        return $this->getEntityManager()->getRepository('AppBundle:IndicateurCache')
            ->createQueryBuilder('ic')
            ->where('ic.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('ic.indicateur = :indicateur')
            ->setParameter('indicateur',$indicateur)
            ->andWhere('ic.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Dossier $dossier
     * @param Indicateur $indicateur
     * @param $exercice
     * @param string $value
     */
    public function setCache(Dossier $dossier,Indicateur $indicateur,$exercice,$value = '')
    {
        $indicateurCache = new IndicateurCache();
        $indicateurCache->setIndicateur($indicateur);
        $indicateurCache->setDossier($dossier);
        $indicateurCache->setExercice($exercice);
        $indicateurCache->setValue($value);

        $em = $this->getEntityManager();
        $em->persist($indicateurCache);
        $em->flush();
    }
}