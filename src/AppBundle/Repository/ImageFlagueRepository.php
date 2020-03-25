<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 30/07/2018
 * Time: 09:33
 */

namespace AppBundle\Repository;

use AppBundle\Controller\Boost;
use AppBundle\Entity\BanqueSousCategorieAutre;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageFlague;
use AppBundle\Entity\ImputationControle;
use AppBundle\Entity\Releve;
use AppBundle\Entity\Separation;
use AppBundle\Entity\TvaImputationControle;
use Doctrine\ORM\EntityRepository;

class ImageFlagueRepository extends EntityRepository
{
    /**
     * @param ImageFlague $imageFlague
     * @param Releve|null $releve
     * @param TvaImputationControle|null $tvaImputationControle
     * @param BanqueSousCategorieAutre|null $banqueSousCategorieAutre
     * @param bool $object
     * @param bool $withLibelle
     * @return object
     */
    public function getSoeurs(ImageFlague $imageFlague, Releve $releve = null, TvaImputationControle $tvaImputationControle = null,BanqueSousCategorieAutre $banqueSousCategorieAutre = null,$object = false,$withLibelle = false)
    {
        /*
         * si releve not null ne pas retourne $releve
         * si image not null ne pas retourne $image
         *
         * si non retourne toutes les releves et images
         */
        $tvaImputationControles = $this->getEntityManager()->getRepository('AppBundle:TvaImputationControle')
            ->getChildImageFlagues($imageFlague,$tvaImputationControle);

        $releves = $this->getEntityManager()->getRepository('AppBundle:Releve')
            ->getChildImageFlagues($imageFlague,$releve);

        $banquesSousCategorieAutres = $this->getEntityManager()->getRepository('AppBundle:BanqueSousCategorieAutre')
            ->getChildImageFlagues($imageFlague,$banqueSousCategorieAutre);

        $resTvaImputations = [];
        foreach ($tvaImputationControles as $tic)
        {
            $key = $tic->getImage()->getId() . '';
            if (!array_key_exists($key,$resTvaImputations)) $resTvaImputations[$key] = [];
            $resTvaImputations[$key][] = $tic;
        }
        $resReleves = [];
        foreach ($releves as $rel)
        {
            $key = $rel->getImage()->getId() . '';
            if (!array_key_exists($key,$resReleves)) $resReleves[$key] = [];
            $resReleves[$key][] = $rel;
        }
        $resBanquesAutres = [];
        foreach ($banquesSousCategorieAutres as $bsca)
        {
            $key = $bsca->getImage()->getId() . '';
            if (!array_key_exists($key,$resBanquesAutres)) $resBanquesAutres[$key] = [];
            $resBanquesAutres[$key][] = $bsca;
        }

        return (object)
        [
            'tic' => $resTvaImputations,
            'rel' => $resReleves,
            'bsca' => $resBanquesAutres
        ];
    }

    public function departagerLettrage(ImageFlague $imageFlague)
    {
        $items = $this->getSoeurs($imageFlague);
        $imagesTics = $items->tic;
        $imagesRels = $items->rel;
        $imagesBscas = $items->bsca;

        $imagesChangers = [];
        $em = $this->getEntityManager();

        foreach ($imagesRels as $imagesRel)
        {
            foreach ($imagesRel as $item)
            {
                /** @var Releve $releve */
                $releve = $item;
                $montant = Round($releve->getDebit() - $releve->getCredit(),2);

                $trouve = false;
                foreach ($imagesTics as $keyImage => $imagesTic)
                {
                    if (in_array($keyImage,$imagesChangers)) continue;

                    /** @var Image $image */
                    $image = $this->getEntityManager()->getRepository('AppBundle:Image')
                        ->find($keyImage);
                    /** @var Separation $separation */
                    $separation = $this->getEntityManager()->getRepository('AppBundle:Separation')
                        ->getSeparationByImage($image);
                    /** @var ImputationControle $imputationControle */
                    $imputationControle = $this->getEntityManager()->getRepository('AppBundle:ImputationControle')
                        ->getByImage($image);

                    $montantPiece = 0;
                    foreach ($imagesTic as $tic)
                    {
                        /** @var TvaImputationControle $tvaImputationControle */
                        $tvaImputationControle = $tic;

                        $m = abs(round($tvaImputationControle->getMontantTtc(),2));
                        if ($separation && $imputationControle)
                            if (in_array($separation->getCategorie()->getId(),[10,12]) &&
                                ((!$imputationControle->getTypePiece() || $imputationControle->getTypePiece() && $imputationControle->getTypePiece()->getId() != 1))
                            ) $m *= -1;

                            elseif (in_array($separation->getCategorie()->getId(),[9,13]) &&
                                ($imputationControle->getTypePiece() && $imputationControle->getTypePiece()->getId() == 1)
                            ) $m *= -1;

                        $montantPiece += $m;
                    }
                    $montantPiece = round($montantPiece,2);

                    if (floatval($montant) == -floatval($montantPiece))
                    {
                        $trouve = true;
                        $imageFlagueNew = new ImageFlague();
                        $imageFlagueNew
                            ->setPcc($imageFlague->getPcc())
                            ->setTiers($imageFlague->getTiers())
                            ->setDateCreation($imageFlague->getDateCreation())
                            ->setDateDevalidation($imageFlague->getDateDevalidation())
                            ->setLettre($imageFlague->getLettre());

                        $em->persist($imageFlagueNew);
                        $em->flush();
                        foreach ($imagesTic as $tic)
                        {
                            /** @var TvaImputationControle $tvaImputationControle */
                            $tvaImputationControle = $tic;
                            $tvaImputationControle->setImageFlague($imageFlagueNew);
                        }

                        $releve->setImageFlague($imageFlagueNew);
                        $em->flush();

                        $imagesChangers[] = $keyImage;
                    }

                    if ($trouve) break;
                }
            }
        }
    }
}