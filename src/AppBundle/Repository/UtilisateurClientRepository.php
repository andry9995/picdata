<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 21/02/2017
 * Time: 17:04
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;


class UtilisateurClientRepository extends EntityRepository
{
    /**
     * Liste des clients d'un utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return array
     */
    public function getUserClients(Utilisateur $utilisateur)
    {
        $clients = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurClient')
            ->createQueryBuilder('utilisateurClient')
            ->select('utilisateurClient')
            ->innerJoin('utilisateurClient.utilisateur', 'utilisateur')
            ->innerJoin('utilisateurClient.client', 'client')
            ->addSelect('client')
            ->where('utilisateur = :utilisateur')
            ->andWhere('client.status = :status')
            ->setParameters(array(
               'utilisateur' => $utilisateur,
                'status' => 1,
            ))
            ->orderBy('client.nom')
            ->getQuery()
            ->getResult();
        return $clients;
    }

    /**
     * Supprimer les clients d'un utilisateur
     *
     * @param Utilisateur $utilisateur
     * @return bool
     */
    public function removeUserClients(Utilisateur $utilisateur)
    {
        $clients = $this->getEntityManager()
            ->getRepository('AppBundle:UtilisateurClient')
            ->getUserClients($utilisateur);
        if ($clients) {
            $em = $this->getEntityManager();
            foreach ($clients as $client) {
                $em->remove($client);
            }
            $em->flush();
        }
        return true;
    }
}