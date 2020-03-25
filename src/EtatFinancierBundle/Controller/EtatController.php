<?php

namespace EtatFinancierBundle\Controller;

use AppBundle\Controller\Boost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Etat;
use AppBundle\Entity\EtatCompte;

class EtatController extends Controller
{
    /**
     * afficher filtre pour parametrage
     *
     * @param $etat
     * @return Response
     */
    public function showFiltreParametrageAction($etat)
    {
        $role = $this->get('security.authorization_checker');
        if($role->isGranted('ROLE_CLIENT'))
        {
            $filtre = 0;
            $client = null;
            if($role->isGranted('ROLE_ADMIN')) $filtre = 1;
            else $client = $this->getUser()->getClient();

            return $this->render('EtatFinancierBundle:Admin:filtre.html.twig', array('filtre'=>$filtre, 'client'=>$client, 'etat'=>$etat, 'parametrage'=>1));
        }

        return new Response('Accès refusé');
    }

    /**
     * Les etats financiers
     * @param $etat
     * @param $dossier
     * @param $regime
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showEtatAction($etat,$dossier,$regime)
    {
        if($regime != '0')
        {
            $dossier = null;
            $regime = Boost::deboost($regime,$this);
            if(is_bool($regime)) return new Response('security');
            $regime = $this->getDoctrine()->getRepository('AppBundle:RegimeFiscal')->createQueryBuilder('r')
                            ->where('r.id = :id')->setParameter('id',$regime)
                            ->getQuery()->getOneOrNullResult();
        }
        elseif($dossier != '0')
        {
            $regime = null;
            $dossier = Boost::deboost($dossier,$this);
            if(is_bool($dossier)) return new Response('security');
            $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->createQueryBuilder('d')
                            ->where('d.id = :id')->setParameter('id',$dossier)
                            ->getQuery()->getOneOrNullResult();
        }

        $em = $this->getDoctrine()->getManager();
        $etats = $em->getRepository('AppBundle:Etat')->getEtatParent($etat,$dossier,$regime);

        return $this->render('EtatFinancierBundle:Etat:etat.html.twig', array('etats'=>$etats));
    }

    /**
     * Les comptes
     *
     * @param $etat
     * @param $brut
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function comptesAction($etat,$brut)
    {
        $role = $this->get('security.authorization_checker');
        if($role->isGranted('ROLE_CLIENT'))
        {
            $etat = $this->getDoctrine()->getRepository('AppBundle:Etat')->createQueryBuilder('e')
                 ->where('e.id = :id')
                 ->setParameter('id',$etat)
                 ->getQuery()
                 ->getOneOrNullResult();
            
            $comptes = array();

            if($etat != null)
                $comptes = $this->getDoctrine()->getManager()->getRepository('AppBundle:EtatCompte')->getComptes($etat,$brut);

            return $this->render('EtatFinancierBundle:Etat:compte.html.twig', array('comptes'=>$comptes, 'brut'=>$brut));
        }
        
        return new Response('Accès refusé');
    }

    /**
     * edit etat compte params:id_compte (id pcg or pcc),id_etat_compte,id_etat,status_debit (1:0),status_credit (1:0)
     *
     * @param $id_compte
     * @param $id_etat_compte
     * @param $id_etat
     * @param $status_debit
     * @param $status_credit
     * @param $brut
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function editEtatCompteAction($id_compte,$id_etat_compte,$id_etat,$status_debit,$status_credit,$brut)
    {
        //sens
        if($status_debit == 1 && $status_credit == 1) $sens = 3;
        elseif($status_debit == 0 && $status_credit == 1) $sens = 2;
        elseif($status_debit == 1 && $status_credit == 0) $sens = 1;
        else $sens = 0;

        $role = $this->get('security.authorization_checker');
        if($role->isGranted('ROLE_CLIENT'))
        {
            $em = $this->getDoctrine()->getManager();
            
            $etat_compte = $this->getDoctrine()->getRepository('AppBundle:EtatCompte')->createQueryBuilder('ec')
                            ->where('ec.id = :id')
                            ->setParameter('id',$id_etat_compte)
                            ->getQuery()
                            ->getOneOrNullResult();

            $etat = $this->getDoctrine()->getRepository('AppBundle:Etat')->createQueryBuilder('e')
                            ->where('e.id = :id')
                            ->setParameter('id',$id_etat)
                            ->getQuery()
                            ->getOneOrNullResult();

            //insertion            
            if($etat_compte == null)
            {
                $etat_compte = new EtatCompte();
                $etat_compte->setSens($sens)
                            ->setBrutAmort($brut)
                            ->setEtat($etat);

                //compte
                if($etat->getRegimeFiscal() != null)
                {
                    $compte = $this->getDoctrine()->getRepository('AppBundle:Pcg')->createQueryBuilder('pcg')
                                    ->where('pcg.id = :id')
                                    ->setParameter('id',$id_compte)
                                    ->getQuery()
                                    ->getOneOrNullResult();
                    $etat_compte->setPcg($compte);
                }
                if($etat->getDossier() != null)
                {
                    $compte = $this->getDoctrine()->getRepository('AppBundle:Pcc')->createQueryBuilder('pcc')
                                    ->where('pcc.id = :id')
                                    ->setParameter('id',$id_compte)
                                    ->getQuery()
                                    ->getOneOrNullResult();
                    $etat_compte->setPcc($compte);
                }

                $em->persist($etat_compte);
                $em->flush();
                return new Response($etat_compte->getId());
            }
            //update delete
            else
            {
                if($sens == 0)
                {
                    $em->remove($etat_compte);
                    $em->flush();
                    return new Response(1);
                }
                else
                {
                    $etat_compte->setSens($sens);
                    $em->flush();
                    return new Response(1);
                }
            }
            
            return new Response($id_compte.$id_etat_compte.$id_etat.$status_debit.$status_credit);
        }

        return new Response('Accès refusé');
    }

    /**
     * edit etat params: $etat = id_etat , $action = 0(show) 1(edit) 2(edit only rang) 3(remove) , $rang = rang etat
     *
     * @param $etat
     * @param $action
     * @param $rang
     * @param $calcul
     * @param $parent
     * @param $etat_select
     * @param $regime
     * @param $dossier
     * @param $libelle
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function editEtatAction($etat,$action,$rang,$calcul,$parent,$etat_select,$regime,$dossier,$libelle)
    {
        $role = $this->get('security.authorization_checker');
        if($role->isGranted('ROLE_CLIENT'))
        {
            $etat = $this->getDoctrine()->getRepository('AppBundle:Etat')->createQueryBuilder('e')
                            ->where('e.id = :id')
                            ->setParameter('id',$etat)
                            ->getQuery()
                            ->getOneOrNullResult();
    
            $em = $this->getDoctrine()->getManager();
    
            //MODIFICATION
            if($etat != null)
            {
                //show fenetre edit etat
                if($action == 0)
                {
                    $etats_amis = $this->getDoctrine()->getRepository('AppBundle:Etat')->getEtatParent($etat->getEtatFinancier(),$etat->getDossier(),$etat->getRegimeFiscal());
                    return $this->render('EtatFinancierBundle:Etat:etat-edit.html.twig', array('etat'=>$etat,'etats_amis'=>$etats_amis));
                }
                //modification etat
                if($action == 1)
                {
                    $parent = $this->getDoctrine()->getRepository('AppBundle:Etat')->createQueryBuilder('e')
                                    ->where('e.id = :id')
                                    ->setParameter('id',$parent)
                                    ->getQuery()
                                    ->getOneOrNullResult();

                    $etat->setEtat($parent);

                    $etat   ->setLibelle($libelle)
                            ->setCalcul($calcul);
                    $em->flush();
                }
                //modification rang etat
                if($action == 2)
                {
                    $etat->setRang($rang);
                    $em->flush();
                }
                //supprimer etat
                if($action == 3)
                {
                    $em->remove($etat);
                    $em->flush();
                }
    
                return new Response(1);
            }
            //AJOUT NOUVELLE LIGNE return id nouvelle ligne
            else
            {
                $etat = new Etat();
                $etat->setEtatFinancier($etat_select);
                
                //etat parent
                $parent = $this->getDoctrine()->getRepository('AppBundle:Etat')->createQueryBuilder('e')
                                ->where('e.id = :id')
                                ->setParameter('id',$parent)
                                ->getQuery()
                                ->getOneOrNullResult();
                if($parent != null) $etat->setEtat($parent);
                
                //regime
                $regime = $this->getDoctrine()->getRepository('AppBundle:RegimeFiscal')->createQueryBuilder('r')
                                ->where('r.id = :id')
                                ->setParameter('id',$regime)
                                ->getQuery()
                                ->getOneOrNullResult();
                if($regime != null) $etat->setRegimeFiscal($regime);
    
                //dossier
                $dossier = $this->getDoctrine()->getRepository('AppBundle:Dossier')->createQueryBuilder('d')
                                ->where('d.id = :id')
                                ->setParameter('id',$dossier)
                                ->getQuery()
                                ->getOneOrNullResult();
                if($dossier != null) $etat->setDossier($dossier);            

                $em->persist($etat);
                $em->flush();

                return new Response($etat->getId());
            }
        }

        return new Response('Accès refusé');
    }
}