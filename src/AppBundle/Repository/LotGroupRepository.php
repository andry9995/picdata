<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 23/05/2017
 * Time: 15:52
 */

namespace AppBundle\Repository;

use AppBundle\Entity\LotGroup;
use Doctrine\ORM\EntityRepository;


class LotGroupRepository extends EntityRepository
{
    /**
     * @param int $status
     * @param null $utilisateur
     * @param null $dossier
     * @return LotGroup
     */
    public function getNewLotGroup($status = 0,$utilisateur = null,$dossier = null)
    {
        $new = new LotGroup();
        $em = $this->getEntityManager();
        $new->setStatus($status);
        if($utilisateur != null) $new->setUtilisateur($utilisateur);
        if($dossier != null) $new->setDossier($dossier);
        $em->persist($new);
        $em->flush();
        return $new;
    }
}