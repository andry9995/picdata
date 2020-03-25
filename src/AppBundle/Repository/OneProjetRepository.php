<?php

/**
 * Created by Netbeans
 * Created on : 18 juil. 2017, 15:32:29
 * Author : Mamy Rakotonirina
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OneProjetRepository extends EntityRepository
{
    /**
     * Récupération des projets
     * @return type
     */
    public function getProjets($sort='echeance', $sortOrder='ASC', $q='', $period='all', $startperiod='', $endperiod='') {
        $qb = $this->createQueryBuilder('projet');
        
        //Recherche mot clé
        if ($q != '') {
            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('projet.nom', ':q'),
                    $qb->expr()->like('projet.description', ':q')
                    ))
                    ->setParameter(':q', '%'.$q.'%');
        }
        
        //Période
        if ($period !== 'all') {
            if ($startperiod != '' && $endperiod != '') {
                $qb->andWhere($qb->expr()->gte('CAST(projet.creeLe AS DATE)', ':startperiod'));
                $qb->andWhere($qb->expr()->lte('CAST(projet.creeLe AS DATE)', ':endperiod'));
                $qb->setParameter(':startperiod', \DateTime::createFromFormat('d/m/Y', $startperiod)->format('Y-m-d'));
                $qb->setParameter(':endperiod', \DateTime::createFromFormat('d/m/Y', $endperiod)->format('Y-m-d'));
            }
        }
        
        //Tri
        if ($sort === 'datecreation') {
            $qb->orderBy('projet.creeLe', $sortOrder);
        } else {
            $qb->orderBy('projet.nom', $sortOrder);
        }

        return $qb->getQuery()
                ->getResult();
    }
}