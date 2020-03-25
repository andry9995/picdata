<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 25/04/2018
 * Time: 16:09
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Linxo;
use AppBundle\Entity\LinxoDossier;
use Doctrine\ORM\EntityRepository;

class LinxoDossierRepository extends EntityRepository
{
    /**
     * @param Linxo $linxo
     * @param $idLinxo
     * @param $idConnection
     * @param $name
     * @param $type
     * @param $iban
     * @param $compteLinxo
     * @param $classification
     * @return LinxoDossier|mixed
     */
    public function getLinxoDossier(Linxo $linxo,$idLinxo,$idConnection,$name,$type,$iban,$compteLinxo,$classification)
    {
        $linxoDossier = $this->createQueryBuilder('ld')
            ->where('ld.idLinxo = :idLinxo')
            ->andWhere('ld.linxo = :linxo')
            ->setParameters([
                'idLinxo' => $idLinxo,
                'linxo' => $linxo])
            ->getQuery()
            ->getOneOrNullResult();

        $banqueCompte = null;
        $em = $this->getEntityManager();
        $create = false;
        if (is_null($linxoDossier))
        {
            $linxoDossier = new LinxoDossier();
            $create = true;
        }

        if (!is_null($linxoDossier) && !is_null($linxoDossier->getBanqueCompte()))
            $banqueCompte = $this->getEntityManager()->getRepository('AppBundle:BanqueCompte')
                ->createQueryBuilder('bc')
                ->leftJoin('bc.dossier','d')
                ->leftJoin('d.site','s')
                ->where('bc.numcompte LIKE :numcompte')
                ->andWhere('s.client = :client')
                ->setParameters(
                    [
                        'client' => $linxo->getClient(),
                        'numcompte' => $compteLinxo
                    ])
                ->getQuery()->getOneOrNullResult();
        //else $banqueCompte = $linxoDossier->getBanqueCompte();

        if (!is_null($linxoDossier->getBanqueCompte())) $banqueCompte = $linxoDossier->getBanqueCompte();

        $linxoDossier->setType($type);
        $linxoDossier->setBanqueCompte($banqueCompte);
        $linxoDossier->setClassification($classification);
        $linxoDossier->setCompteLinxo($compteLinxo);
        $linxoDossier->setIban($iban);
        $linxoDossier->setIdConnection($idConnection);
        $linxoDossier->setIdLinxo($idLinxo);
        $linxoDossier->setLinxo($linxo);
        $linxoDossier->setName($name);
        $linxoDossier->setRecuperation((is_null($banqueCompte) ? 0 : 3));

        if ($create) $em->persist($linxoDossier);
        $em->flush();

        return $linxoDossier;
    }
}