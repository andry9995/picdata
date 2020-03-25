<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 19/06/2017
 * Time: 16:58
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\EtatError;
use Doctrine\ORM\EntityRepository;

class EtatErrorRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @param null $indicateur
     * @param null $etatRegimeFiscal
     * @param $exerciceChoose
     * @param $exercice
     * @param $periode
     * @param bool $oneResult
     * @return EtatError|array|mixed
     */
    public function getErrors(Dossier $dossier,$indicateur = null,$etatRegimeFiscal = null,$exerciceChoose,$exercice,$periode,$oneResult = false)
    {
        $errors = $this->createQueryBuilder('e')
            ->where('e.dossier = :dossier')->setParameter('dossier',$dossier);

        if($indicateur != null)
        {
            $errors = $errors
                ->andWhere('e.indicateur = :indicateur')
                ->setParameter('indicateur',$indicateur);
        }
        if($etatRegimeFiscal != null)
        {
            $errors = $errors
                ->andWhere('e.etatRegimeFiscal = :etatRegimeFiscal')
                ->setParameter('etatRegimeFiscal',$etatRegimeFiscal);
        }

        $errors = $errors
            ->andWhere('e.exerciceChoose = :exerciceChoose')
            ->setParameter('exerciceChoose',$exerciceChoose)
            ->andWhere('e.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('e.periode = :periode')
            ->setParameter('periode',$periode)
            ->getQuery();

        if($oneResult)
        {
            $result = $errors->setMaxResults(1)->getOneOrNullResult();
            if($result == null)
            {
                $em = $this->getEntityManager();
                $result = new EtatError();
                $result->setExerciceChoose($exerciceChoose);
                $result->setExercice($exercice);
                $result->setDossier($dossier);
                $result->setEtatRegimeFiscal($etatRegimeFiscal);
                $result->setIndicateur($indicateur);
                $result->setPeriode($periode);

                $em->persist($result);
                $em->flush();
            }
            return $result;
        }
        else return $errors->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param null $indicateur
     * @param null $etatRegimeFiscal
     * @param $exerciceChoose
     * @param $exercice
     * @param $periode
     * @return int
     */
    public function deleteErrors(Dossier $dossier,$indicateur = null,$etatRegimeFiscal = null,$exerciceChoose,$exercice,$periode)
    {
        $delete =  $this->createQueryBuilder('ee')
            ->delete()
            ->where('ee.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('ee.exerciceChoose = :exerciceChoose')
            ->setParameter('exerciceChoose',$exerciceChoose)
            ->andWhere('ee.exercice = :exercice')
            ->setParameter('exercice',$exercice)
            ->andWhere('ee.periode = :periode')
            ->setParameter('periode',$periode);

        if ($indicateur != null)
            $delete = $delete->andWhere('ee.indicateur = :indicateur')->setParameter('indicateur',$indicateur);
        if ($etatRegimeFiscal != null)
            $delete = $delete->andWhere('ee.etatRegimeFiscal = :etatRegimeFiscal')->setParameter('etatRegimeFiscal',$etatRegimeFiscal);

        $delete->getQuery()->execute();
        return 1;

        /*$errors = $this->getErrors($dossier,$indicateur,$etatRegimeFiscal,$exerciceChoose,$exercice,$periode);
        $em = $this->getEntityManager();
        foreach ($errors as &$error) $em->remove($error);
        $em->flush();
        return 1;*/
    }

    /**
     * @param Client $client
     * @param $site
     * @param $dossier
     * @param $exercices
     * @return array
     */
    public function getAllErros(Client $client,$site,$dossier,$exercices)
    {
        $result =  $this->createQueryBuilder('e')
            ->leftJoin('e.dossier','d')
            ->leftJoin('d.site','s')
            ->leftJoin('s.client','c')
            ->orderBy('e.etatRegimeFiscal')
            ->addOrderBy('e.indicateur')
            ->addOrderBy('c.nom')
            ->addOrderBy('d.nom')
            ->addOrderBy('e.exerciceChoose','DESC')
            ->addOrderBy('e.exercice','DESC')
            ->where('c = :client')
            ->setParameter('client',$client)
            ->andWhere('e.exerciceChoose in (:exercices)')
            ->setParameter('exercices',$exercices);

        if ($dossier != null) $result = $result->andWhere('d = :dossier')->setParameter('dossier',$dossier);
        elseif ($site != null) $result = $result->andWhere('s = :site')->setParameter('site',$site);

        return $result->getQuery()->getResult();
    }
}