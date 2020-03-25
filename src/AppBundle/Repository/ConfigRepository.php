<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 09/01/2018
 * Time: 16:06
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ConfigRepository extends EntityRepository
{
    private function getConfigLike($like = '')
    {
        $configs = $this->getEntityManager()
            ->getRepository('AppBundle:Config')
            ->createQueryBuilder('config')
            ->select('config')
            ->where('config.code LIKE :config')
            ->andWhere('config.value IS NOT NULL')
            ->andWhere("config.value != '' ")
            ->setParameters(array(
                'config' => $like,
            ))
            ->orderBy('config.code')
            ->getQuery()
            ->getResult();
        return $configs;
    }

    public function getEmailAccuseReception()
    {
        $emails = [];
        $configs = $this->getConfigLike("MAIL_CONF_REC%");

        /** @var \AppBundle\Entity\Config $config */
        foreach ($configs as $config) {
            $emails[] = $config->getValue();
        }

        return $emails;
    }

    public function getEmailNotificationStopSaisie()
    {
        $emails = [];
        $configs = $this->getConfigLike("MAIL_CONF_STOP_SAISIE%");

        /** @var \AppBundle\Entity\Config $config */
        foreach ($configs as $config) {
            $emails[] = $config->getValue();
        }

        return $emails;
    }
}