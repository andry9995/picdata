<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 09/05/2018
 * Time: 11:17
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Linxo;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;

class LinxoRepository extends EntityRepository
{
    /**
     * @param Utilisateur $user
     * @param Client $client
     * @param $site
     * @param $dossier
     * @return array
     */
    public function getLinxos(Utilisateur $user,Client $client,$site,$dossier)
    {
        //if ($user->getAccesUtilisateur()->getType() === 1)
        $dossiersUsers = $this->getEntityManager()->getRepository('AppBundle:Dossier')->getUserDossier($user,$client,$site);

        if (!is_null($dossier))
        {
            $linxos = $this->createQueryBuilder('l')
                ->where('l.dossier = :dossier')
                ->orWhere('(l.dossier IS NULL AND l.site = :site)')
                ->orWhere('(l.dossier IS NULL AND l.site IS NULL AND l.client = :client)')
                ->setParameters([
                    'dossier' => $dossier,
                    'site' => $dossier->getSite(),
                    'client' => $dossier->getSite()->getClient()])
                ->getQuery()
                ->getResult();
        }
        else if (!is_null($site))
        {
            $linxos = $this->createQueryBuilder('l')
                ->where('l.site = :site')
                ->orWhere('(l.site IS NULL AND l.client = :client)')
                ->setParameters([
                    'site' => $site,
                    'client' => $site->getClient()])
                ->getQuery()
                ->getResult();
        }
        else
        {
            $linxos = $this->createQueryBuilder('l')
                ->where('l.client = :client')
                ->setParameter( 'client', $client)
                ->getQuery()
                ->getResult();
        }

        /*$linxos = [];
        foreach ($linxosTemps as $linxosTemp)
        {
            $linxos[] = $linxosTemp->getLinxo();
        }*/
        return $linxos;
    }

    /**
     * @param Client $client
     * @param $compte
     * @param $token
     * @return Linxo|null|object
     */
    public function updateOrAddAccount(Client $client,$site,$dossier, $compte, $token)
    {
        $linxo = $this->findOneBy(['login' => $compte->email]);
        $em = $this->getEntityManager();
        $isNew = (is_null($linxo));
        if ($isNew)
        {
            $linxo = new Linxo();
            $linxo->setClient($client);
            $linxo->setSite($site);
            $linxo->setDossier($dossier);
        }
        $linxo
            ->setEmail($compte->email)
            ->setIdCompteLinxo($compte->id)
            ->setJsonCode(json_encode($token))
            ->setLogin($compte->email);

        if ($isNew) $em->persist($linxo);
        $em->flush();
        return $linxo;
    }
}