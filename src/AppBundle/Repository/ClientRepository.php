<?php

/**
 * Created by PhpStorm.
 * User: MAHARO
 * Date: 02/02/2017
 * Time: 08:30
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurClient;
use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository
{

    /**
     * Get All clients Actifs et Inactifs
     *
     * @return array
     */
    public function getAll()
    {
        $clients = $this->getEntityManager()
            ->getRepository('AppBundle:Client')
            ->createQueryBuilder('client')
            ->select('client')
            ->orderBy('client.nom')
            ->getQuery()
            ->getResult();
        return $clients;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Liste des clients actifs
     *
     * @return array
     */
    public function getAllClientActif()
    {
        $clients = $this->getEntityManager()
            ->getRepository('AppBundle:Client')
            ->createQueryBuilder('client')
            ->select('client')
            ->where('client.status = :status')
            ->setParameters([
                'status' => 1,
            ])
            ->orderBy('client.nom')
            ->getQuery()
            ->getResult();
        return $clients;
    }

    /**
     * Retourner le client auquel un utilisateur apartient
     *
     * @param \AppBundle\Entity\Utilisateur $user
     *
     * @return mixed|null
     */
    public function getClientByUser(\AppBundle\Entity\Utilisateur $user)
    {
        if ($user->getClient()) {
            $client = $this->getEntityManager()
                ->getRepository('AppBundle:Client')
                ->createQueryBuilder('client')
                ->select('client')
                ->where('client.status = :status')
                ->andWhere('client = :the_client')
                ->setParameters([
                    'status' => 1,
                    'the_client' => $user->getClient(),
                ])
                ->orderBy('client.nom')
                ->getQuery()
                ->getOneOrNullResult();
            return $client;
        }
        return NULL;
    }

    /**
     * Liste clients d'un utilisateur
     *
     * @param Utilisateur $user
     *
     * @return \AppBundle\Entity\Client|array|null
     */
    public function getUserClients(Utilisateur $user)
    {
        $user_type = $user->getAccesUtilisateur()
            ->getType();
        $user_role = $user->getAccesUtilisateur()
            ->getCode();
        $clients = [];
        if ($user_type == 2) {
            //Utilisateur scriptura
            if ($user_role == 'ROLE_SCRIPTURA_ADMIN') {
                $clients = $this->getEntityManager()
                    ->getRepository('AppBundle:Client')
                    ->createQueryBuilder('client')
                    ->select('client')
                    ->where('client.status = :status')
                    ->setParameters([
                        'status' => 1,
                    ])
                    ->orderBy('client.nom', 'ASC')
                    ->getQuery()
                    ->getResult();
            } else {
                $id_client = [0];
                $user_clients = $this->getEntityManager()
                    ->getRepository('AppBundle:UtilisateurClient')
                    ->createQueryBuilder('utilisateurClient')
                    ->select('utilisateurClient')
                    ->innerJoin('utilisateurClient.utilisateur', 'utilisateur')
                    ->addSelect('utilisateur')
                    ->innerJoin('utilisateurClient.client', 'client')
                    ->addSelect('client')
                    ->where('utilisateur = :utilisateur')
                    ->andWhere('client.status = :status')
                    ->setParameters([
                        'utilisateur' => $user,
                        'status' => 1,
                    ])
                    ->getQuery()
                    ->getResult();
                if (count($user_clients) > 0) {
                    /** @var UtilisateurClient $user_client */
                    foreach ($user_clients as $user_client) {
                        $id_client[] = $user_client->getClient()->getId();
                    }
                }

                $qb = $this->getEntityManager()
                    ->getRepository('AppBundle:Client')
                    ->createQueryBuilder('client');
                $clients = $qb
                    ->select('client')
                    ->where($qb->expr()->in('client.id', $id_client))
                    ->orderBy('client.nom')
                    ->getQuery()
                    ->getResult();
            }
        } else {
            $client = $user->getClient();
            $clients[] = $client;
        }

        if (count($clients) == 0) {
            $clients[] = $user->getClient();
        }
        return $clients;
    }

    public function getClientByName($name){
        $name = str_replace('_', '%', str_replace('-', '%', $name));

        $clients = $this->createQueryBuilder('c')
            ->where('c.nom like :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();

        if(count($clients) > 0){
            return $clients[0];
        }

        return null;
    }
}