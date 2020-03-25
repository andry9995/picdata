<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Banque;
use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Pcc;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class BanqueCompteRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @return BanqueCompte[]
     */
    public function getBanqueCompteByDossier(Dossier $dossier)
    {
        return  $this->createQueryBuilder('bc')
            ->where('bc.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->andWhere('bc.status = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @return Banque[]
     */
    public function getBanques(Dossier $dossier)
    {
        $banquesComptes = $this->getBanquesComptes($dossier);
        $banques = [];
        foreach ($banquesComptes as $banquesCompte)
        {
            $bq = $banquesCompte->getBanque();
            $key = '_'.$bq->getId();
            if (!array_key_exists($key,$banques)) $banques[$key] = $bq;
        }
        return $banques;
    }

    /**
     * @param Dossier $dossier
     * @param null $banque
     * @return BanqueCompte[]
     */
    public function getBanquesComptes(Dossier $dossier = null,$banque = null)
    {
        if (!$dossier) return [];

        $con = new CustomPdoConnection();
        $pdo = $con->connect();

//        $query = "select distinct bc.id
//                  from releve r
//                  left join banque_compte bc on bc.id=r.banque_compte_id
//                  inner join banque bq on bq.id = bc.banque_id
//                  where bc.dossier_id=:dossier ";

        $query = "select distinct bc.id 
                  from imputation_controle ic 
                  left join banque_compte bc on bc.id=ic.banque_compte_id 
                  inner join banque bq on bq.id = bc.banque_id 
                  inner join separation s on ic.image_id = s.image_id and s.souscategorie_id = 10
                  where bc.dossier_id=:dossier ";

        $params = ['dossier' => $dossier->getId()];
        if ($banque != null)
        {
            $query .= "AND bq.id = :banque";
            $params['banque'] = $banque->getId();
        }

        $prep = $pdo->prepare($query);

        $prep->execute($params);
        $idsT = $prep->fetchAll();
        $ids = [];
        foreach ($idsT as $item)
            $ids[] = $item->id;

        return $banquesComptes = $this->createQueryBuilder('bc')
            ->where('bc.id IN (:ids)')
            ->setParameter('ids',$ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @param Banque|null $banque
     * @param BanqueCompte|null $banqueCompte
     * @return BanqueCompte[]
     */
    public function getBanqueComptes(Dossier $dossier,Banque $banque = null, BanqueCompte $banqueCompte = null)
    {
        $results = [];
        if ($banqueCompte) $results[] = $banqueCompte;
        else
        {
            $results = $this->createQueryBuilder('bc')
                ->where('bc.dossier = :dossier')
                ->setParameter('dossier',$dossier);

            if ($banque)
                $results = $results
                    ->andWhere('bc.banque = :banque')
                    ->setParameter('banque',$banque);

            $results = $results->getQuery()->getResult();
        }

        return $results;
    }

    /**
     * @param Dossier $dossier
     * @param $compte
     * @return BanqueCompte
     */
    public function getOneByDossierCompte(Dossier $dossier, $compte)
    {
        return $this->createQueryBuilder('bc')
            ->where('bc.dossier = :dossier')
            ->andWhere('bc.numcompte = :compte')
            ->andWhere('bc.status = 1')
            ->setParameters([
                'dossier' => $dossier,
                'compte' => $compte
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
?>