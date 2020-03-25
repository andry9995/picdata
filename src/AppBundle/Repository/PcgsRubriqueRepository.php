<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 22/03/2017
 * Time: 11:21
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Pcg;
use AppBundle\Entity\Rubrique;
use Doctrine\ORM\EntityRepository;

class PcgsRubriqueRepository extends EntityRepository
{
    /**
     * @param Rubrique $rubrique
     * @param bool $pcg
     * @return array
     */
    public function getPcgs(Rubrique $rubrique,$pcg = true)
    {
        $pcgs = $this->createQueryBuilder('pr')
            ->leftJoin('pr.pcg','pcg')
            ->where('pr.rubrique = :rubrique')
            ->setParameter('rubrique',$rubrique)
            ->orderBy('pcg.compte')
            ->getQuery()
            ->getResult();
        if(!$pcg) return $pcgs;

        $results = [];
        foreach ($pcgs as $pcg) $results[] = $pcg->getPcg();
        return $results;
    }

    /**
     * @param Rubrique $rubrique
     * @param Pcg $pcg
     * @return mixed
     */
    public function getByRubriqueCompte(Rubrique $rubrique,Pcg $pcg)
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.rubrique = :rubrique')
            ->setParameter('rubrique',$rubrique)
            ->andWhere('pr.pcg = :pcg')
            ->setParameter('pcg',$pcg)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function getCaracteres()
    {
        //0:solde ; 1:solde debit ; 2:solde credit ; 3:debit ; 4:credit
        //0 : compte collectif; 5 => 1 : compte auxilliare ; 6=> 2 : factures non payes
        return array('E'=>1, 'F'=>2, 'D'=>3, 'C'=>4, 'X'=>5, 'N'=>6);
    }
}