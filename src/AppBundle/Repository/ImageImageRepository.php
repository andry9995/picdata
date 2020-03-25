<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 28/03/2018
 * Time: 13:49
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Image;
use Doctrine\ORM\EntityRepository;

class ImageImageRepository extends EntityRepository
{
    public function getImageImageByImage(Image $image, $type = null){
        $qb = $this->createQueryBuilder('ii')
            ->where('ii.image = :image')
            ->setParameter('image' , $image);

        if($type !== null){
            $qb->andWhere('ii.imageType = :type')
                ->setParameter('type', $type);

            if ($type === 3) {
                $qb->orWhere('ii.imageAutre = :autre')
                    ->setParameter('autre', $image);
            }

        }

        return $qb->getQuery()->getResult();
    }

}