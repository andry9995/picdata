<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 06/10/2016
 * Time: 10:53
 */

namespace AppBundle\Repository;

use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Pcc;
use AppBundle\Entity\Tiers;
use Doctrine\ORM\EntityRepository;

class PccRepository extends EntityRepository
{
    /**
     * @param array $pcgs
     * @param Dossier $dossier
     * @param array $pcgsOut
     * @param bool $compteAttente
     * @return Pcc[]
     */
    public function getPCCByPCG($pcgs = [],Dossier $dossier,$pcgsOut = [],$compteAttente = false)
    {
        $regex = '';

        foreach ($pcgs as $key => $pcg) $regex .= '^'.$pcg->getCompte().'|';
        $regex .= '^xxxxxxx' . (($compteAttente) ? '|^4710' : '');
        $reg = '^[0-9]+$';

        if($regex == '') return array();

        $results = $this->createQueryBuilder('pcc')
            ->where('pcc.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('REGEXP(pcc.compte, :reg) = true')
            ->setParameter('reg',$reg)
            ->andWhere('REGEXP(pcc.compte, :regex) = true')
            ->setParameter('regex',$regex);

        if(count($pcgsOut) > 0)
        {
            $regexOut = '';
            for($i = 0; $i < count($pcgsOut);$i++)
            {
                $regexOut .= '^'.$pcgsOut[$i]->getCompte();
                if($i != count($pcgsOut) - 1) $regexOut .= '|';
            }

            $results = $results->andWhere('REGEXP(pcc.compte, :regexOut) = false')
                ->setParameter('regexOut',$regexOut);
        }

        return $results
            ->orderBy('pcc.id','ASC')
            ->addOrderBy('pcc.compte')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getById($id)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Dossier $dossier
     * @param bool $withAuxilliaire
     * @return Pcc[]
     */
    public function getPccs(Dossier $dossier = null, $withAuxilliaire = true)
    {
        if (!$dossier) return [];

        $pccs =  $this->createQueryBuilder('p')
            ->where('p.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere("p.compte <> ''");

        if (!$withAuxilliaire)
            $pccs = $pccs
                ->andWhere('p.collectifTiers = -1');

        return $pccs
            ->orderBy('p.compte')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @return array
     */
    public function getPccsTiers(Dossier $dossier,$type)
    {
        $compte = ($type == 0) ? '401' : '411';

        return $this->createQueryBuilder('p')
            ->where('p.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('p.compte LIKE :compte')
            ->setParameter('compte',$compte.'%')
            ->orderBy('p.compte')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param $type
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPccTier(Dossier $dossier,$type)
    {
        return $this->createQueryBuilder('p')
            ->where('p.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('p.collectifTiers = :type')
            ->setParameter('type',$type)
            ->orderBy('p.compte')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Dossier $dossier
     * @param $pcc
     * @param $type
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setTier(Dossier $dossier,$pcc, $type)
    {
        $oldPcc = $this->createQueryBuilder('p')
            ->where('p.collectifTiers = :type')
            ->setParameter('type',$type)
            ->andWhere('p.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()
            ->getOneOrNullResult();

        if($oldPcc != null) $oldPcc->setCollectifTiers(-1);
        if($pcc != null) $pcc->setCollectifTiers($type);
        $tiers = $this->getEntityManager()->getRepository('AppBundle:Tiers')->getTiers($dossier,$type);
        foreach ($tiers as $tier) $tier->setPcc($pcc);
        $em = $this->getEntityManager();
        $em->flush();
        return 1;
    }

    /**
     * @param Dossier $dossier
     * @param $type
     * @return int
     */
    public function getPccTierOfDossier(Dossier $dossier,$type)
    {
        $startCompte = 'xxxx';

        if($type == 0) $startCompte = '401';
        elseif($type == 1) $startCompte = '411';

        $temps = $this->createQueryBuilder('p')
            ->where('p.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('p.compte LIKE :compte')
            ->setParameter('compte',$startCompte.'%')
            ->orderBy('p.compte','ASC')
            ->getQuery()
            ->getResult();

        if(count($temps) > 1) return $temps[0];
        else return 0;
    }

    public function getPccByDossierLike(Dossier $dossier, array $likes){
        $pccs = array();

        foreach ($likes as $like) {
            $temps = $this->createQueryBuilder('pcc')
                ->where('pcc.dossier = :dossier')
                ->setParameter('dossier', $dossier)
                ->andWhere('pcc.compte like :like ')
                ->setParameter('like', $like.'%')
                ->getQuery()
                ->getResult();

            foreach ($temps as $temp){
                if(!in_array($temp, $pccs)){
                    $pccs[] = $temp;
                }
            }
        }
        return $pccs;
    }

    /**
     * @param Dossier $dossier
     * @return Pcc[]
     */
    public function getPccBanque(Dossier $dossier)
    {
        return $this->createQueryBuilder('p')
            ->where('p.dossier = :dossier')
            ->andWhere('p.compte LIKE :compte')
            ->setParameters([
                'dossier' => $dossier,
                'compte' => '512%'
            ])
            ->orderBy('p.compte')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @return Pcc[]
     */
    public function getPccBanqueUsed(Dossier $dossier)
    {
        /** @var BanqueCompte[] $banquesComptes */
        $banquesComptes = $this->getEntityManager()->getRepository('AppBundle:BanqueCompte')
            ->createQueryBuilder('bc')
            ->where('bc.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('bc.status = 1')
            ->andWhere('bc.pcc IS NOT NULL')
            ->getQuery()
            ->getResult();

        /** @var Pcc[] $results */
        $results = [];

        foreach ($banquesComptes as $banquesCompte) $results[$banquesCompte->getPcc()->getId()] = $banquesCompte->getPcc();

        return $results;
    }

    /**
     * @param Dossier $dossier
     * @param bool $debit
     * @return Pcc
     */
    public function getPccAttenteBanque(Dossier $dossier,$debit = true)
    {
        $compte = $debit ? '47260' : '47270';
        $pcc = $this->createQueryBuilder('p')
            ->where('p.dossier = :dossier')
            ->andWhere('p.compte LIKE :compte')
            ->setParameters([
                'dossier' => $dossier,
                'compte' => $compte.'%'
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$pcc)
        {
            $em = $this->getEntityManager();
            $pcc = new Pcc();
            $pcc
                ->setDossier($dossier)
                ->setIntitule('COMPTE D ATTENTE')
                ->setCompte($compte);
            $em->persist($pcc);
            $em->flush();
        }

        return $pcc;
    }

    /**
     * @param Dossier $dossier
     * @param array $pcgs
     * @param bool $withAuxilliaire
     * @return array
     */
    public function getCompteByPcgs(Dossier $dossier,$pcgs = [],$withAuxilliaire = true)
    {
        $res = [];
        $pccs = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgs,$dossier,[],false);
        foreach ($pccs as $pcc)
        {
            $childs = [];
            if ($pcc->getCollectifTiers() != -1 && $withAuxilliaire)
            {
                /** @var Tiers[] $tiers */
                $tiers = $this->getEntityManager()->getRepository('AppBundle:Tiers')
                    ->createQueryBuilder('t')
                    ->where('t.pcc = :pcc')
                    ->setParameter('pcc',$pcc)
                    ->andWhere('t.dossier = :dossier')
                    ->setParameter('dossier',$dossier)
                    ->orderBy('t.intitule')
                    ->getQuery()
                    ->getResult();

                foreach ($tiers as $tier)
                {
                    $childs[] = (object)
                    [
                        't' => 1,
                        'c' => $tier->getCompteStr(),
                        'i' => $tier->getIntitule(),
                        'id' => $tier->getId(),
                        'childs' => []
                    ];
                }
            }

            $res[] = (object)
            [
                't' => 0,
                'c' => $pcc->getCompte(),
                'i' => $pcc->getIntitule(),
                'id' => $pcc->getId(),
                'childs' => $childs
            ];
        }
        return $res;
    }
}