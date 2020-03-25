<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 13/06/2017
 * Time: 10:49
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\ClientTheme;
use Doctrine\ORM\EntityRepository;

class ClientThemeRepository extends EntityRepository
{
    public function getCssTheme(Client $client)
    {
        /** @var ClientTheme[] $theme */
        $theme = $this->getEntityManager()
            ->getRepository('AppBundle:ClientTheme')
            ->createQueryBuilder('client_theme')
            ->select('client_theme')
            ->innerJoin('client_theme.client', 'client')
            ->where('client = :client')
            ->andWhere('client_theme.theme IS NOT NULL')
            ->setParameters(array(
                'client' => $client,
            ))
            ->getQuery()
            ->getResult();

        if (count($theme) > 0) {
            if ($theme[0]->getTheme()) {
                return $theme[0]->getTheme();
            }
        }
        return null;
    }

    public function getColorTheme(Client $client){
        $themes = $this->createQueryBuilder('t')
            ->where('t.client = :client')
            ->setParameter('client', $client)
            ->getQuery()
            ->getResult();

        /**@var $themes ClientTheme[] */
        if(count($themes) > 0){
            return [
                'primarycolor' => $themes[0]->getPrimaryColor(),
                'secondarycolor' => $themes[0]->getSecondaryColor()
                ];
        }

        return [
            'primarycolor' => '#002b7f',
            'secondarycolor' => '#f9f9f9'
        ];
    }
}