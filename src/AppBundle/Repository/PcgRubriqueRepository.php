<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Rubrique;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\PcgRubrique;
use AppBundle\Entity\Pcg;
use AppBundle\Controller\RubriqueParam;
use RubriqueBundle\Controller\pcgRubriqueObject;
use stdClass;

class PcgRubriqueRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getPcgRubrique()
    {
        $pcgs = $this->getEntityManager()->getRepository('AppBundle:Pcg')
            ->createQueryBuilder('p')
            ->orderBy('p.compte','ASC')
            ->getQuery()
            ->getResult();
        $results = array();
        $rubriques = $this->findAll();

        foreach ($pcgs as $pcg)
        {
            $rubriqueObject = new RubriqueParam();
            //pcg
            $rubriqueObject->id = $pcg->getId();
            $rubriqueObject->pcg_compte = $pcg->getCompte();
            $rubriqueObject->pcg_intitule = $pcg->getIntitule();

            //rubriques
            $rubrique = null;
            $superRubrique = null;
            $hyperRubrique = null;
            $typeCout = null;
            $regle = null;
            foreach($rubriques as $r)
            {
                if($r->getPcg() == $pcg)
                {
                    if($r->getRubrique()->getType() == 0) $rubrique = $r->getRubrique();
                    elseif($r->getRubrique()->getType() == 1) $superRubrique = $r->getRubrique();
                    elseif($r->getRubrique()->getType() == 2) $hyperRubrique = $r->getRubrique();
                    elseif($r->getRubrique()->getType() == 3) $typeCout = $r->getRubrique();
                    else $regle = $r->getRubrique();
                }
            }
            if($rubrique != null) $rubriqueObject->rubrique = $rubrique->getId();
            if($superRubrique != null) $rubriqueObject->superRubrique = $superRubrique->getId();
            if($hyperRubrique != null) $rubriqueObject->hyperRubrique = $hyperRubrique->getId();
            /**$rubriqueObject->typeCout = $typeCout;
            $rubriqueObject->regle = $regle;*/

            $results[] = $rubriqueObject;
        }

        return $results;
    }

    /**
     * @return array
     */
    public function getPcgsRubriques()
    {
        $pcgs = $this->getEntityManager()->getRepository('AppBundle:Pcg')
            ->createQueryBuilder('p')
            ->orderBy('p.compte','ASC')
            ->getQuery()
            ->getResult();
        $results = array();
        $rubriques = $this->findAll();

        foreach ($pcgs as $pcg)
        {
            $r_a = array();
            $s_a = array();
            $h_a = array();
            foreach($rubriques as $r)
            {
                if($r->getPcg() == $pcg)
                {
                    if($r->getRubrique()->getType() == 0) $r_a[] = $r->getRubrique();
                    elseif($r->getRubrique()->getType() == 1) $s_a[] = $r->getRubrique();
                    elseif($r->getRubrique()->getType() == 2) $h_a[] = $r->getRubrique();
                    //elseif($r->getRubrique()->getType() == 3) $typeCout = $r->getRubrique();
                    //else $regle = $r->getRubrique();
                }
            }
            $pcgRubrique = new pcgRubriqueObject($pcg,$r_a,$s_a,$h_a);
            $results[] = $pcgRubrique;
        }
        return $results;
    }

    /**
     * @param Pcg $pcg
     * @param $libelle_rubrique
     * @param $type
     * @return int
     */
    public function setRubrique(Pcg $pcg,$libelle_rubrique,$type)
    {
        $em = $this->getEntityManager();

        $pcg_rubrique = $em->getRepository('AppBundle:PcgRubrique')
            ->createQueryBuilder('pr')
            ->leftJoin('pr.rubrique','r')
            ->where('pr.pcg = :pcg')
            ->setParameter('pcg',$pcg)
            ->andWhere('r.type = :type')
            ->setParameter('type',$type)
            ->getQuery()
            ->getOneOrNullResult();

        $action = 0;

        if(trim($libelle_rubrique) == '' && $pcg_rubrique != null)
        {
            $em->remove($pcg_rubrique);
        }
        else
        {
            $rubrique = $em->getRepository('AppBundle:Rubrique')
                ->createQueryBuilder('r')
                ->where('r.libelle = :libelle')
                ->setParameter('libelle',$libelle_rubrique)
                ->andWhere('r.type = :type')
                ->setParameter('type',$type)
                ->getQuery()
                ->getOneOrNullResult();

            //insert rubrique
            if($rubrique == null)
            {
                $rubrique = new Rubrique();
                $rubrique->setLibelle($libelle_rubrique);
                $rubrique->setType($type);

                $em->persist($rubrique);
                $em->flush();
            }

            if($pcg_rubrique == null)
            {
                $pcg_rubrique = new PcgRubrique();
                $pcg_rubrique->setPcg($pcg);
                $pcg_rubrique->setRubrique($rubrique);
                $em->persist($pcg_rubrique);
                $action = 1;
            }
            else
            {
                $pcg_rubrique->setRubrique($rubrique);
                $action = 2;
            }
        }
        $em->flush();

        return $action;
    }

    /**
     * @param Pcg $pcg
     * @param $rubrique
     * @param $type
     * @return int
     */
    public function setRubriqueToLikePcg(Pcg $pcg, $rubrique, $type)
    {
        $em = $this->getEntityManager();

        //suppression
        if($rubrique == null)
        {
            $pcgRubriques = $this->getEntityManager()->getRepository('AppBundle:PcgRubrique')->createQueryBuilder('pr')
                ->leftJoin('pr.rubrique','r')
                ->where('r.type = :type')
                ->setParameter('type',$type)
                ->andWhere('pr.pcg = :pcg')
                ->setParameter('pcg',$pcg)
                ->getQuery()
                ->getResult();
            foreach ($pcgRubriques as $pcgRubrique) $em->remove($pcgRubrique);
            $em->flush();
            return 1;
        }

        //add, edit
        $type = $rubrique->getType();
        $pcgs = $this->getEntityManager()->getRepository('AppBundle:Pcg')->createQueryBuilder('p')
            ->where('p.compte LIKE :compte')
            ->setParameter('compte',$pcg->getCompte().'%')
            ->getQuery()
            ->getResult();

        foreach ($pcgs as $pcg_item)
        {
            $pcgRubrique = $this->getEntityManager()->getRepository('AppBundle:PcgRubrique')->createQueryBuilder('pr')
                ->leftJoin('pr.rubrique','r')
                ->where('pr.pcg = :pcg')
                ->setParameter('pcg',$pcg_item)
                ->andWhere('r.type = :type')
                ->setParameter('type',$type)
                ->getQuery()
                ->getOneOrNullResult();

            if($pcgRubrique == null)
            {
                $pcgRubrique = new PcgRubrique();
                $pcgRubrique->setPcg($pcg_item);
                $pcgRubrique->setRubrique($rubrique);
                $em->persist($pcgRubrique);
            }
            else if($pcg_item == $pcg) $pcgRubrique->setRubrique($rubrique);
        }

        $em->flush();
        return 1;
    }

    /**
     * @param Pcg $pcg
     * @param $rubrique
     * @param $superRubrique
     * @param $hyperRubrique
     * @return int
     */
    public function setRubrique2(Pcg $pcg,$rubrique,$superRubrique,$hyperRubrique)
    {
        $em = $this->getEntityManager();
        $rubriques = $this->createQueryBuilder('pr')
            ->where('pr.pcg = :pcg')
            ->setParameter('pcg',$pcg)
            ->getQuery()
            ->getResult();

        $rubriqueSet = null;
        $superRubriqueSet = null;
        $hyperRubriqueSet = null;

        foreach ($rubriques as $r_item)
        {
            $r = $r_item->getRubrique();
            if($r->getType() == 0) $rubriqueSet = $r_item;
            elseif($r->getType() == 1) $superRubriqueSet = $r_item;
            elseif($r->getType() == 2) $hyperRubriqueSet = $r_item;
        }

        $count_changed = 0;

        //delete rubrique
        if($rubrique == null && $rubriqueSet != null) $em->remove($rubriqueSet);
        //edit rubrique
        elseif($rubrique != null && $rubriqueSet != null)
        {
            $em->remove($rubriqueSet);
            $em->flush();
            $count_changed = $this->setToLikePCG($pcg,$rubrique);
            //$rubriqueSet->setRubrique($rubrique);
        }
        //add rubrique
        elseif($rubrique != null && $rubriqueSet == null) $count_changed = $this->setToLikePCG($pcg,$rubrique);

        //delete super rubrique
        if($superRubrique == null && $superRubriqueSet != null) $em->remove($superRubriqueSet);
        //edit super rubrique
        elseif($superRubrique != null && $superRubriqueSet != null)
        {
            $em->remove($superRubriqueSet);
            $em->flush();
            $count_changed = $this->setToLikePCG($pcg,$superRubrique);
            //$superRubriqueSet->setRubrique($superRubrique);
        }
        //add super rubrique
        elseif($superRubrique != null && $superRubriqueSet == null) $count_changed = $this->setToLikePCG($pcg,$superRubrique);

        //delete hyper rubrique
        if($hyperRubrique == null && $hyperRubriqueSet != null) $em->remove($hyperRubriqueSet);
        //edit hyper rubrique
        elseif($hyperRubrique != null && $hyperRubriqueSet != null)
        {
            $em->remove($hyperRubriqueSet);
            $em->flush();
            $count_changed = $this->setToLikePCG($pcg,$hyperRubrique);
            //$hyperRubriqueSet->setRubrique($hyperRubrique);
        }
        //add hyper rubrique
        elseif($hyperRubrique != null && $hyperRubriqueSet == null) $count_changed = $this->setToLikePCG($pcg,$hyperRubrique);

        $em->flush();
        return $count_changed;
    }

    /**
     * @param Pcg $pcg
     * @param Rubrique $rubrique
     * @return bool
     */
    public function setToLikePCG(Pcg $pcg,Rubrique $rubrique)
    {
        $em = $this->getEntityManager();
        $pcgNotSets = $this->getPcgLikeNotSet($pcg,$rubrique);

        foreach ($pcgNotSets as $pcgNotSet)
        {
            $pcgRubrique = new PcgRubrique();
            $pcgRubrique->setPcg($pcgNotSet);
            $pcgRubrique->setRubrique($rubrique);
            $em->persist($pcgRubrique);
        }
        $em->flush();
        return count($pcgNotSets);
    }

    /**
     * @param Pcg $pcg
     * @param $type
     * @return array
     */
    public function getPcgLikeNotSet(Pcg $pcg,$type)
    {
        $pcgRubriqueSets = $this->getEntityManager()->getRepository('AppBundle:PcgRubrique')->createQueryBuilder('pr')
            ->leftJoin('pr.pcg','p')
            ->leftJoin('pr.rubrique','r')
            ->where('p.compte LIKE :compte')
            ->setParameter('compte',$pcg->getCompte().'%')
            ->andWhere('r.type = :type')
            ->setParameter('type',$type)
            ->getQuery()
            ->getResult();

        $pcgsSets = array();
        foreach ($pcgRubriqueSets as $pcgRubriqueSet) $pcgsSets[] = $pcgRubriqueSet->getPcg();
        $result = $this->getEntityManager()->getRepository('AppBundle:Pcg')
            ->createQueryBuilder('p')
            ->where('p.compte LIKE :compte')
            ->setParameter('compte',$pcg->getCompte().'%');
        if(count($pcgsSets) != 0) $result->andWhere('p NOT IN (:pcgsSets)')->setParameter('pcgsSets',$pcgsSets);

        return $result->getQuery()->getResult();
    }

    /**
     * @param Pcg $pcg
     * @param $rubriques
     * @param $type
     * @return int
     */
    public function setRubPcg(Pcg $pcg,$rubriques,$type)
    {
        $em = $this->getEntityManager();
        //remove olds
        $olds = $this->createQueryBuilder('pr')
            ->leftJoin('pr.rubrique','r')
            ->where('pr.pcg = :pcg')
            ->setParameter('pcg',$pcg)
            ->andWhere('r.type = :type')
            ->setParameter('type',$type)
            ->getQuery()
            ->getResult();

        foreach ($olds as $old) $em->remove($old);
        $em->flush();

        $pcgNotSets = $this->getPcgLikeNotSet($pcg,$type);

        $news = $this->getEntityManager()->getRepository('AppBundle:Rubrique')
            ->createQueryBuilder('r')
            ->where('r.id IN (:ids)')
            ->setParameter('ids',$rubriques)
            ->getQuery()
            ->getResult();


        foreach ($pcgNotSets as $pcgNotSet)
            foreach ($news as $new)
            {
                $pcgRubrique = new PcgRubrique();
                $pcgRubrique->setPcg($pcgNotSet);
                $pcgRubrique->setRubrique($new);
                $em->persist($pcgRubrique);
            }

        $em->flush();

        return 1;
    }

    /**
     * @param Rubrique $rubrique
     * @return array
     */
    public function getPcgs(Rubrique $rubrique)
    {
        $results = array();
        $pcgRubriques = $this->createQueryBuilder('pr')
            ->where('pr.rubrique = :rubrique')
            ->setParameter('rubrique',$rubrique)
            ->getQuery()
            ->getResult();
        foreach ($pcgRubriques as $pcgRubrique) $results[] = $pcgRubrique->getPcg();
        return $results;
    }

    /**
     * @param Rubrique $rubrique
     * @param bool $withNot
     * @return stdClass
     */
    public function getPcgsRubriquesSets(Rubrique $rubrique,$withNot = true)
    {
        $results = array();
        $pcgRubriques = $this->createQueryBuilder('pr')
            ->leftJoin('pr.pcg','pcg')
            ->where('pr.rubrique = :rubrique')
            ->setParameter('rubrique',$rubrique)
            ->orderBy('pcg.compte','ASC')
            ->getQuery()
            ->getResult();
        foreach ($pcgRubriques as $pcgRubrique)
        {
            $pcg = $pcgRubrique->getPcg();
            $compte = $pcg->getCompte();
            $results[$compte] = $pcg;
        }

        $pcgsNotIns = array();
        if($withNot)
        {
            foreach ($results as $numCompte => $result)
            {
                $pcgsNotInTemps = $this->getPcgsNotIn($rubrique,$result,$results);
                foreach ($pcgsNotInTemps as $pcgsNotInTemp)
                {
                    if(!in_array($pcgsNotInTemp,$pcgsNotIns)) $pcgsNotIns[] = $pcgsNotInTemp;
                }
            }
        }

        $res = new stdClass();
        $res->in = $results;
        $res->out = $pcgsNotIns;
        return $res;
    }

    /**
     * @param Rubrique $rubrique
     * @param Pcg $pcg
     * @param array $pcgsIn
     * @return array
     */
    public function getPcgsNotIn(Rubrique $rubrique,Pcg $pcg,$pcgsIn = array())
    {
        $temps = $this->createQueryBuilder('pr')
            ->leftJoin('pr.pcg','pcg')
            ->where('pr.rubrique <> :rubrique')
            ->setParameter('rubrique',$rubrique)
            ->andWhere('pcg.compte LIKE :compteLike')
            ->setParameter('compteLike',$pcg->getCompte().'%');

        $results = array();
        if(count($pcgsIn) <> 0)
            $temps = $temps->andWhere('pr.pcg NOT IN (:pcgsIn)')->setParameter('pcgsIn',$pcgsIn);
        $temps = $temps->getQuery()->getResult();
        foreach ($temps as $temp)
        {
            $pcgTemp = $temp->getPcg();
            if(!in_array($pcgTemp,$results)) $results[] = $pcgTemp;
        }

        $pcgsOtherLikeTemps = $this->getEntityManager()->getRepository('AppBundle:Pcg')->getOtherLikeCompte($pcg);
        $pcgsOtherLikes = array();
        foreach ($pcgsOtherLikeTemps as $pcgsOtherLikeTemp) $pcgsOtherLikes[] = $pcgsOtherLikeTemp;

        return (count($pcgsOtherLikes) == count($results)) ? array() : $results;
    }
}