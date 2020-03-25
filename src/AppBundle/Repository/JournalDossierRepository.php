<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 16/02/2017
 * Time: 09:11
 */

namespace AppBundle\Repository;

use AppBundle\Entity\BanqueCompte;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\JournalDossier;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class JournalDossierRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @return mixed
     */
    public function getJournalADs(Dossier $dossier)
    {
        return $this->createQueryBuilder('jd')
            ->leftJoin('jd.journal','j')
            ->where('j.id = 1')
            ->andWhere('jd.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @return JournalDossier[]
     */
    public function getJournaux(Dossier $dossier)
    {
        return $this->createQueryBuilder('jd')
            ->where('jd.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->orderBy('jd.libelle')
            ->addOrderBy('jd.codeStr')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Dossier $dossier
     * @return JournalDossier[]
     */
    public function getJournauxBanqueUsed(Dossier $dossier)
    {
        /** @var BanqueCompte[] $banquesComptes */
        $banquesComptes = $this->getEntityManager()->getRepository('AppBundle:BanqueCompte')
            ->createQueryBuilder('bc')
            ->where('bc.dossier = :dossier')
            ->setParameter('dossier',$dossier)
            ->andWhere('bc.status = 1')
            ->andWhere('bc.journalDossier IS NOT NULL')
            ->getQuery()
            ->getResult();

        /** @var JournalDossier[] $results */
        $results = [];
        foreach ($banquesComptes as $banquesCompte) $results[$banquesCompte->getJournalDossier()->getId()] = $banquesCompte->getJournalDossier();

        return $results;
    }


    public function getJournauxPicdocActifs(Dossier $dossier, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "select distinct jd.id from journal_dossier jd 
                        inner join journal j on j.id = jd.journal_id
                        inner join categorie c on c.journal_id = j.id
                        inner join separation sep on sep.categorie_id = c.id and sep.image_id in (select i.id from image i inner join lot l on l.id = i.lot_id and i.exercice = :exercice and l.dossier_id = :dossier_id_1)
                        where jd.dossier_id = :dossier_id_2;";

        $prep = $pdo->prepare($query);

        $prep->execute([
            'dossier_id_1' => $dossier->getId(),
            'dossier_id_2' => $dossier->getId(),
            'exercice' => $exercice
        ]);

        $res = $prep->fetchAll();

        $journalDossiers = [];

        foreach ($res as $re){
            $journalDossiers[] = $this->find($re->id);
        }

        return $journalDossiers;
    }

    public function getJournauxComptaActifs(Dossier $dossier, $exercice)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "select distinct jd.id from journal_dossier jd 
                        inner join ecriture e on e.journal_dossier_id = jd.id
                        where jd.dossier_id = :dossier_id and e.exercice = :exercice and e.image_id is not null;";

        $prep = $pdo->prepare($query);

        $prep->execute([
            'dossier_id' => $dossier->getId(),
            'exercice' => $exercice
        ]);

        $res = $prep->fetchAll();

        $journalDossiers = [];

        foreach ($res as $re){
            $journalDossiers[] = $this->find($re->id);
        }

        return $journalDossiers;
    }

    public function getJournalDossierActif(Dossier $dossier, $exercice, $code)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $query = "select distinct jd.id from journal_dossier jd 
                        inner join ecriture e on e.journal_dossier_id = jd.id
                        where jd.dossier_id = :dossier_id and e.exercice = :exercice and jd.code_str = :code;";

        $prep = $pdo->prepare($query);

        $prep->execute([
            'dossier_id' => $dossier->getId(),
            'exercice' => $exercice,
            'code' => $code
        ]);

        $res = $prep->fetchAll();

        $journalDossier = null;

        foreach ($res as $re){
            $journalDossier = $this->find($re->id);
            break;
        }

        return $journalDossier;
    }
}