<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 10/07/2019
 * Time: 14:19
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Image;
use AppBundle\Entity\ImageComment;
use Doctrine\ORM\EntityRepository;

class ImageCommentRepository extends EntityRepository
{
    /**
     * @param Image $image
     * @return ImageComment
     */
    public function getByImage(Image $image)
    {
        return $this->createQueryBuilder('ic')
            ->where('ic.image = :image')
            ->setParameter('image',$image)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}