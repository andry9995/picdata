<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Banque;
use AppBundle\Entity\Dossier;
use Doctrine\ORM\EntityRepository;

class BanqueRepository extends EntityRepository
{
    /**
     * @return Banque[]
     */
    public function getAll()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.codebanque')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $code
     * @return Banque
     */
    public function getOneByCode($code)
    {
        return $this->createQueryBuilder('b')
            ->where('b.codebanque = :code')
            ->andWhere('b.nom <> :nom')
            ->setParameters([
                'code' => $code,
                'nom' => ''
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getListeBanqueCompteByDossierId($dossier)
    {

    }
}
?>