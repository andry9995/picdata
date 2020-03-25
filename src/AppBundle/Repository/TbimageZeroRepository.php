<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 29/05/2017
 * Time: 09:46
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\TbimageZero;
use Doctrine\ORM\EntityRepository;

class TbimageZeroRepository extends EntityRepository
{
    public function getForClient(Client $client, $exercice, &$banque_zero)
    {
        $dossiers = $this->getEntityManager()
            ->getRepository('AppBundle:Dossier')
            ->createQueryBuilder('dossier')
            ->select('dossier')
            ->innerJoin('dossier.site', 'site')
            ->innerJoin('site.client', 'client')
            ->where('client = :client')
            ->setParameters(array(
                'client' => $client
            ))
            ->orderBy('dossier.id')
            ->getQuery()
            ->getResult();
        $data = [];
        /** @var Dossier $dossier */
        foreach ($dossiers as $dossier) {
            $tbimagePeriode = $dossier->getTbimagePeriode();

            $demarrage = null;
            $premiere_cloture = null;
            if ($tbimagePeriode) {
                $demarrage = $tbimagePeriode->getDemarrage();
                $premiere_cloture = $tbimagePeriode->getPremiereCloture();
            }

            if ($dossier->getDebutActivite()) {
                $demarrage = clone $dossier->getDebutActivite();
            }

            if ($dossier->getDateCloture()) {
                $premiere_cloture = clone $dossier->getDateCloture();
            }

            if ($dossier->getCloture() == 12 || $dossier->getCloture() == null) {
                $debut = \DateTime::createFromFormat('Y-m-d', $exercice . '-01-01');
            } else {
                $annee = intval($exercice) - 1;
                $mois = intval($dossier->getCloture() + 1);
                $debut = \DateTime::createFromFormat('Y-m-d', $annee . '-' . $mois . '-01');
            }

            if ($demarrage && $premiere_cloture && $premiere_cloture->format('Y') == intval($exercice)) {
                if ($this->getEntityManager()
                    ->getRepository('AppBundle:Tbimage')
                    ->isNbMoisExerciceOk(clone $demarrage, clone $premiere_cloture)) {
                    $debut = \DateTime::createFromFormat('Y-m-d', $demarrage->format('Y-m-01'));
                }
            }

            $data[$dossier->getId()]['debut'] = new \DateTime($debut->format('Y-m-01'));
        }
        $listes = $this->getEntityManager()
            ->getRepository('AppBundle:TbimageZero')
            ->createQueryBuilder('tbimageZero')
            ->select('tbimageZero')
            ->where('tbimageZero.exercice = :exercice')
            ->innerJoin('tbimageZero.dossier', 'dossier')
            ->innerJoin('dossier.site', 'site')
            ->innerJoin('site.client', 'client')
            ->andWhere('client = :client')
            ->setParameters(array(
                'client' => $client,
                'exercice' => $exercice,
            ))
            ->orderBy('dossier.id')
            ->getQuery()
            ->getResult();

        $categ_zero = [];
        $banque_zero = [];

        /** @var TbimageZero $item */
        foreach ($listes as $item) {
            $dossier_id = $item->getDossier()->getId();
            $categorie_id = $item->getCategorie()->getId();

            $start = $data[$dossier_id]['debut'];
            $diff = $this->getEntityManager()
                ->getRepository('AppBundle:Tbimage')
                ->diffInMonth(clone $start, clone $item->getMois());

            if ($item->getCategorie()->getCode() == 'CODE_BANQUE') {
                if ($item->getBanqueCompte()) {
                    $banque_id = $item->getBanqueCompte()->getId();
                    $banque_zero[$dossier_id][$categorie_id][$banque_id][$diff] = 0;
                }
            } else {
                $categ_zero[$dossier_id][$categorie_id][$diff] = 0;
            }
        }

        return $categ_zero;
    }
}