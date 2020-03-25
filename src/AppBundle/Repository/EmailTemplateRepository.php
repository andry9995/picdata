<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 22/12/2017
 * Time: 13:31
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;

class EmailTemplateRepository extends EntityRepository
{
    public function getByCode($code)
    {
        try {
            $template = $this->getEntityManager()
                ->getRepository('AppBundle:EmailTemplate')
                ->createQueryBuilder('et')
                ->select('et')
                ->where('et.code = :code')
                ->setParameters([
                    'code' => $code,
                ])
                ->getQuery()
                ->getOneOrNullResult();
            return $template;
        } catch (NonUniqueResultException $nure)
        {
            $templates = $this->getEntityManager()
                ->getRepository('AppBundle:EmailTemplate')
                ->createQueryBuilder('et')
                ->select('et')
                ->where('et.code = :code')
                ->setParameters([
                    'code' => $code,
                ])
                ->getQuery()
                ->getResult();
            $i = 0;
            $em = $this->getEntityManager();
            /** @var \AppBundle\Entity\EmailTemplate $template */
            foreach ($templates as $template) {
                if ($i > 0) {
                    $em->remove($template);
                    try {
                        $em->flush();
                    } catch (OptimisticLockException $ole) {

                    }
                }
                $i++;
            }
            return $this->getByCode($code);
        }
    }
}