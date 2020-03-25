<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 19/06/2017
 * Time: 09:24
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\TbimageDossierStatus;
use \Doctrine\ORM\EntityRepository;

class TbimageDossierStatusRepository extends EntityRepository
{
    public function getByClient(Client $client, $exercice)
    {
        $status = $this->getEntityManager()
            ->getRepository('AppBundle:TbimageDossierStatus')
            ->createQueryBuilder('dossier_status')
            ->select('dossier_status')
            ->where('dossier_status.exercice = :exercice')
            ->innerJoin('dossier_status.dossier', 'dossier')
            ->addSelect('dossier')
            ->innerJoin('dossier.site', 'site')
            ->addSelect('site')
            ->innerJoin('site.client', 'client')
            ->addSelect('client')
            ->andWhere('client = :client')
            ->setParameters(array(
                'client' => $client,
                'exercice' => $exercice,
            ))
            ->getQuery()
            ->getResult();
        $data = [];
        /** @var TbimageDossierStatus $item */
        foreach ($status as $item) {
            $data[$item->getDossier()->getId()] = $item->getStatus();
        }
        return $data;
    }
}