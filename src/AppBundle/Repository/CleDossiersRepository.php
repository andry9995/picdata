<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 27/02/2019
 * Time: 14:10
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Cle;
use AppBundle\Entity\CleDossiers;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Utilisateur;
use AppBundle\Functions\CustomPdoConnection;
use Doctrine\ORM\EntityRepository;

class CleDossiersRepository extends EntityRepository
{
    /**
     * @param Dossier $dossier
     * @return Cle[]
     */
    public function clesDesactiver(Dossier $dossier = null)
    {
        /** @var Cle[] $cles */
        $cles = [];
        if ($dossier)
        {
            /** @var CleDossiers[] $cleDossiers */
            $cleDossiers = $this->createQueryBuilder('cds')
                ->where('cds.dossier = :dossier')
                ->setParameter('dossier',$dossier)
                ->getQuery()
                ->getResult();
            foreach ($cleDossiers as $cleDossier) $cles[$cleDossier->getCle()->getId()] = $cleDossier->getCle();
        }
        else
        {
            $con = new CustomPdoConnection();
            $pdo = $con->connect();
            $params = [];
            $req = '
                SELECT 
                  c.id,
                    (
                    SELECT COUNT(cds.id)
                    FROM cle_dossiers cds
                    WHERE cds.cle_id = c.id
                    ) AS nb_cle_dossiers
                FROM cle c
                GROUP BY c.id;';

            $prep = $pdo->prepare($req);
            $prep->execute($params);
            $res = $prep->fetchAll();

            foreach ($res as $re)
            {
                $cles[$re->id.((intval($re->nb_cle_dossiers) > 0) ? '_' : '')] =
                    $this->getEntityManager()->getRepository('AppBundle:Cle')->find($re->id);
            }
        }

        return $cles;
    }

    /**
     * @param Cle $cle
     * @return CleDossiers[]
     */
    public function getCleDossiers(Cle $cle)
    {
        return $this->createQueryBuilder('cd')
            ->where('cd.cle = :cle')
            ->setParameter('cle',$cle)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Cle $cle
     * @param Dossier $dossier
     * @return CleDossiers
     */
    public function getCleDossiersForDossier(Cle $cle, Dossier $dossier)
    {
        return $this->createQueryBuilder('cds')
            ->where('cds.cle = :cle')
            ->andWhere('cds.dossier = :dossier')
            ->setParameters([
                'cle' => $cle,
                'dossier' => $dossier
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}