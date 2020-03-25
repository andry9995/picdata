<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 19/06/2017
 * Time: 16:59
 */

namespace AppBundle\Repository;

use AppBundle\Entity\EtatError;
use AppBundle\Entity\EtatErrorCell;
use Doctrine\ORM\EntityRepository;


class EtatErrorCellRepository extends EntityRepository
{
    /**
     * @param EtatError $etatError
     * @param $row
     * @param $col
     * @return EtatErrorCell
     */
    public function newEtatErrorCell(EtatError $etatError,$row,$col)
    {
        $em = $this->getEntityManager();
        $etatErrorCell = new EtatErrorCell();
        $etatErrorCell->setCol($col);
        $etatErrorCell->setRow($row);
        $etatErrorCell->setEtatError($etatError);

        $em->persist($etatErrorCell);
        $em->flush();
        return $etatErrorCell;
    }
}