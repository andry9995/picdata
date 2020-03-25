<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 10/10/2017
 * Time: 14:17
 */

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;


class RegimeFiscalRepository extends EntityRepository
{
    /**
     * @return null|object
     */
    public function getDefault()
    {
        return $this->findOneBy(array('code' => 'CODE_BIC_IS'));
    }
}