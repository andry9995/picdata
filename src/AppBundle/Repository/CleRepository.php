<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 13/12/2017
 * Time: 16:25
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Controller\StringExt;
use AppBundle\Entity\Banque;
use AppBundle\Entity\CfonbCode;
use AppBundle\Entity\Cle;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Releve;
use Doctrine\ORM\EntityRepository;

class CleRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getDefaults()
    {
        $cles = [];

        $cle = new \stdClass();
        $cle->cles = [/*'CHQ','CHEQUE'*/];
        $cles[] = $cle;
        return $cles;
    }

    /**
     * @param $libelle
     * @return int
     */
    public function getStatus($libelle)
    {
        $scalar = $this->createQueryBuilder('c')
             ->select('COUNT(c.cle) AS nb')
             ->where("( :libelle LIKE CONCAT(c.cle,' %') OR :libelle LIKE CONCAT('% ',c.cle,' %') OR :libelle LIKE CONCAT('% ',c.cle) )")
             ->setParameter('libelle',$libelle)
             ->getQuery()
             ->getScalarResult();
        $nb = intval($scalar[0]['nb']);

        return ($nb == 0) ? 0 : 5;
    }

    /**
     * @param Releve $releve
     * @param CfonbCode[] $cfonbCodeActives
     * @return object
     */
    public function getStatusCles(Releve $releve, $cfonbCodeActives = [])
    {
        $clesTemps = $this->createQueryBuilder('c')
            ->where(":libelle LIKE CONCAT('%',c.cle,'%')")
            ->setParameter('libelle',
                $this->getEntityManager()->getRepository('AppBundle:Releve')->getLibelleWithComplement($releve,$cfonbCodeActives)
            );

        $clesDesactivers = $this->clesDesactiver($releve->getBanqueCompte()->getDossier());
        if (count($clesDesactivers) > 0)
            $clesTemps = $clesTemps->andWhere('c.id NOT IN(:clesDesactivers)')->setParameter('clesDesactivers',$clesDesactivers);

        /** @var Cle[] $clesTemps */
        $clesTemps = $clesTemps
            ->getQuery()
            ->getResult();

        $allIds = [];
        foreach ($clesTemps as $clesTemp)
            $allIds[] = intval($clesTemp->getId());

        /** @var Cle[] $cles */
        $cles = [];

        foreach ($clesTemps as $clesTemp)
        {
            $idsMasters = $this->getEntityManager()->getRepository('AppBundle:CleSlave')
                ->getIdsAllMasters($clesTemp,$releve->getBanqueCompte()->getDossier());
            if (count(array_intersect($idsMasters,$allIds)) == 0)
                $cles[] = $clesTemp;
        }

        if (count($cles) == 1) return (object)['s' => 1, 'c' => $cles[0]];
        elseif (count($cles) == 0) return (object)['s' => 0, 'c' => null];
        else return (object)['s' => 2, 'c' => $cles];
    }

    /**
     * @param Releve $releve
     * @return array
     */
    public function getCles(Releve $releve)
    {
        $banqueCompte = $releve->getBanqueCompte();
        $cles = [];

        //CleDossier
        $clesTrouves = [];

        $clesTemps = $this->createQueryBuilder('c')
            ->where('SIMILARITY(c.cle,:libelle,:similarityMin) > :similarityMin')
            ->setParameters([
                'libelle' => $releve->getLibelle(),
                'similarityMin' => $this->getEntityManager()->getRepository('AppBundle:Releve')->similarityMinimum()
            ])
            ->getQuery()
            ->getResult();

        /*$clesTemps = $this->createQueryBuilder('c')
            ->where("(
                c.cle = :libelle OR 
                :libelle LIKE CONCAT(c.cle,' %') OR 
                :libelle LIKE CONCAT('% ',c.cle,' %') OR 
                :libelle LIKE CONCAT('% ',c.cle) OR 
                
                c.cle = CONCAT(:libelle,';') OR 
                :libelle LIKE CONCAT('%;',c.cle) OR
                :libelle LIKE CONCAT(c.cle,';%') OR 
                :libelle LIKE CONCAT('%;',c.cle,';%')                
                
                )")
            ->setParameter('libelle',$releve->getLibelle())
            ->getQuery()
            ->getResult();*/
        foreach ($clesTemps as $clesTemp)
        {
            //if ($this->getEntityManager()->getRepository('AppBundle:CleBanque')->isInBanque($clesTemp,$banqueCompte->getBanque()))
                $clesTrouves[] = $clesTemp;
        }

        foreach ($clesTrouves as $clesTrouve)
        {
            $cles = array_merge($cles,$this->getEntityManager()->getRepository('AppBundle:CleDossier')
                ->getCleDossiers($clesTrouve,$banqueCompte->getDossier()));
            /*$cles = $cles + $this->getEntityManager()->getRepository('AppBundle:CleDossier')
                    ->getCleDossiers($clesTrouve,$banqueCompte->getDossier());*/
        }

        //Cle Dossier
        foreach ($clesTrouves as $clesTrouve)
            $cles = array_merge($cles,$this->getEntityManager()->getRepository('AppBundle:CleCompte')
                ->getCles($clesTrouve,$banqueCompte));
            /*$cles = $cles + $this->getEntityManager()->getRepository('AppBundle:CleCompte')
                ->getCles($clesTrouve,$banqueCompte);*/

        return $cles;
    }

    /**
     * @param $compte
     * @return object
     */
    public function getCompteObject($compte)
    {
        $class = $this->getEntityManager()->getClassMetadata(get_class($compte))->getName();
        $classPcc = 'AppBundle\Entity\Pcc';
        return (object)
        [
            't' => ($class == $classPcc) ? 0 : 1,
            'c' => ($class == $classPcc) ? $compte->getCompte() : $compte->getCompteStr(),
            'i' => $compte->getIntitule(),
            'id' => Boost::boost($compte->getId())
        ];
    }

    /**
     * @param Cle $cle
     * @param $bilans
     * @param $tvas
     * @param $resultats
     * @param $occurence
     * @param $niveau
     * @return object
     */
    public function getCleObject(Cle $cle,$bilans,$tvas,$resultats,$occurence,$niveau)
    {
        return (object)
        [
            'id' => Boost::boost($cle->getId()),
            'bs' => $bilans,
            'ts' => $tvas,
            'rs' =>$resultats,
            'c' => $cle->getCle(),
            'o' => $occurence,
            'n' => $niveau
        ];
    }

    /**
     * @return Cle[]
     */
    public function getClePasPiece()
    {
        return $this->createQueryBuilder('c')
            ->where('c.pasPiece = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @return Cle[]
     */
    public function getClePasPieceDossier(Dossier $dossier)
    {
        $cles = $this->getClePasPiece();
        $clesDesactivers = $this->getEntityManager()->getRepository('AppBundle:CleDossiers')
            ->clesDesactiver($dossier);
        /** @var Cle[] $results */
        $results = [];
        foreach ($cles as $cle)
        {
            if (!array_key_exists($cle->getId(),$clesDesactivers))
                $results[] = $cle;
        }

        return $results;
    }

    /**
     * @return array
     */
    public function getClePasPieceLibelle()
    {
        $cles = $this->getClePasPiece();
        $cleStrs = [];

        foreach ($cles as $cle) $cleStrs[] = $cle->getCle();
        return $cleStrs;
    }

    /**
     * @param string $cle
     * @return object
     */
    public function explodeCle($cle = '')
    {
        $keysAccepteds = ['TVA','INT','COM'];
        $accOuverts = StringExt::strposAll($cle,'[[');
        $accFermes = StringExt::strposAll($cle,']]');
        $cle2s = [];
        if (count($accOuverts) == count($accFermes) && count($accOuverts) > 0)
        {
            $cle1 = $cle;
            for ($i = 0; $i < count($accOuverts); $i++)
            {
                $cleTemp = substr($cle,$accOuverts[$i] + 2,$accFermes[$i]  - $accOuverts[$i] - 2);
                $trouve = false;
                foreach ($keysAccepteds as $accepted)
                {
                    if (!is_bool(strpos(strtoupper($cleTemp),$accepted)))
                    {
                        $trouve = true;
                        break;
                    }
                }

                if ($trouve) $cle2s[] = $cleTemp;
                $cle1 = str_replace('[['. $cleTemp .']]',($trouve) ? '%' : '',$cle1);
            }
        }
        else $cle1 = $cle;

        for ($i = 0; $i < 10; $i++)
        {
            foreach ($cle2s as &$cle2) $cle2 = trim(str_replace(' ','',$cle2));
            $cle1 = str_replace('  ',' ',$cle1);
            $cle1 = str_replace('% ','%',$cle1);
            $cle1 = str_replace(' %','%',$cle1);
            $cle1 = str_replace(';','%',$cle1);
            $cle1 = str_replace('%%','%',$cle1);
        }

        return (object)
        [
            'c' => trim($cle1),
            'c2s' => $cle2s
        ];
    }

    /**
     * @param $libelle
     * @return Cle
     */
    public function getByLibelle($libelle)
    {
        return $this->createQueryBuilder('c')
            ->where('c.cle = :libelle')
            ->setParameter('libelle',$libelle)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $libelle
     * @param Dossier $dossier
     * @return Cle[]
     */
    public function getClesValideLibelle($libelle,Dossier $dossier = null)
    {
        /** @var Cle[] $clesTemps */
        $clesTemps = $this->createQueryBuilder('c')
            ->where(":libelle LIKE CONCAT('%',c.cle,'%')")
            ->setParameter('libelle',$libelle)
            ->orderBy('c.cle')
            ->getQuery()
            ->getResult();

        /** @var Cle[] $clesTemps */
        $cles = ($dossier) ? [] : $clesTemps;

        if ($dossier)
        {
            $allIds = [];
            foreach ($clesTemps as $clesTemp) $allIds[] = $clesTemp->getId();

            foreach ($clesTemps as $clesTemp)
            {
                $idsMasters = $this->getEntityManager()->getRepository('AppBundle:CleSlave')
                    ->getIdsAllMasters($clesTemp,$dossier);

                if (count(array_intersect($allIds,$idsMasters)) == 0)
                    $cles[] = $clesTemp;
            }
        }

        return $cles;
    }

    /**
     * @param Dossier $dossier
     * @param Banque|null $banque
     * @return Cle[]
     */
    public function clesDesactiver(Dossier $dossier,Banque $banque = null)
    {
        return $this->getEntityManager()->getRepository('AppBundle:CleDossiers')
            ->clesDesactiver($dossier);
    }

    /**
     * @return Cle[]
     */
    public function getListe()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.cle')
            ->getQuery()
            ->getResult();
    }
}