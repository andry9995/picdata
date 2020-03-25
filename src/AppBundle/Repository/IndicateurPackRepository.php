<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 02/11/2016
 * Time: 10:27
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\IndicateurGroup;
use AppBundle\Entity\IndicateurPack;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityRepository;

class IndicateurPackRepository extends EntityRepository
{
    /**
     * @param $dossier
     * @param IndicateurPack|null $indicateurPack
     * @return array|\Doctrine\ORM\QueryBuilder
     */
    function getListe($dossier,IndicateurPack $indicateurPack = null)
    {
        $liste = $this->createQueryBuilder('p');

        //dossier
        if($dossier == null) $liste->where('p.dossier IS NULL');
        else $liste->where('p.dossier = :dossier OR p.dossier IS NULL')->setParameter('dossier',$dossier);

        //indicateur specifique
        if($indicateurPack != null) $liste->andWhere('p = :id')->setParameter('id',$indicateurPack->getId());

        $liste = $liste->orderBy('p.rang')->addOrderBy('p.libelle')->getQuery()->getResult();

        foreach ($liste as &$pack)
        {
            $packItems = $this->getEntityManager()->getRepository('AppBundle:Indicateur')->packItems($pack,$dossier);
            $pack->setIndicateurs($packItems);

            if($dossier != null && $pack->getDossier() == null)
            {
                $packSpecDossier = $this->getEntityManager()->getRepository('AppBundle:IndicateurSpecPack')
                    ->createQueryBuilder('psd')
                    ->where('psd.dossier = :dossier')
                    ->setParameter('dossier',$dossier)
                    ->andWhere('psd.indicateurPack = :pack')
                    ->setParameter('pack',$pack)
                    ->getQuery()
                    ->getResult();
                if($packSpecDossier != null) $pack->setEnabled(false);
            }
        }
        return $liste;
    }

    /**
     * @param $id
     * @return mixed
     */
    function getById($id)
    {
        return $this->createQueryBuilder('sp')
            ->where('sp.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $indicateuPacks
     */
    function arrangeRang($indicateuPacks)
    {
        $em = $this->getEntityManager();
        foreach ($indicateuPacks as $key => $indicateuPack) $indicateuPack->setRang($key);
        $em->flush();
    }

    /**
     * @param IndicateurGroup $indicateurGroup
     * @param $client
     * @param $dossier
     * @param null $indicateurPack
     * @param bool $withNotValidate
     * @return \Doctrine\ORM\QueryBuilder
     */
    function getPacksInGroups(IndicateurGroup $indicateurGroup,$client,$dossier,$indicateurPack = null,$withNotValidate = true)
    {
        $liste = $this->createQueryBuilder('p');

        if($client == null && $dossier == null)
        {
            $liste = $liste->where('p.dossier IS NULL')->andWhere('p.client IS NULL');
        }
        else
        {
            if($client != null)
            {
                $liste = $liste->where('(p.client = :client OR (p.dossier IS NULL AND p.client IS NULL))')->setParameter('client',$client);
            }
            else
            {
                $liste = $liste
                    ->where('(p.dossier = :dossier OR p.client = :client OR (p.dossier IS NULL AND p.client IS NULL))')
                    ->setParameter('dossier',$dossier)
                    ->setParameter('client',$dossier->getSite()->getClient());
            }
        }

        if($indicateurPack != null)
            $liste = $liste->andWhere('p.id = :pac')->setParameter('pac',$indicateurPack->getId());

        if(!$withNotValidate)
            $liste = $liste->andWhere('p.valider = 1');

        $liste = $liste->andWhere('p.indicateurGroup = :indicateurGroup')
            ->setParameter('indicateurGroup',$indicateurGroup)
            ->orderBy('p.rang')->addOrderBy('p.libelle')->getQuery()->getResult();

        foreach ($liste as &$pack)
        {
            $packItems = $this->getEntityManager()->getRepository('AppBundle:Indicateur')->getIndicateurs($pack,$client,$dossier,$withNotValidate);
            $pack->setIndicateurs($packItems);

            if($client != null || $dossier != null)
            {
                $pack = $this->getEntityManager()->getRepository('AppBundle:IndicateurSpecPack')->setEnabled($pack,$client,$dossier);
            }
        }
        return $liste;
    }

    /**
     * @param IndicateurPack $indicateurPack
     * @param $client
     * @param $dossier
     * @return bool
     */
    function dupliquer(IndicateurPack $indicateurPack,$client,$dossier)
    {
        $em = $this->getEntityManager();
        $indicateurPackDupliquer = new IndicateurPack();
        $indicateurPackDupliquer->setDossier($dossier);
        $indicateurPackDupliquer->setIndicateurGroup($indicateurPack->getIndicateurGroup());
        $indicateurPackDupliquer->setClient($client);
        $indicateurPackDupliquer->setLibelle($indicateurPack->getLibelle());
        $indicateurPackDupliquer->setKeyDupliquer(Boost::getUuid(25));

        $em->persist($indicateurPackDupliquer);
        try
        {
            $em->flush();
            $indicateurs = $this->getEntityManager()->getRepository('AppBundle:Indicateur')->getIndicateursADupliquer($indicateurPack,$client,$dossier);
            foreach ($indicateurs as $indicateur)
            {
                $this->getEntityManager()->getRepository('AppBundle:Indicateur')->dupliquer($indicateur,$client,$dossier,$indicateurPackDupliquer);
            }

            return 1;
        }
        catch (UniqueConstraintViolationException $violationException)
        {
            return 0;
        }
    }
}