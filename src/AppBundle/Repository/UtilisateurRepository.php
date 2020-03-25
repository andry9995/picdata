<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Site;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;

class UtilisateurRepository extends EntityRepository
{
    /**
     * Get one user by login
     * @deprecated use getUserByEmail instead
     *
     * @param $login
     * @return mixed
     */
    public function getUserByLogin($login)
    {
        return $this->createQueryBuilder('u')
            ->where('u.login = :login')
            ->setParameter('login',$login)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Get one user by email
     *
     * @param $email
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserByEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email',$email)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Client $client
     * @return array
     */
    public function getClientUsers(Client $client)
    {
        $users = $this->getEntityManager()
            ->getRepository('AppBundle:Utilisateur')
            ->createQueryBuilder('u')
            ->select('u')
            ->addSelect("CASE WHEN TRIM(CONCAT_WS(' ', u.nom, u.prenom)) != '' THEN TRIM(CONCAT_WS(' ', UPPER(u.nom), u.prenom)) ELSE u.email END AS nomComplet")
            ->innerJoin('u.client', 'client')
            ->addSelect('client')
            ->where('client = :client')
            ->innerJoin('u.accesUtilisateur', 'accesUtilisateur')
            ->addSelect('accesUtilisateur')
            ->andWhere('accesUtilisateur.type > :type')
            ->setParameters(array(
                'client' => $client,
                'type' => 1
            ))
            ->orderBy('nomComplet')
            ->addOrderBy('u.prenom')
            ->addOrderBy('u.nom')
            ->getQuery()
            ->getResult();
        return $users;
    }

    /**
     * @param Utilisateur $utilisateur
     * @param $client
     * @return array
     */
    public function getChildsUsers(Utilisateur $utilisateur,Client $client)
    {
        $type = $utilisateur->getAccesUtilisateur()->getType();
        $users = $this->getEntityManager()
            ->getRepository('AppBundle:Utilisateur')
            ->createQueryBuilder('u')
            ->leftJoin('u.accesUtilisateur','accesUtilisateur')
            ->leftJoin('u.client', 'client')
            ->where('client = :client');

        if($type != 2)
        {
            $users = $users
                ->andWhere('accesUtilisateur.type > :type')
                ->orWhere('u = :utilisateur')
                ->setParameters(array(
                    'type' => $utilisateur->getAccesUtilisateur()->getType(),
                    'utilisateur' => $utilisateur
                ));
            $client = $utilisateur->getClient();
        }
        else
        {
            $users = $users
                ->orWhere('accesUtilisateur.type = 2');
        }

        return $users
            ->setParameter('client',$client)
            ->orderBy('client.nom')
            ->addOrderBy('u.prenom')
            ->addOrderBy('u.nom')
            ->getQuery()->getResult();
    }

    public function getUtilisateursByClient(Utilisateur $utilisateur, Client $client, Site $site = null, $exercice = null){

        if($utilisateur->getAccesUtilisateur()->getType() <= 2){

            return $this->createQueryBuilder('u')
                ->where('u.client = :client')
                ->setParameter('client', $client)
                ->getQuery()
                ->getResult();
        }


        $utilisateurs[] = $utilisateur;

        if($utilisateur->getAccesUtilisateur()->getType() == 3){
            //Ampiana ny utilisateurs sites
            if($site !== null) {
                $utilisateurSites = $this->getEntityManager()
                    ->getRepository('AppBundle:UtilisateurSite')
                    ->findBy(array('site' => $site));

                foreach ($utilisateurSites as $utilisateurSite){
                    if(!in_array($utilisateurSite->getUtilisateur(), $utilisateurs))
                        $utilisateurs[] = $utilisateurSite->getUtilisateur();
                }
            }
            else{
                $sites = $this->getEntityManager()
                    ->getRepository('AppBundle:Site')
                    ->findBy(array('client' => $client));

                foreach ($sites as $site){
                    $utilisateurSites = $this->getEntityManager()
                        ->getRepository('AppBundle:UtilisateurSite')
                        ->findBy(array('site' => $site));

                    foreach ($utilisateurSites as $utilisateurSite){
                        if(!in_array($utilisateurSite->getUtilisateur(), $utilisateurs))
                            $utilisateurs[] = $utilisateurSite->getUtilisateur();
                    }
                }
            }
        }

        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->getUserDossier($utilisateur,$client,$site,$exercice);

        foreach ($dossiers as $dossier) {
            $utilisateurDossiers = $this->getEntityManager()
                ->getRepository('AppBundle:UtilisateurDossier')
                ->findBy(array('dossier' => $dossier));

            foreach ($utilisateurDossiers as $utilisateurDossier){
                if(!in_array($utilisateurDossier->getUtilisateur(), $utilisateurs))
                    $utilisateurs[] = $utilisateurDossier->getUtilisateur();
            }
        }

        return $utilisateurs;



    }

    public function getUtilisateursByTypeAcces($typeAcces){
        return $this->createQueryBuilder('u')
            ->innerJoin('u.accesUtilisateur', 'accesUtilisateur')
            ->where('accesUtilisateur.type = :type')
            ->setParameter('type', $typeAcces)
            ->getQuery()
            ->getResult();
    }

    public function getUtilisateurAccesDrt(){
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "select * from utilisateur u 
                inner join acces_utilisateur au on (au.id = u.acces_utilisateur_id)
                inner join menu_utilisateur mu on (mu.utilisateur_id = u.id)
                inner join menu m on (m.id = mu.menu_id)
                where m.lien = 'drt_index'";
        $prep = $pdo->prepare($query);
        $prep->execute();
        $result = $prep->fetchAll();
        if(count($result) == 0){
            $query = "select * from menu_par_role menuRole
                    inner join acces_utilisateur au on (au.id = menuRole.acces_utilisateur_id)
                    inner join utilisateur u on (u.acces_utilisateur_id = au.id)
                    inner join menu_utilisateur mu on (mu.utilisateur_id = u.id)
                    inner join menu m on (m.id = mu.menu_id)
                    where m.lien = 'drt_index'";
            $prep = $pdo->prepare($query);
            $prep->execute();
            $result = $prep->fetchAll();
        }
        return $result;
    }

    /**
     * @param Client $client
     * @param Site $site
     * @return array
     */
    public function getClientUsersBySite(Client $client, Site $site = null)
    {
        $con = new CustomPdoConnection();
        $pdo = $con->connect();
        $query = "select s.nom as site, c.nom as client, u.*, au.libelle as acces, d.nom as dossier, tu.type as type_user
                from utilisateur u 
                inner join acces_utilisateur au on (au.id = u.acces_utilisateur_id)
                left join type_utilisateur tu on (tu.id = u.type_utilisateur_id)
                inner join utilisateur_dossier ud on (ud.utilisateur_id = u.id)
                inner join dossier d on (d.id = ud.dossier_id)
                inner join client c on (c.id = u.client_id)
                inner join site s on (s.client_id = c.id)
                where au.type > 1
                and c.id = ".$client->getId()." ";
        if($site) $query .= "and s.id = ".$site->getId()." ";
        $query .= "group by u.id
                order by u.nom, u.prenom";
        $prep = $pdo->prepare($query);
        $prep->execute();
        return $prep->fetchAll();
    }
}