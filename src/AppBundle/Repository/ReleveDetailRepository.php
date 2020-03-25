<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 11/08/2017
 * Time: 09:15
 */

namespace AppBundle\Repository;

use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\Releve;
use AppBundle\Entity\ReleveDetail;
use Doctrine\ORM\EntityRepository;

class ReleveDetailRepository extends EntityRepository
{
    /**
     * @param Releve $releve
     * @param array $clesPasPieces
     * @return int
     */
    public function getStatus(Releve $releve, $clesPasPieces = [])
    {
        /**
         * 0 : a categoriser
         * 1 : piece manquante (affecte sans piece)
         * 2 : inconnu
         * 3 : piece affecter (affecte avec piece) releve_detail
         * 4 : piece a affecter (montant trouve)
         * 5 : cle a valider (cle trouve)
         * 6 : cle valider (sans piece)
         * 7 : affecter a une image
         * 8 : cle annuler et a revalider
         * 9 : affecter a des images par cle
         * 10 : piece manquante : pas de piece a affecter
         */


        $em = $this->getEntityManager();

        $status = 0; //non impute



        $releveDetails = $this->createQueryBuilder('rd')
            ->where('rd.releve = :releve')
            ->setParameter('releve',$releve)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (count($releveDetails) > 0) $status = 1; //impute sans piece

        if ($releve->getEclate() == 2)
        {
            $relevesEclates = $this->getEntityManager()->getRepository('AppBundle:Releve')->getEclates($releve);
            if (count($relevesEclates) > 0)
            {
                $status = $this->getStatus($relevesEclates[0]);
                if ($status == 0) $status = 2; //INCONNU
                else $status = 3; //impute avec piece
            }
        }
        //$statuss = ['non impute','impute sans piece','Inconnu','impute avec piece'];

        if ($status == 0 && !is_null($releve->getCleDossier())) $status = 6; //impute par cle
        if ($status == 0 && !is_null($releve->getImageTemp())) $status = 7; //impute par piece
        if ($status == 0 && $releve->getFlaguer() == 2) $status = 9;

        $existanceClePasPiece = false;
        foreach ($clesPasPieces as $clesPasPiece)
        {
            if (strpos($releve->getLibelle(),$clesPasPiece->getCle()) !== false)
            {
                $existanceClePasPiece = true;
                break;
            }
        }
        if ($status == 0 && !$existanceClePasPiece && intval($releve->getPasImage()) == 0)
        {
            $imagesAAffecter = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
                ->getImageAAffecter($releve);
            if (count($imagesAAffecter) > 0)
            {
                //$status = 4;
                foreach ($imagesAAffecter as $imageAAffet)
                {
                    $status = 4;

                    $imputationControl = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                        ->createQueryBuilder('i')
                        ->where('i.image =  :image')
                        ->setParameter('image',$imageAAffet['tvaic']->getImage())
                        ->getQuery()
                        ->getOneOrNullResult();

                    //$imputationControl = new ImputationControle();
                    if (!is_null($imputationControl) &&
                        $imputationControl->getNumPaiement() != '' &&
                        strpos(strtoupper(str_replace(' ','',$releve->getLibelle())) , strtoupper(str_replace(' ','',$imputationControl->getNumPaiement()))) !== false)
                    {
                        $releve->setImageTemp($imageAAffet['tvaic']->getImage()->setFlaguer(1));
                        $em->flush();
                        $status = 7;
                    }
                }
            } //images a affecter
        }

        if ($status == 0 && intval($releve->getPasCle()) == 0)
        {
            $statusTemp = $em->getRepository('AppBundle:Cle')->getStatusCles($releve->getLibelle());
            //$status = $statusTemp;

            if ($statusTemp->s == 2) $status = 5;
            elseif ($statusTemp->s == 1)
            {
                $cleDossiers = $this->getEntityManager()->getRepository('AppBundle:CleDossier')
                    ->createQueryBuilder('cd')
                    ->where('cd.dossier = :dossier')
                    ->andWhere('cd.cle = :cle')
                    ->setParameters(array(
                        'dossier' => $releve->getBanqueCompte()->getDossier(),
                        'cle' => $statusTemp->c
                    ))
                    ->getQuery()
                    ->getResult();

                if (count($cleDossiers) == 1)
                {
                    $cleDossier = $cleDossiers[0];
                    //$cleDossier = new CleDossier();
                    if ($cleDossier->getTypeCompta() != 2)
                    {
                        $releve->setCleDossier($cleDossier);
                        $em->flush();
                        $status = 6;
                    }
                    else $status = 5;
                }
                else $status = 5;
            }
        }
        elseif (intval($releve->getPasImage()) != 0) //2: piece manquante
        {
            $status = 10;
        }
        elseif ($releve->getPasCle() != 0) $status = 8;

        return $status;
    }


    public function getStatus_5(Releve $releve)
    {
        /** @var ReleveDetail[] $status */
        $status = $this->createQueryBuilder('rd')
            ->leftJoin('rd.releve','r')
            ->leftJoin('r.releve','rm')
            ->where('r.releve = :releve')
            ->setParameter('releve',$releve)
            ->getQuery()
            ->getResult();

        $tiersExist = false;
        foreach ($status as $statu) if (is_null($statu->getCompteTiers2())) $tiersExist = true;

        $engagementTresorerie = $tiersExist ? 0 : 1;
        $image = $status->getReleve()->getImage();

        $em = $this->getEntityManager();
        $imageFlague = new ImageFlague();
        $imageFlague->setDateCreation(new \DateTime());

        $em->persist($imageFlague);
        $em->flush();

        $releve
            ->setEngagementTresorerie($engagementTresorerie)
            ->setFlaguer(5)
            ->setImageFlague($imageFlague);

        $image->setImageFlague($imageFlague);
        $em->flush();

        return 1;
    }

    /**
     * @param Releve $releve
     * @return mixed
     */
    public function getOneReleveDetail(Releve $releve)
    {
        return $this->createQueryBuilder('rd')
            ->where('rd.releve = :releve')
            ->setParameter('releve',$releve)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}