<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 06/06/2016
 * Time: 15:31
 */
namespace AppBundle\Repository;

use AppBundle\Entity\Dossier;
use AppBundle\Entity\IndicateurOperande;
use AppBundle\Entity\Rubrique;
use Doctrine\ORM\EntityRepository;
use stdClass;

class RubriqueRepository extends EntityRepository
{
    /**
     * @param $type
     * @return array
     */
    public function getRubriquesLibelles($type)
    {
        $result = $this->createQueryBuilder('r')
            ->select('r.libelle,r.type');

        if($type < 10) $result = $result->where('r.type = :type')->setParameter('type',$type);

        return $result->orderBy('r.type')->addOrderBy('r.libelle')
            ->getQuery()
            ->getResult();
    }

    /**
     * niveau = 0 : tous ; 2 : sans fille ; 1 : formule
     *
     * @param $type
     * @param int $niveau
     * @return array
     */
    public function getRubriques($type,$niveau = 0)
    {
        $result = $this->createQueryBuilder('r');

        $andSet = false;

        if($type < 10)
        {
            $result = $result->where('r.type = :type')->setParameter('type',$type);
            $andSet = true;
        }

        if($niveau == 1)
        {
            if($andSet) $result = $result->andWhere("r.formule <> ''");
            else $result = $result->where("r.formule <> ''");
        }
        elseif ($niveau == 2)
        {
            if($andSet) $result = $result->andWhere("r.formule = ''");
            else $result = $result->where("r.formule = ''");
        }

        return $result->orderBy('r.type')
            ->addOrderBy('r.libelle')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $libelle
     * @param $type
     * @return mixed
     */
    public function getRubriqueByLibelleType($libelle,$type)
    {
        return $this->createQueryBuilder('r')
            ->where('r.libelle = :libelle')
            ->setParameter('libelle',$libelle)
            ->andWhere('r.type = :type')
            ->setParameter('type',$type)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getByIds($ids = array())
    {
        return $this->createQueryBuilder('r')
            ->where('r.id IN (:ids)')
            ->setParameter('ids',$ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->createQueryBuilder('r')
            ->where('r.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Rubrique $rubrique
     * @return array
     */
    public function getFilles(Rubrique $rubrique)
    {
        $filles = array();
        $idsSpilters = explode(';',$rubrique->getRubriquesFilles());
        foreach ($idsSpilters as $idsSpilter)
        {
            $id = intval($idsSpilter);
            if($id != 0) $filles[] = $this->getById($id);
        }
        return $filles;
    }

    /**
     * @param Rubrique $rubrique
     * @return array
     */
    public function getFillesObject(Rubrique $rubrique)
    {
        $ids = $this->getFilles($rubrique);
        $results = array();

        foreach ($ids as $id) $results[] = $this->getById($id);
        return $results;
    }

    /**
     * @param IndicateurOperande $indicateurOperande
     * @param Dossier $dossier
     * @param bool $withPcgNot
     * @return stdClass
     */
    public function getRubriquesInOperande(IndicateurOperande $indicateurOperande,Dossier $dossier,$withPcgNot = true)
    {
        $rubrique = $indicateurOperande->getRubrique();
        $rubriques = array();
        $rubriquesTemps = array();
        if($rubrique->getFormule() != '')
        {
            $rubriquesTemps = $this->getFillesObject($rubrique);
            $formule = $rubrique->getFormule();
        }
        else
        {
            $rubriquesTemps[] = $rubrique;
            $formule = '#';
        }

        foreach ($rubriquesTemps as $rubriquesTemp)
        {
            $rb = new stdClass();
            $pcgsSet = $this->getEntityManager()->getRepository('AppBundle:PcgRubrique')->getPcgsRubriquesSets($rubriquesTemp,$withPcgNot);

            $pcgs = $pcgsSet->in;
            $pcgsOuts = $pcgsSet->out;

            $pccs = array();
            $pccsTemp = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgs,$dossier,$pcgsOuts);
            foreach ($pccsTemp as $item)
            {
                $pccs[$item->getCompte()] = $item;
            }
            $rb->pccs = $pccs;
            $rb->solde = $rubriquesTemp->getSolde();
            $rb->typeCompte = $rubriquesTemp->getTypeCompte();
            $rubriques[] = $rb;
        }

        $operandeRubriques = new stdClass();
        $operandeRubriques->variation = $indicateurOperande->getVariationN();
        $operandeRubriques->rubriques = $rubriques;
        $operandeRubriques->formule = $formule;
        return $operandeRubriques;
        /**
         *  return stdClass
         * ->variation;
         * ->rubriques: pccs,solde,typeCompte
         * ->formule;
         */
    }

    /**
     * @param IndicateurOperande $indicateurOperande
     * @param Dossier $dossier
     * @return stdClass
     */
    public function getRubriquesInOperandesV2(IndicateurOperande $indicateurOperande,Dossier $dossier)
    {
        $rubrique = $indicateurOperande->getRubrique();
        $rubriques = array();
        $rubriquesTemps = array();
        if($rubrique->getFormule() != '')
        {
            $rubriquesTemps = $this->getFillesObject($rubrique);
            $formule = $rubrique->getFormule();
        }
        else
        {
            $rubriquesTemps[] = $rubrique;
            $formule = '#';
        }

        foreach ($rubriquesTemps as $rubriquesTemp)
        {
            $pcgsRubriques = $this->getEntityManager()->getRepository('AppBundle:PcgsRubrique')->getPcgs($rubriquesTemp,false);
            $pcgsIn = [];
            $pcgsOut = [];
            $pcgsRubriquesIns = [];

            foreach ($pcgsRubriques as $pcgsRubrique)
            {
                if($pcgsRubrique->getNegation() == 0)
                {
                    $pcgsIn[] = $pcgsRubrique->getPcg();
                    $pcgsRubriquesIns[] = $pcgsRubrique;
                }
                else $pcgsOut[] = $pcgsRubrique->getPcg();
            }

            $pccs = [];
            $pccsTemps = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgsIn,$dossier,$pcgsOut);

            foreach ($pccsTemps as $pccsTemp)
            {
                $compte = $pccsTemp->getCompte();
                if(!array_key_exists($compte,$pccs))
                {
                    $solde = 0;
                    $typeCompte = 0;
                    foreach ($pcgsRubriquesIns as $pcgsRubriquesIn)
                    {
                        $compteTemp = $pcgsRubriquesIn->getPcg()->getCompte();
                        if(substr($compte,0,strlen($compteTemp)) == $compteTemp)
                        {
                            $solde = $pcgsRubriquesIn->getSolde();
                            $typeCompte = $pcgsRubriquesIn->getTypeCompte();
                            break;
                        }
                    }
                    $pccInRubrique = new stdClass();
                    $pccInRubrique->pcc = $pccsTemp;
                    $pccInRubrique->solde = $solde;
                    $pccInRubrique->typeCompte = $typeCompte;
                    $pccs[$compte] = $pccInRubrique;
                }
            }

            $rb = new stdClass();
            $rb->pccsInRubriques = $pccs;
            $rb->libelle = $rubriquesTemp->getLibelle();
            $rubriques[] = $rb;
        }

        $operandeRubriques = new stdClass();
        $operandeRubriques->formule = $formule;
        $operandeRubriques->variation = $indicateurOperande->getVariationN();
        $operandeRubriques->rubriques = $rubriques;
        return $operandeRubriques;
        /**
         *  return stdClass
         * ->variation;
         * ->rubriques[]: pccsInRubriques[compte]: pcc,solde,typeCompte
         * ->formule;
         */
    }

    /**
     * @param IndicateurOperande $indicateurOperande
     * @return stdClass
     */
    public function getRubriquesInOperandes(IndicateurOperande $indicateurOperande)
    {
        $rubrique = $indicateurOperande->getRubrique();
        $rubriques = array();
        $rubriquesTemps = array();
        if($rubrique->getFormule() != '')
        {
            $rubriquesTemps = $this->getFillesObject($rubrique);
            $formule = $rubrique->getFormule();
        }
        else
        {
            $rubriquesTemps[] = $rubrique;
            $formule = '#';
        }

        foreach ($rubriquesTemps as $rubriquesTemp)
        {
            $pcgsRubriques = $this->getEntityManager()->getRepository('AppBundle:PcgsRubrique')->getPcgs($rubriquesTemp,false);
            $pcgsIn = [];
            $pcgsOut = [];
            $pcgsRubriquesIns = [];

            foreach ($pcgsRubriques as $pcgsRubrique)
            {
                if($pcgsRubrique->getNegation() == 0)
                {
                    $pcgsIn[] = $pcgsRubrique->getPcg()->setCochage($pcgsRubrique->getSolde())->setIdEtatCompte($pcgsRubrique->getTypeCompte());
                    $pcgsRubriquesIns[] = $pcgsRubrique;
                }
                else $pcgsOut[] = $pcgsRubrique->getPcg();
            }

            /*$pccs = [];
            $pccsTemps = $this->getEntityManager()->getRepository('AppBundle:Pcc')->getPCCByPCG($pcgsIn,$dossier,$pcgsOut);

            foreach ($pccsTemps as $pccsTemp)
            {
                $compte = $pccsTemp->getCompte();
                if(!array_key_exists($compte,$pccs))
                {
                    $solde = 0;
                    $typeCompte = 0;
                    foreach ($pcgsRubriquesIns as $pcgsRubriquesIn)
                    {
                        $compteTemp = $pcgsRubriquesIn->getPcg()->getCompte();
                        if(substr($compte,0,strlen($compteTemp)) == $compteTemp)
                        {
                            $solde = $pcgsRubriquesIn->getSolde();
                            $typeCompte = $pcgsRubriquesIn->getTypeCompte();
                            break;
                        }
                    }
                    $pccInRubrique = new stdClass();
                    $pccInRubrique->pcc = $pccsTemp;
                    $pccInRubrique->solde = $solde;
                    $pccInRubrique->typeCompte = $typeCompte;
                    $pccs[$compte] = $pccInRubrique;
                }
            }*/

            $rb = new stdClass();
            $rb->pcgsIns = $pcgsIn;
            $rb->pcgsOuts = $pcgsOut;
            $rb->libelle = $rubriquesTemp->getLibelle();
            $rb->solde = $rubriquesTemp->getSolde();
            $rb->typeCompte = $rubriquesTemp->getTypeCompte();
            $rubriques[] = $rb;
        }

        $operandeRubriques = new stdClass();
        $operandeRubriques->formule = $formule;
        $operandeRubriques->variation = $indicateurOperande->getVariationN();
        $operandeRubriques->rubriques = $rubriques;
        return $operandeRubriques;
        /**
         *  return stdClass
         * ->variation;
         * ->rubriques[]: pccsInRubriques[compte]: pcc,solde,typeCompte
         * ->formule;
         */
    }
}