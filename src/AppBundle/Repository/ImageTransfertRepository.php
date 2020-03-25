<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 14/06/2017
 * Time: 14:53
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageTransfert;
use Doctrine\ORM\EntityRepository;

class ImageTransfertRepository extends EntityRepository
{
    public function imageIsInTransfert(Image $image)
    {
        $imageTransfert = $this->createQueryBuilder('it')
            ->where('it.image = :image')
            ->andWhere('it.lot IS NULL')
            ->setParameter('image',$image);
    }

    /**
     * @param Client $client
     * @param bool $returnImage
     * @return Image[]|ImageTransfert[]
     */
    public function imagesInTransferts(Client $client, $returnImage = false)
    {
        /** @var ImageTransfert[] $imagesInTranferts */
        $imagesInTranferts = $this->createQueryBuilder('it')
            ->join('it.image','i')
            ->join('i.lot','l')
            ->join('l.dossier','d')
            ->join('d.site','s')
            ->where('it.lot IS NULL')
            ->andWhere('s.client = :client')
            ->setParameters([
                'client' => $client
            ])
            ->getQuery()
            ->getResult();

        if (!$returnImage) return $imagesInTranferts;

        /** @var Image[] $images */
        $images = [];
        foreach ($imagesInTranferts as $imagesInTranfert)
        {
            $images[] = $imagesInTranfert->getImage();
        }

        return $images;
    }
}